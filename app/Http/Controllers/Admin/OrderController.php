<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for Admin.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'transactions'])->latest();

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by keyword (Order Code, ID, Name, Phone, GHN Code)
        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('ghn_order_code', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15);

        // Summary Counters
        $totalOrdersCount = Order::count();
        $paidOrdersCount = Order::where('status', 'paid')->count();
        $codOrdersCount = Order::where('payment_method', 'cod')->count();
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $cancelledOrdersCount = Order::where('status', 'cancelled')->count();
        $totalRevenue = Order::whereIn('status', ['paid', 'cod_ordered', 'completed'])->sum('total_price');

        return view('admin.orders.index', compact(
            'orders',
            'totalOrdersCount',
            'paidOrdersCount',
            'codOrdersCount',
            'pendingOrdersCount',
            'cancelledOrdersCount',
            'totalRevenue'
        ));
    }

    /**
     * Display the specified order details for Admin.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'transactions']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status & shipping status, syncing payment transactions automatically.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,cod_ordered,cod_paid,failed,cancelled,completed',
            'shipping_status' => 'required|string|in:pending,ready_to_pick,delivering,delivered,cancelled',
        ]);

        DB::transaction(function () use ($request, $order) {
            $order->update([
                'status' => $request->status,
                'shipping_status' => $request->shipping_status,
            ]);

            // Synchronize with payment_transactions table (Order is primary source of truth)
            $targetPaymentStatus = match ($request->status) {
                'paid', 'completed', 'cod_paid' => 'paid',
                'failed' => 'failed',
                'cancelled' => 'cancelled',
                default => 'pending', // 'pending', 'cod_ordered'
            };

            $latestPayment = $order->paymentTransactions()->latest('id')->first();
            $adminName = auth()->check() ? 'Admin #' . auth()->id() : 'Quản trị viên';

            if ($latestPayment) {
                $updateData = [
                    'status' => $targetPaymentStatus,
                    'message' => "Đồng bộ từ cập nhật Đơn hàng #{$order->id} ({$request->status}) bởi {$adminName}",
                ];

                if ($targetPaymentStatus === 'paid') {
                    $updateData['paid_at'] = $latestPayment->paid_at ?? now();
                } elseif ($targetPaymentStatus === 'pending') {
                    $updateData['paid_at'] = null;
                }

                $latestPayment->update($updateData);
            } else {
                $order->paymentTransactions()->create([
                    'gateway' => $order->payment_method ?: 'cod',
                    'amount' => $order->total_price,
                    'status' => $targetPaymentStatus,
                    'paid_at' => ($targetPaymentStatus === 'paid') ? now() : null,
                    'message' => "Tự động tạo giao dịch khi cập nhật Đơn hàng #{$order->id} ({$request->status}) bởi {$adminName}",
                ]);
            }
        });

        return redirect()->back()->with('success', "Đã cập nhật trạng thái Đơn Hàng #{$order->id} và tự động đồng bộ giao dịch thanh toán thành công!");
    }

    /**
     * Sync shipping status live from GHN API.
     */
    public function syncGhn(Order $order, \App\Services\GHNService $ghnService)
    {
        if (!$order->ghn_order_code) {
            return redirect()->back()->with('error', 'Đơn hàng chưa có Mã Vận Đơn GHN để đồng bộ.');
        }

        $result = $ghnService->getOrderDetail($order->ghn_order_code);

        if ($result['success']) {
            $ghnStatus = $result['status'] ?? 'ready_to_pick';
            
            // Map GHN status codes to internal status
            $mappedStatus = match ($ghnStatus) {
                'ready_to_pick', 'picking' => 'ready_to_pick',
                'storing', 'transporting', 'delivering' => 'delivering',
                'delivered' => 'delivered',
                'cancel', 'return' => 'cancelled',
                default => 'ready_to_pick',
            };

            $order->update([
                'shipping_status' => $mappedStatus,
            ]);

            return redirect()->back()->with('success', "⚡ Đã đồng bộ thành công trạng thái GHN: '{$ghnStatus}' -> Trạng thái hệ thống: '{$mappedStatus}'!");
        }

        return redirect()->back()->with('error', "Không thể đồng bộ với GHN: " . ($result['message'] ?? 'Lỗi không xác định'));
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

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
     * Update order status & shipping status.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,cod_ordered,failed,cancelled,completed',
            'shipping_status' => 'required|string|in:pending,ready_to_pick,delivering,delivered,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
            'shipping_status' => $request->shipping_status,
        ]);

        return redirect()->back()->with('success', "Đã cập nhật trạng thái Đơn Hàng #{$order->id} thành công!");
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


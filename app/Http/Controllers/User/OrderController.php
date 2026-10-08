<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Services\GHNOrderService;
use App\Services\GHNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display listing of user orders (Order History).
     */
    public function index()
    {
        $user = Auth::user();
        $orders = Order::with(['items.product', 'transactions'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('user.orders.index', compact('orders'));
    }

    /**
     * Display order details.
     */
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id() && (!Auth::user() || !Auth::user()->isAdmin())) {
            abort(403);
        }

        $order->load(['items.product', 'transactions']);
        return view('user.orders.show', compact('order'));
    }

    /**
     * Process payment & create order (COD or MoMo).
     */
    public function processPayment(Request $request, GHNService $ghn, GHNOrderService $ghnOrders)
    {
        $request->validate([
            'fullname' => 'required|string|max:100',
            'phone' => ['required', 'regex:/^0\d{9,10}$/'],
            'address' => 'required|string|max:500',
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
            'payment_method' => 'required|in:cod,momo',
        ], [
            'fullname.required' => 'Vui lòng nhập Họ và tên người nhận.',
            'phone.required' => 'Vui lòng nhập Số điện thoại hợp lệ.',
            'phone.regex' => 'Số điện thoại không đúng định dạng (Ví dụ: 0987654321).',
            'address.required' => 'Vui lòng nhập Địa chỉ nhận hàng.',
            'to_district_id.required' => 'Vui lòng chọn Tỉnh / Quận / Huyện.',
            'to_ward_code.required' => 'Vui lòng chọn Phường / Xã.',
            'payment_method.required' => 'Vui lòng chọn Hình thức thanh toán.',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Không thể thanh toán vì giỏ hàng đang trống.');
        }

        // Sync live prices from DB before processing order
        $productIds = array_keys($cart);
        $dbProducts = Product::with('variants')->whereIn('id', $productIds)->get()->keyBy('id');
        foreach ($cart as $id => &$cartItem) {
            if (isset($dbProducts[$id])) {
                $p = $dbProducts[$id];
                $cartItem['price'] = $p->variants->count() > 0 
                    ? (float) ($p->variants->map(fn($v) => $v->effective_price)->min() ?? $p->original_price) 
                    : (float) ($p->original_price ?? 0);
            }
        }
        session()->put('cart', $cart);

        // Selected items filter if user specified selected items
        $selectedIds = $request->has('selected_items') && !empty($request->input('selected_items'))
            ? explode(',', $request->input('selected_items'))
            : array_keys($cart);

        $selectedCart = array_filter($cart, function ($key) use ($selectedIds) {
            return in_array((string) $key, $selectedIds);
        }, ARRAY_FILTER_USE_KEY);

        if (empty($selectedCart)) {
            $selectedCart = $cart;
        }

        // Kiểm tra tồn kho thực tế trước khi tính toán & tạo đơn
        foreach ($selectedCart as $item) {
            $variantId = $item['variant_id'] ?? null;
            $productId = $item['product_id'] ?? $item['id'];
            $requestedQty = (int) ($item['quantity'] ?? 1);
            $itemName = $item['name'] . (!empty($item['variant_name']) ? " ({$item['variant_name']})" : '');

            if ($variantId) {
                $var = \App\Models\ProductVariant::find($variantId);
                if (!$var || $var->stock <= 0) {
                    return redirect()->route('cart.index')->with('error', "Sản phẩm \"{$itemName}\" hiện đã hết hàng, vui lòng bỏ chọn hoặc xóa khỏi giỏ hàng!");
                }
                if ($requestedQty > $var->stock) {
                    return redirect()->route('cart.index')->with('error', "Sản phẩm \"{$itemName}\" chỉ còn {$var->stock} món trong kho!");
                }
            } else {
                $prod = Product::with('variants')->find($productId);
                if (!$prod || $prod->total_stock <= 0) {
                    return redirect()->route('cart.index')->with('error', "Sản phẩm \"{$itemName}\" hiện đã hết hàng, vui lòng bỏ chọn hoặc xóa khỏi giỏ hàng!");
                }
                if ($requestedQty > $prod->total_stock) {
                    return redirect()->route('cart.index')->with('error', "Sản phẩm \"{$itemName}\" chỉ còn {$prod->total_stock} món trong kho!");
                }
            }
        }

        // 1. Calculate subtotal
        $subtotal = 0;
        foreach ($selectedCart as $item) {
            $subtotal += (float) $item['price'] * (int) $item['quantity'];
        }

        // 2. Calculate dynamic shipping fee from GHN API
        $districtId = (int) $request->to_district_id;
        $wardCode = (string) $request->to_ward_code;
        $feeResponse = $ghn->calculateFee($districtId, $wardCode, 500);

        $shippingFee = (isset($feeResponse['success']) && $feeResponse['success'] && isset($feeResponse['total_fee']))
            ? (float) $feeResponse['total_fee']
            : 30000;

        $finalTotal = $subtotal + $shippingFee;

        // 3. Create Order & OrderItems in DB Transaction
        $order = DB::transaction(function () use ($request, $subtotal, $shippingFee, $finalTotal, $selectedCart, $districtId, $wardCode) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'name' => $request->fullname,
                'phone' => $request->phone,
                'email' => $request->email ?? (Auth::user() ? Auth::user()->email : null),
                'address' => $request->address,
                'to_district_id' => $districtId,
                'to_ward_code' => $wardCode,
                'subtotal' => $subtotal,
                'ghn_total_fee' => $shippingFee,
                'total_price' => $finalTotal,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'shipping_status' => 'pending',
            ]);

            foreach ($selectedCart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? $item['id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'variant_name' => $item['variant_name'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                if (!empty($item['variant_id'])) {
                    \App\Models\ProductVariant::where('id', $item['variant_id'])->decrement('stock', (int) $item['quantity']);
                }
            }

            return $order;
        });

        // Clear paid items from cart session
        foreach ($selectedCart as $id => $item) {
            if (isset($cart[$id])) {
                unset($cart[$id]);
            }
        }
        session()->put('cart', $cart);

        // 4. Payment Branching
        if ($request->payment_method === 'momo') {
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway' => 'momo',
                'amount' => $order->total_price,
                'status' => 'pending',
            ]);

            return redirect()->route('user.orders.momo.start', $order);
        }

        // --- COD Branch: Create GHN Order Immediately ---
        PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => 'cod',
            'amount' => $order->total_price,
            'status' => 'pending',
            'message' => 'Thanh toán tiền mặt khi nhận hàng (COD)',
        ]);

        $order->load('items.product');
        $ghnOrderResponse = $ghnOrders->create($order);

        if (($ghnOrderResponse['code'] ?? null) == 200 && !empty($ghnOrderResponse['data']['order_code'])) {
            $order->update([
                'status' => 'cod_ordered',
                'ghn_order_code' => $ghnOrderResponse['data']['order_code'],
                'shipping_status' => 'ready_to_pick',
            ]);

            return redirect()->route('user.orders.index')
                ->with('success', 'Đặt hàng COD thành công! Mã đơn hàng của bạn: ' . $order->order_code);
        }

        Log::error('GHN COD Order Failed: ', $ghnOrderResponse ?? []);
        $order->update(['status' => 'cod_ordered']);

        return redirect()->route('user.orders.index')
            ->with('warning', 'Đặt hàng thành công nhưng chưa thể tạo vận đơn GHN tự động.');
    }

    /**
     * Cancel an order by user.
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Check cancellation condition: cannot cancel if order is already delivering or delivered
        if (in_array($order->shipping_status, ['delivering', 'delivered'])) {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng vì hàng đã được giao cho shipper GHN vận chuyển. Vui lòng liên hệ Hotline shop để hỗ trợ!');
        }

        if ($order->status === 'cancelled') {
            return redirect()->back()->with('warning', 'Đơn hàng này đã được hủy trước đó.');
        }

        $order->update([
            'status' => 'cancelled',
            'shipping_status' => 'cancelled',
        ]);

        return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công!');
    }
}


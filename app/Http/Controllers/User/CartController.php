<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\GHNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Synchronize session cart item prices and details with current database values.
     */
    private function syncCartPrices(array &$cart): array
    {
        if (empty($cart)) {
            return $cart;
        }

        $productIds = [];
        $variantIds = [];
        foreach ($cart as $key => $item) {
            $pId = $item['product_id'] ?? $item['id'] ?? null;
            if ($pId) $productIds[] = $pId;
            if (!empty($item['variant_id'])) $variantIds[] = $item['variant_id'];
        }

        $products = Product::with('variants.category')->whereIn('id', array_unique($productIds))->get()->keyBy('id');
        $variants = ProductVariant::with('category')->whereIn('id', array_unique($variantIds))->get()->keyBy('id');

        $updated = false;
        foreach ($cart as $key => &$item) {
            $pId = $item['product_id'] ?? $item['id'] ?? null;
            $vId = $item['variant_id'] ?? null;

            if ($vId && isset($variants[$vId])) {
                $variant = $variants[$vId];
                $product = $products[$variant->product_id] ?? $variant->product;
                $currentPrice = (float) $variant->effective_price;

                if (!isset($item['price']) || (float)$item['price'] !== $currentPrice) {
                    $item['price'] = $currentPrice;
                    $updated = true;
                }
                $item['name'] = $product ? $product->name : $item['name'];
                $item['variant_name'] = $variant->type;
                $item['image'] = $variant->image_url ?: ($product ? $product->first_image_url : null);
                $item['stock'] = $variant->stock;
                $item['category_name'] = $variant->category?->name ?? ($product?->variants->first()?->category?->name ?? 'Mỹ phẩm');
            } elseif ($pId && isset($products[$pId])) {
                $product = $products[$pId];
                $currentPrice = $product->variants->count() > 0
                    ? (float) ($product->variants->map(fn($v) => $v->effective_price)->min() ?? $product->original_price)
                    : (float) ($product->original_price ?? 0);

                if (!isset($item['price']) || (float)$item['price'] !== $currentPrice) {
                    $item['price'] = $currentPrice;
                    $updated = true;
                }
                $item['name'] = $product->name;
                $item['image'] = $product->first_image_url;
                $item['category_name'] = $product->variants->first()?->category?->name ?? 'Mỹ phẩm';
                $item['stock'] = $product->total_stock;
            }
        }

        if ($updated) {
            session()->put('cart', $cart);
        }

        return $cart;
    }

    /**
     * Display the shopping cart page.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $this->syncCartPrices($cart);
        return view('user.cart.index', compact('cart'));
    }

    /**
     * Add a product or variant to the shopping cart via AJAX/Form.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $productId = $request->product_id;
        $variantId = $request->variant_id;
        $quantity = (int) ($request->quantity ?? 1);
        $product = Product::with('variants.category')->findOrFail($productId);

        $variant = null;
        if ($variantId) {
            $variant = $product->variants->firstWhere('id', $variantId);
        } elseif ($product->variants->count() === 1) {
            $variant = $product->variants->first();
        }

        $cartKey = $variant ? "{$productId}_{$variant->id}" : (string) $productId;
        $price = $variant ? (float) $variant->effective_price : (
            $product->variants->count() > 0 
                ? (float) ($product->variants->map(fn($v) => $v->effective_price)->min() ?? $product->original_price) 
                : (float) ($product->original_price ?? 0)
        );

        $image = $variant?->image_url ?: $product->first_image_url;
        $variantName = $variant?->type;
        $categoryName = $variant?->category?->name ?? ($product->variants->first()?->category?->name ?? 'Mỹ phẩm');

        $availableStock = $variant ? (int) $variant->stock : (int) $product->total_stock;
        $itemName = $variantName ? "{$product->name} ({$variantName})" : $product->name;

        if ($availableStock <= 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Sản phẩm \"{$itemName}\" hiện đã hết hàng!",
                ], 422);
            }
            return redirect()->back()->with('error', "Sản phẩm \"{$itemName}\" hiện đã hết hàng!");
        }

        $currentQtyInCart = isset($cart[$cartKey]) ? (int) $cart[$cartKey]['quantity'] : 0;
        if (($currentQtyInCart + $quantity) > $availableStock) {
            $msg = "Số lượng yêu cầu vượt quá số lượng còn lại trong kho (Kho chỉ còn {$availableStock} sản phẩm)!";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
            $cart[$cartKey]['price'] = $price;
            $cart[$cartKey]['stock'] = $availableStock;
        } else {
            $cart[$cartKey] = [
                'id' => $product->id,
                'cart_key' => $cartKey,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'variant_name' => $variantName,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $image,
                'category_name' => $categoryName,
                'stock' => $availableStock,
            ];
        }

        $this->syncCartPrices($cart);
        session()->put('cart', $cart);

        $totalCount = array_sum(array_column($cart, 'quantity'));
        $itemName = $variantName ? "{$product->name} ({$variantName})" : $product->name;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã thêm \"{$itemName}\" vào giỏ hàng thành công!",
                'cart_count' => $totalCount,
                'cart' => $cart,
            ]);
        }

        return redirect()->back()->with('success', "Đã thêm {$itemName} vào giỏ hàng!");
    }

    /**
     * Update cart item quantity via AJAX.
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $cartKey = (string) ($request->cart_key ?? $request->product_id);

        if (isset($cart[$cartKey])) {
            $itemStock = isset($cart[$cartKey]['stock']) ? (int) $cart[$cartKey]['stock'] : 999;
            $requestedQty = (int) $request->quantity;

            if ($requestedQty > $itemStock) {
                return response()->json([
                    'success' => false,
                    'message' => "Số lượng vượt quá số lượng có sẵn trong kho (Còn {$itemStock} sản phẩm)!",
                ], 422);
            }

            $cart[$cartKey]['quantity'] = $requestedQty;
        }

        $this->syncCartPrices($cart);
        session()->put('cart', $cart);

        $totalCount = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'cart_count' => $totalCount,
        ]);
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
        ]);

        $cart = session()->get('cart', []);
        $cartKey = (string) ($request->cart_key ?? $request->product_id);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
        }

        $this->syncCartPrices($cart);
        session()->put('cart', $cart);

        $totalCount = array_sum(array_column($cart, 'quantity'));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                'cart_count' => $totalCount,
                'cart' => $cart,
            ]);
        }

        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    /**
     * Return JSON for cart item count.
     */
    public function count()
    {
        $cart = session()->get('cart', []);
        $this->syncCartPrices($cart);
        $totalCount = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'cart_count' => $totalCount,
        ]);
    }

    /**
     * Display the checkout page.
     */
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        $this->syncCartPrices($cart);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('warning', 'Giỏ hàng của bạn đang trống! Vui lòng chọn sản phẩm trước khi thanh toán.');
        }

        $user = Auth::user();

        // Optional filter if user passed specific selected items from cart
        $selectedIds = $request->has('selected_items') && !empty($request->input('selected_items'))
            ? explode(',', $request->input('selected_items')) 
            : array_keys($cart);

        $selectedCart = array_filter($cart, function($key) use ($selectedIds) {
            return in_array((string)$key, $selectedIds);
        }, ARRAY_FILTER_USE_KEY);

        if (empty($selectedCart)) {
            $selectedCart = $cart;
        }

        $totalAmount = 0;
        foreach ($selectedCart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        return view('user.checkout.index', compact('selectedCart', 'totalAmount', 'user'));
    }

    /**
     * Process checkout form submission and clear paid items from cart.
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,momo',
        ], [
            'fullname.required' => 'Vui lòng nhập Họ và tên người nhận.',
            'phone.required' => 'Vui lòng nhập Số điện thoại nhận hàng.',
            'address.required' => 'Vui lòng nhập Địa chỉ nhận hàng chi tiết.',
            'payment_method.required' => 'Vui lòng chọn Hình thức thanh toán.',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('warning', 'Giỏ hàng của bạn đang trống.');
        }

        // Selected items IDs to remove from cart
        $selectedIds = $request->has('selected_items') && !empty($request->input('selected_items'))
            ? explode(',', $request->input('selected_items'))
            : array_keys($cart);

        // Remove processed/purchased items from session cart
        foreach ($selectedIds as $id) {
            if (isset($cart[$id])) {
                unset($cart[$id]);
            }
        }

        session()->put('cart', $cart);

        $paymentText = $request->payment_method === 'momo' ? 'qua ví MoMo' : 'khi nhận hàng (COD)';

        return redirect()->route('home')->with('success', "Đặt hàng thành công! Đơn hàng thanh toán {$paymentText} của bạn đang được đóng gói và giao đến địa chỉ nhận hàng.");
    }

    /**
     * AJAX: Get Provinces from GHN API.
     */
    public function getGhnProvinces(GHNService $ghnService)
    {
        $provinces = $ghnService->getProvinces();
        return response()->json([
            'success' => true,
            'data' => $provinces,
        ]);
    }

    /**
     * AJAX: Get Districts by Province ID from GHN API.
     */
    public function getGhnDistricts($provinceId, GHNService $ghnService)
    {
        $districts = $ghnService->getDistricts((int) $provinceId);
        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * AJAX: Get Wards by District ID from GHN API.
     */
    public function getGhnWards($districtId, GHNService $ghnService)
    {
        $wards = $ghnService->getWards((int) $districtId);
        return response()->json([
            'success' => true,
            'data' => $wards,
        ]);
    }

    /**
     * AJAX: Calculate shipping fee via GHN API.
     */
    public function calculateGhnFee(Request $request, GHNService $ghnService)
    {
        $request->validate([
            'district_id' => 'required|integer',
            'ward_code' => 'required|string',
            'weight' => 'nullable|integer',
        ]);

        $districtId = (int) $request->district_id;
        $wardCode = (string) $request->ward_code;
        $weight = (int) ($request->weight ?? 500);

        $result = $ghnService->calculateFee($districtId, $wardCode, $weight);

        return response()->json($result);
    }
}

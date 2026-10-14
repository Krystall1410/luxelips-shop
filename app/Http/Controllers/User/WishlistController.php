<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display a listing of the user's favorite products.
     */
    public function index()
    {
        $user = Auth::user();

        $wishlists = Wishlist::where('user_id', $user->id)
            ->with(['product' => function ($query) {
                $query->with(['variants', 'brand'])->where('is_active', true);
            }])
            ->latest()
            ->paginate(12);

        return view('user.wishlist.index', compact('wishlists'));
    }

    /**
     * Toggle a product in/out of the user's wishlist (AJAX friendly).
     */
    public function toggle(Request $request, Product $product)
    {
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'require_auth' => true,
                    'message' => 'Vui lòng đăng nhập để lưu sản phẩm vào danh sách yêu thích.',
                    'redirect' => route('login'),
                ], 200);
            }

            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để lưu sản phẩm vào danh sách yêu thích.');
        }

        $user = Auth::user();
        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $action = 'removed';
            $isFavorited = false;
            $message = 'Đã bỏ ' . $product->name . ' khỏi danh sách yêu thích.';
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            $action = 'added';
            $isFavorited = true;
            $message = 'Đã thêm ' . $product->name . ' vào danh sách yêu thích!';
        }

        $count = Wishlist::where('user_id', $user->id)->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'action' => $action,
                'is_favorited' => $isFavorited,
                'count' => $count,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Remove a product from the wishlist.
     */
    public function destroy(Product $product)
    {
        $user = Auth::user();
        Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi danh sách yêu thích.');
    }

    /**
     * Get the count of favorite products for the current user.
     */
    public function count()
    {
        $count = Auth::check() ? Wishlist::where('user_id', Auth::id())->count() : 0;
        return response()->json(['count' => $count]);
    }
}

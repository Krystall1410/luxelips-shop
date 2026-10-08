<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display user homepage with Cocolux-inspired layout.
     */
    public function index()
    {
        // 1. Featured / Best Seller Products (ordered by total purchased quantity from orders)
        $featuredProducts = Product::where('is_active', true)
            ->with(['variants.category'])
            ->withCount(['orderItems as total_sold' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity), 0)'))
                    ->whereHas('order', function ($q) {
                        $q->whereNotIn('status', ['cancelled', 'failed']);
                    });
            }])
            ->orderByDesc('total_sold')
            ->latest()
            ->take(10)
            ->get();

        // 2. Latest Products (for category tab filter section)
        $latestProducts = Product::where('is_active', true)
            ->with(['variants.category'])
            ->latest()
            ->take(10)
            ->get();

        // 3. Categories with active products count for horizontal mega menu
        $categories = Category::where('is_active', true)
            ->withCount(['variants as products_count' => function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('is_active', true);
                })->select(DB::raw('count(distinct product_id)'));
            }])
            ->get();

        // Backward compatibility variable
        $products = $latestProducts;

        return view('user.index', compact('featuredProducts', 'latestProducts', 'products', 'categories'));
    }

    /**
     * Display public product detail page (Cocolux style with variant switching).
     */
    public function show(string $productKey)
    {
        $product = Product::with(['variants.category', 'brand'])
            ->where('is_active', true)
            ->where(function ($query) use ($productKey) {
                $query->where('slug', $productKey);
                if (is_numeric($productKey)) {
                    $query->orWhere('id', (int) $productKey);
                }
            })
            ->firstOrFail();

        // Related products in same category
        $categoryIds = $product->variants->pluck('category_id')->filter()->unique();
        $relatedProducts = Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->whereHas('variants', function ($q) use ($categoryIds) {
                if ($categoryIds->isNotEmpty()) {
                    $q->whereIn('category_id', $categoryIds);
                }
            })
            ->with('variants.category')
            ->take(4)
            ->get();

        if ($relatedProducts->isEmpty()) {
            $relatedProducts = Product::where('is_active', true)
                ->where('id', '!=', $product->id)
                ->with('variants.category')
                ->latest()
                ->take(4)
                ->get();
        }

        $categories = Category::where('is_active', true)->get();

        return view('user.products.show', compact('product', 'relatedProducts', 'categories'));
    }

    /**
     * Display Products Collection / Search Catalog (Cocolux inspired, filterable).
     */
    public function collection(Request $request)
    {
        $query = Product::where('is_active', true)->with(['variants.category']);

        // 1. Filter by Category
        if ($request->filled('category') && $request->category !== 'all') {
            $catId = $request->category;
            $query->whereHas('variants', function ($q) use ($catId) {
                $q->where('category_id', $catId);
            });
        }

        // 2. Filter by Price Range
        if ($request->filled('min_price')) {
            $minPrice = (float) str_replace(['.', ',', ' '], '', $request->min_price);
            if ($minPrice > 0) {
                $query->where(function ($q) use ($minPrice) {
                    $q->where('original_price', '>=', $minPrice)
                        ->orWhereHas('variants', function ($vq) use ($minPrice) {
                            $vq->where('price', '>=', $minPrice);
                        });
                });
            }
        }
        if ($request->filled('max_price')) {
            $maxPrice = (float) str_replace(['.', ',', ' '], '', $request->max_price);
            if ($maxPrice > 0) {
                $query->where(function ($q) use ($maxPrice) {
                    $q->where('original_price', '<=', $maxPrice)
                        ->orWhereHas('variants', function ($vq) use ($maxPrice) {
                            $vq->where('price', '<=', $maxPrice);
                        });
                });
            }
        }

        // 3. Filter by In Stock
        if ($request->boolean('in_stock')) {
            $query->whereHas('variants', function ($q) {
                $q->where('stock', '>', 0);
            });
        }

        // 4. Search Keyword
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // 5. Total Sold count for best seller sorting
        $query->withCount(['orderItems as total_sold' => function ($q) {
            $q->select(DB::raw('COALESCE(SUM(quantity), 0)'))
                ->whereHas('order', function ($oq) {
                    $oq->whereNotIn('status', ['cancelled', 'failed']);
                });
        }]);

        // 6. Sorting
        $sort = $request->get('sort', 'popular');
        switch ($sort) {
            case 'latest':
                $query->latest();
                break;
            case 'best_seller':
                $query->orderByDesc('total_sold')->latest();
                break;
            case 'price_asc':
                $query->orderBy('original_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('original_price', 'desc');
                break;
            case 'popular':
            default:
                $query->orderByDesc('total_sold')->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Categories with distinct active product count
        $categories = Category::where('is_active', true)
            ->withCount(['variants as products_count' => function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('is_active', true);
                })->select(DB::raw('count(distinct product_id)'));
            }])
            ->get();

        return view('user.collection.index', compact('products', 'categories', 'sort'));
    }
}


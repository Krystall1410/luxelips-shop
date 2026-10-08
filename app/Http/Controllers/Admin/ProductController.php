<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('variants.category');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('variants', function($vq) use ($search) {
                      $vq->where('type', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%")
                         ->orWhere('color_description', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category_id')) {
            $categoryId = $request->input('category_id');
            $query->whereHas('variants', function($vq) use ($categoryId) {
                $vq->where('category_id', $categoryId);
            });
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        $totalProductsCount = Product::count();
        $activeProductsCount = Product::where('is_active', true)->count();
        $totalCategoriesCount = Category::count();
        $totalStockSum = ProductVariant::sum('stock');

        return view('admin.products.index', compact('products', 'categories', 'totalProductsCount', 'activeProductsCount', 'totalCategoriesCount', 'totalStockSum'));
    }

    /**
     * Toggle product active status (Hiển thị / Ẩn).
     */
    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $product->is_active,
                'message' => $product->is_active ? 'Sản phẩm đã được bật hiển thị' : 'Sản phẩm đã được ẩn'
            ]);
        }

        return redirect()->back()->with('success', $product->is_active ? 'Đã bật hiển thị sản phẩm!' : 'Đã ẩn sản phẩm!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        if ($categories->isEmpty()) {
            $categories = Category::all();
        }
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        if ($brands->isEmpty()) {
            $brands = Brand::orderBy('name')->get();
        }
        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'brand_id' => 'nullable|exists:brands,id',
            'uses' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'original_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'required|array|min:1',
            'variants.*.type' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'variants.*.image_url' => 'nullable|string|max:1000',
            'variants.*.sku' => 'nullable|string|max:100|distinct|unique:product_variants,sku',
            'variants.*.color_description' => 'nullable|string|max:255',
            'variants.*.hex_color' => 'nullable|string|max:20'
        ], [
            'category_id.required' => 'Vui lòng chọn danh mục cho sản phẩm.',
            'sku.unique' => 'Mã SKU gốc này đã tồn tại trong hệ thống.',
            'slug.unique' => 'Slug sản phẩm này đã tồn tại.',
            'variants.*.price.required' => 'Vui lòng nhập giá cho từng phân loại / biến thể.',
            'variants.*.sku.unique' => 'Mã SKU của biến thể đã tồn tại trong hệ thống.',
            'variants.*.sku.distinct' => 'Mã SKU giữa các biến thể không được trùng nhau.'
        ]);

        // Generate unique slug
        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Product::generateUniqueSlug($request->name);

        // Calculate original price fallback from variant prices
        $variantPrices = collect($request->variants)->pluck('price')->filter(fn($p) => is_numeric($p));
        $originalPrice = $request->filled('original_price') 
            ? (float) $request->original_price 
            : ($variantPrices->min() ?? 0);

        // Create product
        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'sku' => $request->sku ? strtoupper(trim($request->sku)) : null,
            'brand_id' => $request->brand_id,
            'uses' => $request->uses,
            'ingredients' => $request->ingredients,
            'original_price' => $originalPrice,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        // Create variants
        foreach ($request->variants as $index => $variantData) {
            $imagePath = null;
            
            // Priority 1: File Upload
            if (isset($variantData['image']) && $variantData['image']->isValid()) {
                $imagePath = $variantData['image']->store('products', 'public');
            } 
            // Priority 2: Online Image URL
            elseif (!empty($variantData['image_url'])) {
                $imagePath = trim($variantData['image_url']);
            }

            // Determine SKU: custom or auto-generated from parent SKU
            $variantSku = !empty($variantData['sku']) ? strtoupper(trim($variantData['sku'])) : null;
            if (empty($variantSku) && !empty($product->sku)) {
                $baseVarSku = $product->sku . '-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $variantSku = $baseVarSku;
                $vCount = 1;
                while (ProductVariant::where('sku', $variantSku)->exists()) {
                    $variantSku = $baseVarSku . '-' . $vCount;
                    $vCount++;
                }
            }

            $vPrice = (isset($variantData['price']) && $variantData['price'] !== '') ? (float) $variantData['price'] : $originalPrice;

            ProductVariant::create([
                'product_id' => $product->id,
                'category_id' => $request->category_id,
                'type' => $variantData['type'],
                'price' => $vPrice,
                'stock' => $variantData['stock'],
                'image' => $imagePath,
                'sku' => $variantSku,
                'color_description' => $variantData['color_description'] ?? null,
                'hex_color' => $variantData['hex_color'] ?? null,
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm và các phân loại đã được thêm thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['variants.category', 'brand']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['variants', 'brand']);
        $categories = Category::all();
        $brands = Brand::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'brand_id' => 'nullable|exists:brands,id',
            'uses' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'original_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.type' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'variants.*.image_url' => 'nullable|string|max:1000',
            'variants.*.color_description' => 'nullable|string|max:255',
            'variants.*.hex_color' => 'nullable|string|max:20',
            'deleted_variants' => 'nullable|array',
            'deleted_variants.*' => 'exists:product_variants,id'
        ];

        if ($request->has('variants')) {
            foreach ($request->variants as $index => $v) {
                $vId = $v['id'] ?? null;
                $rules["variants.{$index}.sku"] = 'nullable|string|max:100|distinct|unique:product_variants,sku,' . ($vId ?: 'NULL') . ',id';
            }
        }

        $request->validate($rules, [
            'category_id.required' => 'Vui lòng chọn danh mục cho sản phẩm.',
            'sku.unique' => 'Mã SKU gốc này đã tồn tại trong hệ thống.',
            'slug.unique' => 'Slug sản phẩm này đã tồn tại.',
            'variants.*.price.required' => 'Vui lòng nhập giá cho từng phân loại / biến thể.',
            'variants.*.sku.unique' => 'Mã SKU của biến thể đã tồn tại trong hệ thống.',
            'variants.*.sku.distinct' => 'Mã SKU giữa các biến thể không được trùng nhau.'
        ]);

        // Generate unique slug
        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Product::generateUniqueSlug($request->name, $product->id);

        // Calculate original price fallback from variant prices
        $variantPrices = collect($request->variants)->pluck('price')->filter(fn($p) => is_numeric($p));
        $originalPrice = $request->filled('original_price') 
            ? (float) $request->original_price 
            : ($variantPrices->min() ?? $product->original_price ?? 0);

        // Update product basic info
        $product->update([
            'name' => $request->name,
            'slug' => $slug,
            'sku' => $request->sku ? strtoupper(trim($request->sku)) : null,
            'brand_id' => $request->brand_id,
            'uses' => $request->uses,
            'ingredients' => $request->ingredients,
            'original_price' => $originalPrice,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
        ]);

        // Handle deleted variants
        if ($request->has('deleted_variants')) {
            $deletedVariants = ProductVariant::whereIn('id', $request->deleted_variants)
                                            ->where('product_id', $product->id)
                                            ->get();
            foreach ($deletedVariants as $deletedVariant) {
                if ($deletedVariant->image && !str_starts_with($deletedVariant->image, 'http')) {
                    Storage::disk('public')->delete($deletedVariant->image);
                }
                $deletedVariant->delete();
            }
        }

        // Handle create/update variants
        foreach ($request->variants as $index => $variantData) {
            $variantModel = null;
            
            if (isset($variantData['id']) && $variantData['id']) {
                $variantModel = ProductVariant::where('id', $variantData['id'])
                                              ->where('product_id', $product->id)
                                              ->first();
            }

            $imagePath = $variantModel ? $variantModel->image : null;
            
            // Handle file upload if exists
            if (isset($variantData['image']) && $variantData['image']->isValid()) {
                if ($imagePath && !str_starts_with($imagePath, 'http')) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $variantData['image']->store('products', 'public');
            }
            // Handle online image URL if provided
            elseif (!empty($variantData['image_url'])) {
                $imagePath = trim($variantData['image_url']);
            }

            // Determine SKU: custom or auto-generated from parent SKU
            $variantSku = !empty($variantData['sku']) ? strtoupper(trim($variantData['sku'])) : null;
            if (empty($variantSku) && !empty($product->sku)) {
                $baseVarSku = $product->sku . '-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $variantSku = $baseVarSku;
                $vCount = 1;
                while (ProductVariant::where('sku', $variantSku)->where('id', '!=', $variantModel?->id)->exists()) {
                    $variantSku = $baseVarSku . '-' . $vCount;
                    $vCount++;
                }
            }

            $vPrice = (isset($variantData['price']) && $variantData['price'] !== '') ? (float) $variantData['price'] : $originalPrice;

            $variantPayload = [
                'product_id' => $product->id,
                'category_id' => $request->category_id,
                'type' => $variantData['type'],
                'price' => $vPrice,
                'stock' => $variantData['stock'],
                'image' => $imagePath,
                'sku' => $variantSku,
                'color_description' => $variantData['color_description'] ?? null,
                'hex_color' => $variantData['hex_color'] ?? null,
            ];

            if ($variantModel) {
                $variantModel->update($variantPayload);
            } else {
                ProductVariant::create($variantPayload);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $variants = $product->variants;
        foreach ($variants as $variant) {
            if ($variant->image && !str_starts_with($variant->image, 'http')) {
                Storage::disk('public')->delete($variant->image);
            }
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Đã xóa sản phẩm thành công!');
    }
}

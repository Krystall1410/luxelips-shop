<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Brand;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::withCount('products')->latest()->get();
        $totalBrandsCount = $brands->count();
        $activeBrandsCount = $brands->where('is_active', true)->count();

        return view('admin.brands.index', compact('brands', 'totalBrandsCount', 'activeBrandsCount'));
    }

    /**
     * Toggle brand active status.
     */
    public function toggleStatus(Brand $brand)
    {
        $brand->update([
            'is_active' => !$brand->is_active
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $brand->is_active,
                'message' => $brand->is_active ? 'Thương hiệu đã được bật hiển thị' : 'Thương hiệu đã được ẩn'
            ]);
        }

        return redirect()->back()->with('success', $brand->is_active ? 'Đã bật hiển thị thương hiệu!' : 'Đã ẩn thương hiệu!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'origin' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Vui lòng nhập tên thương hiệu.',
            'slug.unique' => 'Slug thương hiệu này đã tồn tại.',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Brand::generateUniqueSlug($request->name);

        Brand::create([
            'name' => $request->name,
            'slug' => $slug,
            'origin' => $request->origin,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Thương hiệu đã được tạo thành công');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands,slug,' . $brand->id,
            'origin' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Vui lòng nhập tên thương hiệu.',
            'slug.unique' => 'Slug thương hiệu này đã tồn tại.',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Brand::generateUniqueSlug($request->name, $brand->id);

        $brand->update([
            'name' => $request->name,
            'slug' => $slug,
            'origin' => $request->origin,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
        ]);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Thương hiệu đã được cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('admin.brands.index')
            ->with('success', 'Thương hiệu đã được xóa thành công');
    }
}

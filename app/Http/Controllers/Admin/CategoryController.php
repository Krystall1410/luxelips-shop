<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Auto-fix any missing category slugs
        foreach (Category::whereNull('slug')->get() as $cat) {
            $cat->update(['slug' => Category::generateUniqueSlug($cat->name, $cat->id)]);
        }

        $categories = Category::all();
        $totalCategoriesCount = $categories->count();
        $activeCategoriesCount = $categories->where('is_active', true)->count();

        return view('admin.categories.index', compact('categories', 'totalCategoriesCount', 'activeCategoriesCount'));
    }

    /**
     * Toggle category active status.
     */
    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $category->is_active,
                'message' => $category->is_active ? 'Danh mục đã được bật hiển thị' : 'Danh mục đã được ẩn'
            ]);
        }

        return redirect()->back()->with('success', $category->is_active ? 'Đã bật hiển thị danh mục!' : 'Đã ẩn danh mục!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'is_active' => 'nullable|boolean',
        ], [
            'slug.unique' => 'Slug danh mục này đã tồn tại.',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Category::generateUniqueSlug($request->name);

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục đã được tạo thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'is_active' => 'nullable|boolean',
        ], [
            'slug.unique' => 'Slug danh mục này đã tồn tại.',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Category::generateUniqueSlug($request->name, $category->id);

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục đã được cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục đã được xóa thành công');
    }
}

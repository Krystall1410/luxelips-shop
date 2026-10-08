@extends('admin.layouts.app')

@section('title', 'Danh sách Danh Mục | LuxeLips Admin')
@section('header_title', 'Quản Lý Danh Mục')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">
            <i class="bi bi-tags"></i>
            Danh sách Danh Mục
        </h1>
        <a class="btn-rose" href="{{ route('admin.categories.create') }}" id="btn-create-category">
            <i class="bi bi-plus-lg me-1"></i> Thêm Danh Mục
        </a>
    </div>

    {{-- Success Alert --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success rounded-3 mb-4 d-flex align-items-center" id="alert-success" role="alert" style="background-color: rgba(34, 197, 94, 0.15); border: none; border-left: 4px solid #22c55e; color: #4ade80;">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            {{ $message }}
        </div>
    @endif

    {{-- Summary Cards Row (2 Cards) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #8b5cf6;">
                    <i class="bi bi-tags-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Tổng số danh mục</div>
                    <div class="fw-bold text-light" style="font-size: 1.15rem;">{{ $totalCategoriesCount }} danh mục</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #10b981;">
                    <i class="bi bi-eye-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Danh mục đang hoạt động</div>
                    <div class="fw-bold text-success" style="font-size: 1.15rem;">{{ $activeCategoriesCount }} đang hiển thị</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card-elegant">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-table me-2"></i>
                Tất cả danh mục
            </div>
            <span class="badge rounded-pill" style="font-size:0.75rem; background-color: var(--primary-color) !important;">
                {{ $categories->count() }} mục
            </span>
        </div>

        @if($categories->isEmpty())
            <div class="card-body p-5 text-center text-muted">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #475569;"></i>
                <p class="mt-3 mb-0">Chưa có danh mục nào. <a href="{{ route('admin.categories.create') }}" style="color: var(--primary-color);">Thêm danh mục đầu tiên</a>.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="categories-table">
                    <thead style="background-color: var(--card-header-bg);">
                        <tr>
                            <th style="padding-left: 1.5rem; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border-color);">Tên danh mục</th>
                            <th style="color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border-color);">Slug (SEO)</th>
                            <th style="width:160px; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border-color);">Trạng thái</th>
                            <th scope="col" class="text-nowrap text-center" style="width: 180px; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border-color);">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr id="category-row-{{ $category->id }}" style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding-left: 1.5rem;"><span class="fw-semibold text-light" style="font-size: 0.85rem;">{{ $category->name }}</span></td>
                                <td>
                                    @if($category->slug)
                                        <code class="badge bg-dark border border-secondary text-info fs-7">{{ $category->slug }}</code>
                                    @else
                                        <span class="text-muted fs-7">Chưa có</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.categories.toggle-status', $category->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $category->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }} rounded-pill" style="font-size: 0.75rem; padding: 2px 10px;" title="Bật/Tắt hiển thị">
                                            @if($category->is_active)
                                                <i class="bi bi-eye-fill me-1"></i> Hiển thị
                                            @else
                                                <i class="bi bi-eye-slash-fill me-1"></i> Đang ẩn
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="text-nowrap text-center">
                                    <div class="d-inline-flex gap-1 justify-content-center py-1">
                                        <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-sm btn-outline-light border-secondary px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Xem chi tiết" id="btn-view-{{ $category->id }}">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Sửa thông tin" id="btn-edit-{{ $category->id }}">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xoá danh mục này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Xóa danh mục" id="btn-delete-{{ $category->id }}">
                                                <i class="bi bi-trash3"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection

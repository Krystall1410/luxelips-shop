@extends('admin.layouts.app')

@section('title', 'Danh sách Thương Hiệu | LuxeLips Admin')
@section('header_title', 'Quản Lý Thương Hiệu')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">
            <i class="bi bi-award"></i>
            Danh sách Thương Hiệu
        </h1>
        <a class="btn-rose" href="{{ route('admin.brands.create') }}" id="btn-create-brand">
            <i class="bi bi-plus-lg me-1"></i> Thêm Thương Hiệu
        </a>
    </div>

    {{-- Success Alert --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success rounded-3 mb-4 d-flex align-items-center" id="alert-success" role="alert" style="background-color: rgba(34, 197, 94, 0.15); border: none; border-left: 4px solid #22c55e; color: #4ade80;">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            {{ $message }}
        </div>
    @endif

    {{-- Summary Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #ec4899;">
                    <i class="bi bi-award-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Tổng số thương hiệu</div>
                    <div class="fw-bold text-light" style="font-size: 1.15rem;">{{ $totalBrandsCount }} thương hiệu</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #10b981;">
                    <i class="bi bi-eye-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Thương hiệu đang hiển thị</div>
                    <div class="fw-bold text-success" style="font-size: 1.15rem;">{{ $activeBrandsCount }} đang hiển thị</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card-elegant">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-table me-2"></i>
                Tất cả thương hiệu
            </div>
            <span class="badge rounded-pill" style="font-size:0.75rem; background-color: var(--primary-color) !important;">
                {{ $brands->count() }} mục
            </span>
        </div>

        @if($brands->isEmpty())
            <div class="card-body p-5 text-center text-muted">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #475569;"></i>
                <p class="mt-3 mb-0">Chưa có thương hiệu nào. <a href="{{ route('admin.brands.create') }}" style="color: var(--primary-color);">Thêm thương hiệu đầu tiên</a>.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="brands-table">
                    <thead style="background-color: var(--card-header-bg);">
                        <tr>
                            <th style="padding-left: 1.5rem; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border-color);">Tên thương hiệu</th>
                            <th style="color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border-color);">Xuất xứ thương hiệu</th>
                            <th style="color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border-color);">Slug (SEO)</th>
                            <th style="color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border-color);">Số sản phẩm</th>
                            <th style="width:160px; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border-color);">Trạng thái</th>
                            <th scope="col" class="text-nowrap text-center" style="width: 180px; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border-color);">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($brands as $brand)
                            <tr id="brand-row-{{ $brand->id }}" style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding-left: 1.5rem;">
                                    <span class="fw-semibold text-light" style="font-size: 0.85rem;">{{ $brand->name }}</span>
                                </td>
                                <td>
                                    @if($brand->origin)
                                        <span class="badge bg-secondary text-light px-2 py-1" style="font-size: 0.78rem; font-weight: 500;">
                                            <i class="bi bi-geo-alt-fill me-1 text-danger"></i>{{ $brand->origin }}
                                        </span>
                                    @else
                                        <span class="text-muted fs-7">Chưa cập nhật</span>
                                    @endif
                                </td>
                                <td>
                                    @if($brand->slug)
                                        <code class="badge bg-dark border border-secondary text-info fs-7">{{ $brand->slug }}</code>
                                    @else
                                        <span class="text-muted fs-7">Chưa có</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-dark border border-secondary text-slate-300">
                                        {{ $brand->products_count }} SP
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.brands.toggle-status', $brand->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $brand->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }} rounded-pill" style="font-size: 0.75rem; padding: 2px 10px;" title="Bật/Tắt hiển thị">
                                            @if($brand->is_active)
                                                <i class="bi bi-eye-fill me-1"></i> Hiển thị
                                            @else
                                                <i class="bi bi-eye-slash-fill me-1"></i> Đang ẩn
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="text-nowrap text-center">
                                    <div class="d-inline-flex gap-1 justify-content-center py-1">
                                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-sm btn-outline-primary px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Sửa thông tin" id="btn-edit-{{ $brand->id }}">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>
                                        <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xoá thương hiệu này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Xóa thương hiệu" id="btn-delete-{{ $brand->id }}">
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

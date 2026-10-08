@extends('admin.layouts.app')

@section('title', 'Chi tiết Danh Mục | LuxeLips Admin')
@section('header_title', 'Quản Lý Danh Mục')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">
            <i class="bi bi-folder2-open"></i>
            Chi tiết Danh Mục
        </h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card-elegant">
        <div class="card-header">
            <i class="bi bi-info-circle me-2"></i> Thông tin Danh Mục
        </div>

        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <tbody>
                    <tr>
                        <th style="width: 200px; padding: 1.5rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">ID</th>
                        <td style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                            <span class="badge bg-light text-dark border">#{{ $category->id }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 1.5rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Tên Danh Mục</th>
                        <td style="padding: 1.5rem; font-size: 1.1rem; font-weight: 500; color: var(--primary-color); border-bottom: 1px solid var(--border-color);">
                            {{ $category->name }}
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 1.5rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Slug (Đường dẫn SEO)</th>
                        <td style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                            <code class="badge bg-dark border border-secondary text-info fs-6">{{ $category->slug }}</code>
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 1.5rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color);">Trạng thái</th>
                        <td style="padding: 1.5rem;">
                            <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                                {{ $category->is_active ? 'Đang hiển thị' : 'Đang ẩn' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-footer border-top p-4 d-flex justify-content-end gap-2" style="background-color: transparent; border-color: var(--border-color) !important;">
            <a class="btn btn-outline-primary"
               href="{{ route('admin.categories.edit', $category->id) }}"
               id="btn-edit-{{ $category->id }}">
                <i class="bi bi-pencil me-1"></i> Chỉnh sửa
            </a>
            <form action="{{ route('admin.categories.destroy', $category->id) }}"
                  method="POST"
                  style="display:inline"
                  onsubmit="return confirm('Bạn có chắc muốn xoá danh mục này?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="btn btn-outline-danger"
                        id="btn-delete-{{ $category->id }}">
                    <i class="bi bi-trash3 me-1"></i> Xoá
                </button>
            </form>
        </div>
    </div>

@endsection

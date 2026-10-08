@extends('admin.layouts.app')

@section('title', 'Thêm Danh Mục | LuxeLips Admin')
@section('header_title', 'Quản Lý Danh Mục')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">
            <i class="bi bi-plus-circle-dotted"></i>
            Thêm Danh Mục Mới
        </h1>
        <a href="{{ route('admin.categories.index') }}" class="btn-outline-rose">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card-elegant">
        <div class="card-header">
            <i class="bi bi-folder-plus me-2"></i> Thông tin danh mục
        </div>

        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 mb-4" role="alert" style="background-color: rgba(225, 29, 72, 0.1); border: none; border-left: 4px solid #f43f5e; color: #fb7185;">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <strong>Có lỗi xảy ra:</strong>
                    </div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.categories.store') }}" method="POST" id="form-create-category">
                @csrf
                <div class="row">
                    <div class="col-md-5 mb-4">
                        <label for="input-name" class="form-label">
                            Tên Danh Mục <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="input-name"
                               name="name"
                               class="form-control"
                               placeholder="VD: Son Kem Lì, Son Thỏi..."
                               value="{{ old('name') }}"
                               required
                               autofocus>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="input-slug" class="form-label">
                            Slug (Đường dẫn SEO) <span class="text-muted fs-7">(Tự động tạo)</span>
                        </label>
                        <input type="text"
                               id="input-slug"
                               name="slug"
                               class="form-control"
                               placeholder="VD: son-kem-li"
                               value="{{ old('slug') }}">
                    </div>

                    <div class="col-md-3 mb-4">
                        <label class="form-label">Trạng thái</label>
                        <div class="form-check form-switch pt-1">
                            <input class="form-check-input" type="checkbox" name="is_active" id="switch-is-active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 2.3em; height: 1.2em; cursor: pointer;">
                            <label class="form-check-label ms-1 text-light fw-medium" for="switch-is-active">Hiển thị</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-4" style="border-color: var(--border-color) !important;">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary" style="padding: 0.6rem 1.5rem; font-weight: 500; border-radius: 8px;">
                        Huỷ bỏ
                    </a>
                    <button type="submit" class="btn btn-outline-primary" style="padding: 0.6rem 1.5rem;">
                        <i class="bi bi-floppy me-1"></i> Lưu Danh Mục
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('input-name');
        const slugInput = document.getElementById('input-slug');
        if (nameInput && slugInput) {
            let isManualSlug = false;
            slugInput.addEventListener('input', function() {
                isManualSlug = true;
            });
            nameInput.addEventListener('input', function() {
                if (!isManualSlug) {
                    slugInput.value = nameInput.value.toLowerCase().trim()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                        .replace(/[đĐ]/g, 'd')
                        .replace(/[^a-z0-9 -]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                }
            });
        }
    });
</script>
@endsection

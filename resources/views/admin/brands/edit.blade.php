@extends('admin.layouts.app')

@section('title', 'Sửa Thương Hiệu | LuxeLips Admin')
@section('header_title', 'Quản Lý Thương Hiệu')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">
            <i class="bi bi-pencil-square"></i>
            Chỉnh Sửa Thương Hiệu: {{ $brand->name }}
        </h1>
        <a href="{{ route('admin.brands.index') }}" class="btn-outline-rose">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card-elegant">
        <div class="card-header">
            <i class="bi bi-award me-2"></i> Thông tin thương hiệu
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

            <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" id="form-edit-brand">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-5 mb-4">
                        <label for="input-name" class="form-label">
                            Tên Thương Hiệu <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="input-name"
                               name="name"
                               class="form-control"
                               placeholder="VD: MAC, 3CE, Dior, Romand..."
                               value="{{ old('name', $brand->name) }}"
                               required>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="input-origin" class="form-label">
                            Xuất Xứ Thương Hiệu <span class="text-info fs-7">(Quốc gia / Nơi ra đời)</span>
                        </label>
                        <input type="text"
                               id="input-origin"
                               name="origin"
                               class="form-control"
                               placeholder="VD: Hàn Quốc, Pháp, Mỹ, Nhật Bản..."
                               value="{{ old('origin', $brand->origin) }}">
                    </div>

                    <div class="col-md-3 mb-4">
                        <label class="form-label">Trạng thái</label>
                        <div class="form-check form-switch pt-1">
                            <input class="form-check-input" type="checkbox" name="is_active" id="switch-is-active" value="1" {{ old('is_active', $brand->is_active) ? 'checked' : '' }} style="width: 2.3em; height: 1.2em; cursor: pointer;">
                            <label class="form-check-label ms-1 text-light fw-medium" for="switch-is-active">Hiển thị</label>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="input-slug" class="form-label">
                            Slug (Đường dẫn SEO) <span class="text-muted fs-7">(Để trống sẽ tự tạo lại)</span>
                        </label>
                        <input type="text"
                               id="input-slug"
                               name="slug"
                               class="form-control"
                               placeholder="VD: 3ce, mac, dior"
                               value="{{ old('slug', $brand->slug) }}">
                    </div>

                    <div class="col-12 mb-4">
                        <label for="input-description" class="form-label">
                            Mô tả thương hiệu <span class="text-muted fs-7">(Tùy chọn)</span>
                        </label>
                        <textarea id="input-description"
                                  name="description"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Giới thiệu ngắn về thương hiệu, lịch sử hình thành hoặc phong cách sản phẩm...">{{ old('description', $brand->description) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-4" style="border-color: var(--border-color) !important;">
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary" style="padding: 0.6rem 1.5rem; font-weight: 500; border-radius: 8px;">
                        Huỷ bỏ
                    </a>
                    <button type="submit" class="btn btn-outline-primary" style="padding: 0.6rem 1.5rem;">
                        <i class="bi bi-floppy me-1"></i> Cập Nhật Thương Hiệu
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@extends('admin.layouts.app')

@section('title', 'Thêm Sản Phẩm Mới | LuxeLips Admin')
@section('header_title', 'Quản Lý Sản Phẩm')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">
            <i class="bi bi-box-seam"></i>
            Thêm Sản Phẩm Mới
        </h1>
        <a href="{{ route('admin.products.index') }}" class="btn-outline-rose">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

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

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="form-create-product">
        @csrf

        <!-- Phần 1: Thông tin cơ bản -->
        <div class="card-elegant mb-4">
            <div class="card-header">
                <i class="bi bi-info-square me-2"></i> Thông tin cơ bản
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label for="input-name" class="form-label">
                            Tên Sản Phẩm <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="input-name"
                               name="name"
                               class="form-control"
                               placeholder="VD: Son Mac Matte Lipstick..."
                               value="{{ old('name') }}"
                               required
                               autofocus>
                    </div>

                    <div class="col-md-3 mb-4">
                        <label for="input-parent-sku" class="form-label">
                            Mã SKU Gốc <span class="text-pink fs-7">(Dùng tạo SKU biến thể)</span>
                        </label>
                        <input type="text"
                               id="input-parent-sku"
                               name="sku"
                               class="form-control"
                               placeholder="VD: MAC-LIP"
                               value="{{ old('sku') }}">
                    </div>

                    <div class="col-md-3 mb-4">
                        <label for="select-brand" class="form-label">
                            Thương Hiệu
                        </label>
                        <select name="brand_id" id="select-brand" class="form-select">
                            <option value="">-- Chọn thương hiệu đã có --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }} @if($brand->origin)({{ $brand->origin }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-4">
                        <label class="form-label">Trạng thái</label>
                        <div class="form-check form-switch pt-1">
                            <input class="form-check-input" type="checkbox" name="is_active" id="switch-is-active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 2.3em; height: 1.2em; cursor: pointer;">
                            <label class="form-check-label ms-1 text-light fw-medium" for="switch-is-active">Hiển thị</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label for="select-category" class="form-label">
                            Danh Mục Sản Phẩm <span class="text-danger">*</span>
                        </label>
                        <select name="category_id" id="select-category" class="form-select" required>
                            <option value="">-- Chọn danh mục sản phẩm --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-8 mb-4">
                        <label for="input-slug" class="form-label">
                            Slug (Đường dẫn SEO) <span class="text-muted fs-7">(Tự động)</span>
                        </label>
                        <input type="text"
                               id="input-slug"
                               name="slug"
                               class="form-control"
                               placeholder="VD: son-mac-matte-lipstick"
                               value="{{ old('slug') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label for="input-description" class="form-label">
                            Mô Tả Sản Phẩm
                        </label>
                        <textarea id="input-description"
                                  name="description"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Nhập thông tin giới thiệu chung về sản phẩm...">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="input-uses" class="form-label">
                            Công Dụng Sản Phẩm <span class="text-info fs-7">(Hiển thị chi tiết)</span>
                        </label>
                        <textarea id="input-uses"
                                  name="uses"
                                  class="form-control"
                                  rows="4"
                                  placeholder="VD: Dưỡng ẩm môi mềm mượt, lên màu chuẩn sắc nét lâu trôi, bảo vệ môi khỏi khô ráp...">{{ old('uses') }}</textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="input-ingredients" class="form-label">
                            Thành Phần / Chất Liệu <span class="text-info fs-7">(Hiển thị chi tiết)</span>
                        </label>
                        <textarea id="input-ingredients"
                                  name="ingredients"
                                  class="form-control"
                                  rows="4"
                                  placeholder="VD: Chiết xuất bơ hạt mỡ, Vitamin E, Dầu Jojoba, Sáp ong tự nhiên...">{{ old('ingredients') }}</textarea>
                    </div>
                </div>

        <!-- Phần 2: Phân loại sản phẩm (Variants) -->
        <div class="card-elegant mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><i class="bi bi-layers me-2"></i> Phân loại sản phẩm (Biến thể)</div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-info" id="btn-auto-generate-skus" title="Tự động điền SKU biến thể theo SKU gốc">
                        <i class="bi bi-magic me-1"></i> Tạo SKU tự động
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-rose" id="btn-add-variant">
                        <i class="bi bi-plus-circle me-1"></i> Thêm phân loại
                    </button>
                </div>
            </div>
            
            <div class="card-body p-4" id="variants-container" style="background-color: #0f172a !important;">
                
                @php
                    $oldVariants = old('variants', [
                        0 => [
                            'type' => '',
                            'category_id' => '',
                            'stock' => 0,
                            'sku' => '',
                            'color_description' => '',
                            'hex_color' => '#ec4899',
                            'image_url' => ''
                        ]
                    ]);
                @endphp

                @foreach($oldVariants as $index => $variantData)
                <!-- Variant Item -->
                <div class="variant-item p-3 border border-secondary rounded-3 mb-3 shadow-sm position-relative" style="background-color: #1e293b;">
                    <div class="position-absolute top-0 end-0 p-2">
                        <button type="button" class="btn-close btn-close-white btn-remove-variant text-danger" aria-label="Close" style="font-size: 0.8rem;"></button>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Thể loại (Màu sắc) <span class="text-danger">*</span></label>
                            <input type="text" name="variants[{{ $index }}][type]" class="form-control form-control-sm" placeholder="VD: Đỏ thuần bản đặc biệt" value="{{ $variantData['type'] ?? '' }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size: 0.85rem;">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="variants[{{ $index }}][price]" class="form-control form-control-sm" placeholder="VD: 350000" value="{{ $variantData['price'] ?? '' }}" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size: 0.85rem;">Tồn kho *</label>
                            <input type="number" name="variants[{{ $index }}][stock]" class="form-control form-control-sm" value="{{ $variantData['stock'] ?? 0 }}" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Ảnh (File hoặc Link Online)</label>
                            <div class="d-flex flex-column gap-1">
                                <input type="file" name="variants[{{ $index }}][image]" class="form-control form-control-sm input-variant-image" accept="image/*">
                                <input type="url" name="variants[{{ $index }}][image_url]" class="form-control form-control-sm input-variant-image-url" placeholder="Hoặc dán Link ảnh Online (https://...)" value="{{ $variantData['image_url'] ?? '' }}">
                                <img class="img-preview d-none rounded border mt-1" style="width: 40px; height: 40px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Mã SKU</label>
                            <input type="text" name="variants[{{ $index }}][sku]" class="form-control form-control-sm input-variant-sku" placeholder="VD: MAC-RBY-01" value="{{ $variantData['sku'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 0.85rem;">Mô tả màu</label>
                            <input type="text" name="variants[{{ $index }}][color_description]" class="form-control form-control-sm" placeholder="VD: Sắc đỏ lạnh nổi bật..." value="{{ $variantData['color_description'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size: 0.85rem;">Mã màu Hex</label>
                            <input type="color" name="variants[{{ $index }}][hex_color]" class="form-control form-control-sm form-control-color w-100" value="{{ $variantData['hex_color'] ?? '#ec4899' }}">
                        </div>
                    </div>
                </div>
                <!-- End Variant Item -->
                @endforeach

            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pb-5">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary" style="padding: 0.6rem 1.5rem; font-weight: 500; border-radius: 8px;">
                Huỷ bỏ
            </a>
            <button type="submit" class="btn btn-outline-primary" style="padding: 0.6rem 2rem;">
                <i class="bi bi-floppy me-1"></i> Lưu Sản Phẩm
            </button>
        </div>
    </form>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let variantIndex = Number('{{ count($oldVariants) }}');
        const container = document.getElementById('variants-container');
        const btnAdd = document.getElementById('btn-add-variant');
        
        function initImagePreview(item) {
            const fileInput = item.querySelector('.input-variant-image');
            const urlInput = item.querySelector('.input-variant-image-url');
            const preview = item.querySelector('.img-preview');

            function updatePreview() {
                if (fileInput && fileInput.files && fileInput.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        preview.src = evt.target.result;
                        preview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(fileInput.files[0]);
                } else if (urlInput && urlInput.value.trim()) {
                    preview.src = urlInput.value.trim();
                    preview.classList.remove('d-none');
                } else {
                    preview.classList.add('d-none');
                }
            }

            if (fileInput) fileInput.addEventListener('change', updatePreview);
            if (urlInput) urlInput.addEventListener('input', updatePreview);
        }

        container.querySelectorAll('.variant-item').forEach(initImagePreview);

        const getVariantTemplate = (index) => `
            <div class="variant-item p-3 border border-secondary rounded-3 mb-3 shadow-sm position-relative" style="background-color: #1e293b;">
                <div class="position-absolute top-0 end-0 p-2">
                    <button type="button" class="btn-close btn-close-white btn-remove-variant text-danger" aria-label="Close" style="font-size: 0.8rem;"></button>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size: 0.85rem;">Thể loại (Màu sắc) <span class="text-danger">*</span></label>
                        <input type="text" name="variants[${index}][type]" class="form-control form-control-sm" placeholder="VD: Đỏ thuần bản đặc biệt" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size: 0.85rem;">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="variants[${index}][price]" class="form-control form-control-sm" placeholder="VD: 350000" min="0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size: 0.85rem;">Tồn kho *</label>
                        <input type="number" name="variants[${index}][stock]" class="form-control form-control-sm" value="0" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size: 0.85rem;">Ảnh (File hoặc Link Online)</label>
                        <div class="d-flex flex-column gap-1">
                            <input type="file" name="variants[${index}][image]" class="form-control form-control-sm input-variant-image" accept="image/*">
                            <input type="url" name="variants[${index}][image_url]" class="form-control form-control-sm input-variant-image-url" placeholder="Hoặc dán Link ảnh Online (https://...)">
                            <img class="img-preview d-none rounded border mt-1" style="width: 40px; height: 40px; object-fit: cover;">
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size: 0.85rem;">Mã SKU</label>
                        <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm input-variant-sku" placeholder="VD: MAC-RBY-01">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 0.85rem;">Mô tả màu</label>
                        <input type="text" name="variants[${index}][color_description]" class="form-control form-control-sm" placeholder="VD: Sắc đỏ lạnh nổi bật...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size: 0.85rem;">Mã màu Hex</label>
                        <input type="color" name="variants[${index}][hex_color]" class="form-control form-control-sm form-control-color w-100" value="#ec4899">
                    </div>
                </div>
            </div>
        `;

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

        function autoGenerateVariantSkus(force = false) {
            const parentSkuInput = document.getElementById('input-parent-sku');
            if (!parentSkuInput) return;
            const parentSku = parentSkuInput.value.trim().toUpperCase();
            if (!parentSku && force) {
                alert('Vui lòng nhập Mã SKU Gốc ở phần Thông tin cơ bản trước!');
                return;
            }
            if (!parentSku) return;

            const variantSkuInputs = container.querySelectorAll('.input-variant-sku');
            variantSkuInputs.forEach((input, idx) => {
                if (force || !input.value.trim()) {
                    const padIndex = String(idx + 1).padStart(2, '0');
                    input.value = `${parentSku}-${padIndex}`;
                }
            });
        }

        const parentSkuInput = document.getElementById('input-parent-sku');
        if (parentSkuInput) {
            parentSkuInput.addEventListener('input', function() {
                autoGenerateVariantSkus(false);
            });
        }

        const btnAutoSku = document.getElementById('btn-auto-generate-skus');
        if (btnAutoSku) {
            btnAutoSku.addEventListener('click', function() {
                autoGenerateVariantSkus(true);
            });
        }

        btnAdd.addEventListener('click', function() {
            const template = getVariantTemplate(variantIndex);
            container.insertAdjacentHTML('beforeend', template);
            const newItem = container.lastElementChild;
            initImagePreview(newItem);
            variantIndex++;
            autoGenerateVariantSkus(false);
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-variant')) {
                const variantsCount = container.querySelectorAll('.variant-item').length;
                if (variantsCount > 1) {
                    e.target.closest('.variant-item').remove();
                } else {
                    alert('Sản phẩm phải có ít nhất 1 phân loại!');
                }
            }
        });
    });
</script>
@endsection

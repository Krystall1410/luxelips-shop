@extends('admin.layouts.app')

@section('title', 'Chi tiết Sản Phẩm | LuxeLips Admin')
@section('header_title', 'Quản Lý Sản Phẩm')

@section('content')
@php /** @var \App\Models\Product $product */ @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">
        <i class="bi bi-eye"></i>
        Chi tiết Sản Phẩm
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Sửa Sản Phẩm
        </a>
    </div>
</div>

<!-- Stat cards summary -->
<div class="row g-2.5 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
            <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #ec4899;">
                <i class="bi bi-tag fs-6"></i>
            </div>
            <div class="min-w-0">
                <div class="text-slate-400 fs-8 fw-medium">Khoảng giá bán</div>
                <div class="fw-bold text-success" style="font-size: 1.15rem; line-height: 1.2;">{{ $product->price_range_formatted }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
            <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #3b82f6;">
                <i class="bi bi-layers fs-6"></i>
            </div>
            <div class="min-w-0">
                <div class="text-slate-400 fs-8 fw-medium">Số lượng phân loại</div>
                <div class="fw-bold text-light" style="font-size: 1.15rem; line-height: 1.2;">{{ $product->variants->count() }} biến thể</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
            <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #10b981;">
                <i class="bi bi-box-seam fs-6"></i>
            </div>
            <div class="min-w-0">
                <div class="text-slate-400 fs-8 fw-medium">Tổng tồn kho</div>
                <div class="fw-bold text-light" style="font-size: 1.15rem; line-height: 1.2;">{{ $product->total_stock }} sản phẩm</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
            <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0 {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}" style="width: 38px; height: 38px;">
                <i class="bi {{ $product->is_active ? 'bi-eye-fill' : 'bi-eye-slash-fill' }} fs-6"></i>
            </div>
            <div class="min-w-0">
                <div class="text-slate-400 fs-8 fw-medium">Trạng thái</div>
                <div class="fw-bold {{ $product->is_active ? 'text-success' : 'text-slate-300' }}" style="font-size: 1.15rem; line-height: 1.2;">
                    {{ $product->is_active ? 'Đang hiển thị' : 'Đang ẩn' }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Thông tin cơ bản -->
<div class="card-elegant mb-4">
    <div class="card-header">
        <i class="bi bi-info-square me-2"></i> Thông tin cơ bản <span class="badge bg-dark border border-secondary text-light ms-2">#{{ $product->id }}</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-borderless mb-0">
            <tbody>
                <tr>
                    <th style="width: 200px; padding: 1.25rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Tên sản phẩm gốc</th>
                    <td style="padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
                        <h5 class="mb-0 fw-bold text-light">{{ $product->name }}</h5>
                    </td>
                </tr>
                @if($product->slug)
                <tr>
                    <th style="padding: 1.25rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Slug (Đường dẫn SEO)</th>
                    <td style="padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
                        <span class="badge bg-dark border border-secondary text-info fs-7">{{ $product->slug }}</span>
                    </td>
                </tr>
                @endif
                @if($product->sku)
                <tr>
                    <th style="padding: 1.25rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Mã SKU Gốc</th>
                    <td style="padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
                        <code class="text-rose fs-6 fw-bold">{{ $product->sku }}</code>
                    </td>
                </tr>
                @endif
                <tr>
                    <th style="padding: 1.25rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Thương hiệu</th>
                    <td style="padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
                        @if($product->brand)
                            <span class="badge bg-secondary text-light fs-7 px-2.5 py-1">
                                <i class="bi bi-award me-1 text-warning"></i>{{ $product->brand->name }}
                            </span>
                            @if($product->brand->origin)
                                <span class="ms-2 text-slate-300 fs-7">(Xuất xứ: <strong>{{ $product->brand->origin }}</strong>)</span>
                            @endif
                        @else
                            <span class="text-muted fst-italic">Chưa gán thương hiệu</span>
                        @endif
                    </td>
                </tr>
                @if($product->uses)
                <tr>
                    <th style="padding: 1.25rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Công dụng</th>
                    <td style="padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
                        <div class="text-slate-300" style="white-space: pre-line;">{{ $product->uses }}</div>
                    </td>
                </tr>
                @endif
                @if($product->ingredients)
                <tr>
                    <th style="padding: 1.25rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Thành phần / Chất liệu</th>
                    <td style="padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
                        <div class="text-slate-300" style="white-space: pre-line;">{{ $product->ingredients }}</div>
                    </td>
                </tr>
                @endif
                <tr>
                    <th style="padding: 1.25rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color);">Mô tả</th>
                    <td style="padding: 1.25rem;">
                        @if($product->description)
                        <div class="text-slate-300" style="white-space: pre-line;">{{ $product->description }}</div>
                        @else
                        <span class="text-muted fst-italic">Không có mô tả</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Phân loại sản phẩm -->
<div class="card-elegant mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><i class="bi bi-layers me-2"></i> Danh sách biến thể & Phiên bản đặc biệt</div>
        <span class="badge bg-rose text-white rounded-pill px-3 py-2">{{ $product->variants->count() }} biến thể</span>
    </div>

    <div class="card-body p-4" style="background-color: #0f172a;">
        <div class="row g-4">
            @foreach($product->variants as $variant)
            <div class="col-md-6 col-lg-4">
                <div class="border border-secondary rounded-3 p-3 shadow-sm h-100 d-flex flex-column position-relative overflow-hidden" style="background-color: #1e293b;">
                    <!-- Color Banner top -->
                    @if($variant->hex_color)
                    <div class="position-absolute top-0 start-0 end-0" {!! 'style="height: 6px; background-color: ' . $variant->hex_color . ';"' !!}></div>
                    @endif

                    <div class="d-flex mb-3 mt-2">
                        <div class="me-3">
                            @if($variant->image_url)
                            <img src="{{ $variant->image_url }}" alt="{{ $variant->type }}" class="rounded-3 object-fit-cover border border-secondary" style="width: 75px; height: 75px;">
                            @else
                            <div class="rounded-3 border border-secondary d-flex align-items-center justify-content-center bg-dark text-muted" style="width: 75px; height: 75px;">
                                <i class="bi bi-image fs-3"></i>
                            </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-light">{{ $product->name }} - {{ $variant->type }}</h6>
                            <div class="badge bg-dark text-light border border-secondary mb-1">
                                {{ $variant->category ? $variant->category->name : 'Không có danh mục' }}
                            </div>
                            @if($variant->sku)
                            <div class="text-muted fs-7">SKU: <code class="text-rose">{{ $variant->sku }}</code></div>
                            @endif
                        </div>
                    </div>

                    @if($variant->color_description)
                    <div class="text-muted fs-7 mb-3 fst-italic">
                        <i class="bi bi-info-circle me-1"></i> {{ $variant->color_description }}
                    </div>
                    @endif

                    <div class="mt-auto pt-3 border-top border-secondary d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted fs-7">Giá bán biến thể:</div>
                            <strong class="text-success fs-6">
                                {{ number_format($variant->effective_price, 0, ',', '.') }} ₫
                            </strong>
                            @if(!is_null($variant->price) && $variant->price != $product->original_price)
                            <span class="badge bg-warning-subtle text-warning border border-warning ms-1" style="font-size: 0.7rem;">Giá riêng</span>
                            @endif
                        </div>

                        <div class="text-end">
                            <div class="text-muted fs-7">Tồn kho:</div>
                            @if($variant->stock > 0)
                            <strong class="text-success">{{ $variant->stock }}</strong>
                            @else
                            <strong class="text-danger">{{ $variant->stock }}</strong>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@extends('user.layouts.app')

@section('title', $product->name . ' | LuxeLips PRO')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/pages/product-detail.css') }}">
@endpush

@section('content')
<div class="container py-4" id="product-detail-container"
    data-is-guest="{{ Auth::guest() ? 'true' : 'false' }}"
    data-login-url="{{ route('login') }}"
    data-cart-add-url="{{ route('cart.add') }}"
    data-checkout-url="{{ route('checkout.index') }}">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0 fs-8">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none" style="color: #475569;">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('collection.index') }}" class="text-decoration-none" style="color: #475569;">Bộ sưu tập</a></li>
            <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    @php
    $firstVariant = $product->variants->first();
    $defaultPrice = $firstVariant ? $firstVariant->effective_price : $product->original_price;
    $defaultImage = $firstVariant?->image_url ?: $product->first_image_url;
    $defaultSku = $firstVariant?->sku ?: ($product->sku ?: 'LIP-' . str_pad($product->id, 5, '0', STR_PAD_LEFT));
    $defaultStock = $firstVariant ? $firstVariant->stock : $product->total_stock;
    @endphp

    <!-- PHẦN 1: Top Product Detail (Cocolux Style: Gallery + Variant Selector) -->
    <div class="cocolux-card p-4 p-lg-5 mb-5">
        <div class="row g-4 g-lg-5 align-items-start">

            <!-- Cột trái: Hình ảnh lớn sản phẩm -->
            <div class="col-lg-5">
                <div class="product-gallery-main shadow-lg">
                    <img id="main-product-image" src="{{ $defaultImage ?: 'https://placehold.co/600x600/131d38/ec4899?text=LuxeLips' }}" alt="{{ $product->name }}">

                    <div class="color-preview-badge {{ $firstVariant?->hex_color ? '' : 'd-none' }}" id="color-preview-pill">
                        <div class="color-preview-circle" id="color-preview-dot" style="background-color: {{ $firstVariant?->hex_color ?? '#ec4899' }};"></div>
                        <div class="text-start">
                            <span class="fs-8 fw-semibold text-dark d-block lh-1" id="color-preview-name">{{ $firstVariant?->type ?? 'Màu chuẩn' }}</span>
                            <small class="text-muted fs-9 d-block mt-0.5 {{ $firstVariant?->color_description ? '' : 'd-none' }}" id="color-preview-desc">{{ $firstVariant?->color_description }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Thông tin đặt hàng & Chọn biến thể -->
            <div class="col-lg-7">
                <div class="ps-lg-2">
                    <!-- Tên sản phẩm -->
                    <h1 class="fs-4 fw-bold text-dark mb-2" style="line-height: 1.35; letter-spacing: -0.2px;">{{ $product->name }}</h1>

                    <!-- Meta thông tin: Thương hiệu & Xuất xứ thương hiệu -->
                    <div class="d-flex align-items-center flex-wrap gap-2.5 mb-3 fs-8" style="color: #0f172a; font-weight: 500;">
                        <div>Thương hiệu: <strong class="text-rose" style="color: #ec4899;">{{ $product->brand?->name ?? 'LuxeLips Cosmetics' }}</strong></div>
                        @if($product->brand?->origin)
                        <span style="color: #0f172a; font-weight: bold;">•</span>
                        <div>Xuất xứ thương hiệu: <strong style="color: #0f172a;">{{ $product->brand->origin }}</strong></div>
                        @endif
                    </div>

                    <!-- Giá bán sản phẩm -->
                    <div class="mb-4 d-flex align-items-center gap-3">
                        <span class="fs-2 fw-bold" id="current-price" style="color: #ec4899; letter-spacing: -0.5px;">
                            {{ number_format($defaultPrice, 0, ',', '.') }} ₫
                        </span>
                        @if($product->original_price > $defaultPrice)
                        <span class="text-decoration-line-through fs-6" id="original-price-strike" style="color: #64748b;">
                            {{ number_format($product->original_price, 0, ',', '.') }} ₫
                        </span>
                        @endif
                        <span class="badge bg-danger rounded-pill px-2.5 py-1 fs-9">Chính hãng 100%</span>
                    </div>

                    <!-- Khu vực Chọn Biến Thể -->
                    <div class="mb-4">
                        <label class="form-label d-block fs-7 fw-semibold mb-1" style="color: #0f172a;">
                            Loại sản phẩm / Màu sắc:
                            <strong class="ms-1" id="current-variant-type" style="color: #ec4899;">
                                {{ $firstVariant ? $firstVariant->type : 'Tiêu chuẩn' }}
                            </strong>
                        </label>

                        @if($product->variants->isNotEmpty())
                        <div class="variant-swatch-list" id="variant-swatches">
                            @foreach($product->variants as $index => $variant)
                            @php
                            $isFirst = $index === 0;
                            $vPrice = $variant->effective_price;
                            $vImg = $variant->image_url ?: $product->first_image_url;
                            $vSku = $variant->sku ?: ($product->sku ?: 'LIP-' . str_pad($product->id, 5, '0', STR_PAD_LEFT));
                            @endphp
                            <div class="variant-swatch-item {{ $isFirst ? 'active' : '' }}"
                                data-variant-id="{{ $variant->id }}"
                                data-type="{{ $variant->type }}"
                                data-price="{{ $vPrice }}"
                                data-price-formatted="{{ number_format($vPrice, 0, ',', '.') }} ₫"
                                data-image="{{ $vImg }}"
                                data-sku="{{ $vSku }}"
                                data-stock="{{ $variant->stock }}"
                                data-hex="{{ $variant->hex_color ?? '#ec4899' }}"
                                data-color-desc="{{ $variant->color_description ?? '' }}">
                                @if($variant->image_url)
                                <img src="{{ $variant->image_url }}" alt="{{ $variant->type }}" class="variant-swatch-img">
                                @elseif($variant->hex_color)
                                <div class="variant-color-dot" style="background-color: {{ $variant->hex_color }};"></div>
                                @else
                                <i class="bi bi-tag text-rose"></i>
                                @endif
                                <div class="text-start">
                                    <span class="fs-8 fw-semibold text-dark d-block">{{ $variant->type }}</span>
                                    @if($variant->color_description)
                                    <small class="text-muted fs-9 d-block text-truncate" style="max-width: 130px;" title="{{ $variant->color_description }}">{{ $variant->color_description }}</small>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-muted fs-8 fst-italic">Sản phẩm phiên bản tiêu chuẩn.</div>
                        @endif
                    </div>

                    <!-- Chọn số lượng & Trạng thái hàng -->
                    <div class="mb-4">
                        <label class="form-label fs-7 fw-semibold mb-2" style="color: #0f172a;">Số lượng</label>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="quantity-control">
                                <button type="button" class="btn-qty" id="btn-qty-minus">-</button>
                                <input type="number" id="input-quantity" value="1" min="1" max="{{ max(1, $defaultStock) }}" class="input-qty" readonly>
                                <button type="button" class="btn-qty" id="btn-qty-plus">+</button>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span id="current-stock-status" class="badge {{ $defaultStock > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-2.5 py-1.5 rounded-pill fs-8 fw-semibold">
                                    <i class="bi {{ $defaultStock > 0 ? 'bi-check-circle-fill' : 'bi-slash-circle' }} me-1"></i>
                                    <span id="current-stock-status-text">{{ $defaultStock > 0 ? 'Còn hàng' : 'Hết hàng' }}</span>
                                </span>
                                <span class="fs-8 {{ $defaultStock > 0 ? '' : 'd-none' }}" id="stock-count-wrapper" style="color: #475569;">
                                    (Còn <strong class="text-dark" id="current-stock-count">{{ $defaultStock }}</strong> sản phẩm)
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Nút Thêm vào giỏ & Mua ngay -->
                    <form id="add-to-cart-form" class="mb-4">
                        <input type="hidden" name="product_id" id="form-product-id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" id="form-variant-id" value="{{ $firstVariant ? $firstVariant->id : '' }}">
                        <input type="hidden" name="quantity" id="form-quantity" value="1">

                        <div class="d-flex flex-column flex-sm-row gap-3 align-items-stretch">
                            <button type="button" class="btn-add-cart-main flex-grow-1 {{ $defaultStock <= 0 ? 'disabled' : '' }}" id="btn-add-cart" @disabled($defaultStock <= 0)>
                                <i class="bi {{ $defaultStock <= 0 ? 'bi-slash-circle' : 'bi-cart-plus-fill' }} fs-5" id="btn-add-cart-icon"></i>
                                <span id="btn-add-cart-text">{{ $defaultStock <= 0 ? 'Tạm Hết Hàng' : 'Thêm Vào Giỏ Hàng' }}</span>
                            </button>
                            <button type="button" class="btn-buy-now-main flex-grow-1 {{ $defaultStock <= 0 ? 'disabled' : '' }}" id="btn-buy-now" @disabled($defaultStock <= 0)>
                                <i class="bi {{ $defaultStock <= 0 ? 'bi-slash-circle' : 'bi-lightning-charge-fill' }} fs-5" id="btn-buy-now-icon"></i>
                                <span>Mua Ngay</span>
                            </button>
                            @php
                                $isFav = Auth::check() && $product->isFavoritedBy(Auth::user());
                            @endphp
                            <button type="button" 
                                    class="btn rounded-3 d-flex align-items-center justify-content-center px-3 btn-wishlist-toggle {{ $isFav ? 'active favorited' : '' }}" 
                                    style="border: 2px solid #ec4899; color: {{ $isFav ? '#ffffff' : '#ec4899' }}; background: {{ $isFav ? '#e11d48' : '#fff0f5' }}; min-width: 48px;"
                                    data-wishlist-product-id="{{ $product->id }}"
                                    onclick="window.toggleWishlist({{ $product->id }}, this);"
                                    title="{{ $isFav ? 'Bỏ khỏi yêu thích' : 'Lưu vào yêu thích' }}">
                                <i class="bi {{ $isFav ? 'bi-heart-fill' : 'bi-heart' }} fs-5"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Cam kết chất lượng chuẩn Cocolux -->
                    <div class="row g-2 pt-3 border-top border-secondary fs-8" style="color: #334155; font-weight: 500;">
                        <div class="col-sm-4 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                            <span>Chính hãng 100%</span>
                        </div>
                        <div class="col-sm-4 d-flex align-items-center gap-2">
                            <i class="bi bi-truck text-info fs-5"></i>
                            <span>Giao nhanh toàn quốc</span>
                        </div>
                        <div class="col-sm-4 d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-counterclockwise text-warning fs-5"></i>
                            <span>Đổi trả trong 7 ngày</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- PHẦN 2: Thông tin chi tiết & Tabs mô tả -->
    <div class="cocolux-card mb-5">
        <!-- Header Thông tin sản phẩm -->
        <div class="p-4 border-bottom border-secondary" style="background-color: #fdf2f8;">
            <h4 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text" style="color: #ec4899;"></i>
                <span>Thông tin sản phẩm</span>
            </h4>
        </div>

        <!-- Bảng thông số chi tiết (Thương hiệu, Xuất xứ, Công dụng, Thành phần) -->
        <div class="table-responsive">
            <table class="table specs-table mb-0 align-middle">
                <tbody>
                    <tr>
                        <th>Danh mục sản phẩm</th>
                        <td>
                            @if($product->variants->first()?->category)
                            <span class="badge bg-light border border-secondary text-dark px-3 py-1.5 fs-8">
                                {{ $product->variants->first()->category->name }}
                            </span>
                            @else
                            <span>Son Môi Cao Cấp</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Thương hiệu</th>
                        <td>
                            <strong class="text-dark">{{ $product->brand?->name ?? 'LuxeLips Cosmetics' }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <th>Xuất xứ thương hiệu</th>
                        <td>
                            @if($product->brand?->origin)
                            <span>{{ $product->brand->origin }}</span>
                            @else
                            <span>Chính hãng phân phối</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Hệ thống Tabs (Mô tả sản phẩm, Công dụng, Thành phần, Cách dùng) -->
        <div class="border-top border-secondary mt-3">
            <ul class="nav cocolux-tabs px-4 border-bottom border-secondary" id="productDetailTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-description" type="button">
                        Mô tả sản phẩm
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-benefits" type="button">
                        Công dụng nổi bật
                    </button>
                </li>
                @if($product->ingredients)
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ingredients" type="button">
                        Thành phần / Chất liệu
                    </button>
                </li>
                @endif
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-howtouse" type="button">
                        Cách dùng chuẩn đẹp
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-reviews" type="button">
                        Đánh giá từ khách hàng
                    </button>
                </li>
            </ul>

            <div class="tab-content p-4 p-lg-5" id="productDetailTabsContent">
                <!-- Tab 1: Mô tả chi tiết -->
                <div class="tab-pane fade show active" id="tab-description">
                    <h5 class="fw-bold text-dark mb-3">Mô tả sản phẩm</h5>
                    @if($product->description)
                    <div class="text-secondary fs-7" style="line-height: 1.8; white-space: pre-line;">
                        {{ $product->description }}
                    </div>
                    @else
                    <p class="text-secondary fs-7" style="line-height: 1.8;">
                        Dòng son môi cao cấp <strong>{{ $product->name }}</strong> sở hữu kết cấu siêu nhẹ môi, khả năng giữ màu bền đẹp lên tới 8 giờ đồng hồ mà vẫn đảm bảo độ ẩm mượt dịu nhẹ. Từng sắc son được nghiên cứu kỹ lưỡng để phù hợp nhất với làn da châu Á, giúp nâng tone da và tôn trọn nét đẹp quyến rũ tự tin.
                    </p>
                    @endif
                </div>

                <!-- Tab 2: Công dụng -->
                <div class="tab-pane fade" id="tab-benefits">
                    <h5 class="fw-bold text-dark mb-3">Công dụng vượt trội</h5>
                    @if($product->uses)
                    <div class="text-secondary fs-7" style="line-height: 1.8; white-space: pre-line;">
                        {{ $product->uses }}
                    </div>
                    @else
                    <ul class="text-secondary fs-7 ps-3" style="line-height: 2;">
                        <li>Lên màu chuẩn sắc ngay từ lần chạm cọ đầu tiên, che phủ hoàn hảo rãnh môi.</li>
                        <li>Bổ sung tinh chất dưỡng ẩm từ bơ hạt mỡ và Vitamin E giúp đôi môi luôn căng mọng.</li>
                        <li>Độ bám màu bền bỉ suốt ngày dài, không lem trôi khi ăn uống nhẹ.</li>
                        <li>Hương thơm ngọt ngào tự nhiên, dễ chịu, không gây kích ứng môi nhạy cảm.</li>
                    </ul>
                    @endif
                </div>

                @if($product->ingredients)
                <!-- Tab Thành phần / Chất liệu -->
                <div class="tab-pane fade" id="tab-ingredients">
                    <h5 class="fw-bold text-dark mb-3">Thành phần / Chất liệu</h5>
                    <div class="text-secondary fs-7" style="line-height: 1.8; white-space: pre-line;">
                        {{ $product->ingredients }}
                    </div>
                </div>
                @endif

                <!-- Tab 3: Cách dùng -->
                <div class="tab-pane fade" id="tab-howtouse">
                    <h5 class="fw-bold text-dark mb-3">Hướng dẫn thoa son</h5>
                    <ol class="text-secondary fs-7 ps-3" style="line-height: 2;">
                        <li>Tẩy tế bào chết định kỳ và thoa một lớp son dưỡng mỏng trước khi đánh son.</li>
                        <li><strong>Đánh lòng môi</strong>: Chấm 2-3 điểm son ở giữa lòng môi trên và dưới, dùng đầu ngón tay hoặc cọ tán nhẹ ra viền môi.</li>
                        <li><strong>Đánh full môi</strong>: Thoa đều son từ lòng môi ra khóe môi, viền môi cẩn thận để tạo độ sắc nét và quyến rũ.</li>
                    </ol>
                </div>

                <!-- Tab 4: Đánh giá -->
                <div class="tab-pane fade" id="tab-reviews">
                    <div class="d-flex align-items-center gap-3 mb-4 p-3.5 rounded-3" style="background-color: #fdf2f8;">
                        <div class="display-5 fw-bold text-warning">5.0</div>
                        <div>
                            <div class="text-warning fs-6 mb-1">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <span class="fs-8 text-muted">100% Khách hàng hài lòng với chất lượng màu son và độ dưỡng ẩm.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PHẦN 3: Sản phẩm liên quan / Tương tự -->
    @if($relatedProducts->isNotEmpty())
    <div class="mb-5">
        <h3 class="h4 fw-bold text-dark mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-heart-fill" style="color: #ec4899;"></i>
            <span>Sản Phẩm Tương Tự Có Thể Bạn Thích</span>
        </h3>

        <div class="row g-4">
            @foreach($relatedProducts as $rel)
            <div class="col-md-6 col-lg-3">
                <div class="cocolux-card h-100 p-3 d-flex flex-column" style="transition: transform 0.3s ease;">
                    <a href="{{ route('products.show', $rel->slug ?? $rel->id) }}" class="text-decoration-none">
                        <div class="rounded-3 overflow-hidden mb-3" style="height: 200px; background-color: #fdf2f8;">
                            @if($rel->first_image_url)
                            <img src="{{ $rel->first_image_url }}" alt="{{ $rel->name }}" class="w-100 h-100 object-fit-cover">
                            @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                <i class="bi bi-image fs-1"></i>
                            </div>
                            @endif
                        </div>
                        <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $rel->name }}</h6>
                        <p class="fs-8 text-muted mb-2 text-truncate">{{ $rel->description ?: 'Son môi cao cấp' }}</p>
                    </a>
                    <div class="mt-auto pt-2 border-top border-secondary d-flex justify-content-between align-items-center">
                        <strong class="text-warning fs-6">{{ $rel->price_range_formatted }}</strong>
                        <a href="{{ route('products.show', $rel->slug ?? $rel->id) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1 fs-9" style="border-color: rgba(236, 72, 153, 0.4); color: #0f172a;">
                            Xem ngay
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/user/pages/product-detail.js') }}"></script>
@endpush
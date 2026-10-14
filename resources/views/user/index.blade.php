@extends('user.layouts.app')

@section('title', 'LuxeLips PRO | Thời Trang Son Môi Cao Cấp')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/pages/home.css') }}">
@endpush

@section('content')
<!-- Hero Banner -->
<section class="hero-banner text-center">
    <div class="container">
        <span class="badge rounded-pill px-3 py-2 mb-3 shadow-sm" style="background: linear-gradient(135deg, #ec4899, #e11d48); color: #ffffff !important; font-weight: 600; letter-spacing: 0.02em;">
            Bộ Sưu Tập Son Môi 2026
        </span>
        <h1 class="display-4 fw-bold text-dark mb-3 brand-font">Tôn Vinh Vẻ Đẹp Quyến Rũ</h1>
        <p class="lead mx-auto mb-4 text-muted" style="max-width: 650px; font-size: 1.1rem;">
            Khám phá thế giới son môi thời thượng với sắc màu chuẩn mịn, lâu trôi và chất dưỡng mềm mại tôn vinh thần thái của bạn.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('collection.index') }}" class="btn-add-cart px-4 py-2 text-decoration-none rounded-pill fs-6">
                <i class="bi bi-grid me-1"></i> Khám Phá Bộ Sưu Tập
            </a>
        </div>
    </div>
</section>

<!-- Cam Kết Dịch Vụ -->
<div class="container mb-5">
    <div class="py-4 border-top border-bottom border-secondary border-opacity-25">
        <div class="row g-0 justify-content-center align-items-center">
            <div class="col-6 col-lg-3 d-flex align-items-center justify-content-center py-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(16, 185, 129, 0.15); color: #10b981; width: 44px; height: 44px;">
                        <i class="bi bi-patch-check-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-dark fw-bold mb-0 fs-7">100% Chính Hãng</h6>
                        <span class="fs-9 text-muted">Cam kết chất lượng cao</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 d-flex align-items-center justify-content-center py-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; width: 44px; height: 44px;">
                        <i class="bi bi-truck fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-dark fw-bold mb-0 fs-7">Giao Nhanh GHN</h6>
                        <span class="fs-9 text-muted">Theo dõi hành trình dễ dàng</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 d-flex align-items-center justify-content-center py-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; width: 44px; height: 44px;">
                        <i class="bi bi-arrow-repeat fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-dark fw-bold mb-0 fs-7">Đổi Trả 7 Ngày</h6>
                        <span class="fs-9 text-muted">Thủ tục nhanh chóng</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 d-flex align-items-center justify-content-center py-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(236, 72, 153, 0.15); color: #ec4899; width: 44px; height: 44px;">
                        <i class="bi bi-headset fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-dark fw-bold mb-0 fs-7">Hỗ Trợ 24/7</h6>
                        <span class="fs-9 text-muted">Tư vấn màu son tận tâm</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KHỐI 1: Sản Phẩm Nổi Bật -->
@if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
<section class="py-4" id="featured-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 fw-bold text-dark mb-1 brand-font d-flex align-items-center gap-2">
                    <span>Sản Phẩm Nổi Bật</span>
                </h2>
            </div>
        </div>

        <div class="row g-4">
            @foreach($featuredProducts as $product)
            @php
                $isOutOfStock = ($product->total_stock <= 0);
            @endphp
            <div class="col-md-6 col-lg-3">
                <div class="card-product h-100 p-3 d-flex flex-column {{ $isOutOfStock ? 'is-out-of-stock' : '' }}">
                    <!-- Floating Wishlist Button -->
                    @php
                        $isFav = Auth::check() && $product->isFavoritedBy(Auth::user());
                    @endphp
                    <button type="button" 
                            class="btn btn-sm rounded-circle position-absolute top-0 end-0 m-2 z-3 shadow-sm border-0 d-flex align-items-center justify-content-center btn-wishlist-toggle {{ $isFav ? 'active favorited' : '' }}" 
                            style="width: 34px; height: 34px; background: rgba(255, 255, 255, 0.92); backdrop-filter: blur(4px);"
                            data-wishlist-product-id="{{ $product->id }}"
                            onclick="event.preventDefault(); event.stopPropagation(); window.toggleWishlist({{ $product->id }}, this);"
                            title="{{ $isFav ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích' }}">
                        <i class="bi {{ $isFav ? 'bi-heart-fill text-danger' : 'bi-heart text-muted' }} fs-6"></i>
                    </button>

                    <!-- Image Wrapper linking to detail -->
                    <a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="text-decoration-none">
                        <div class="product-img-wrapper mb-3">
                            @if($product->first_image_url)
                            <img src="{{ $product->first_image_url }}" alt="{{ $product->name }}">
                            @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary">
                                <i class="bi bi-image fs-1"></i>
                            </div>
                            @endif
                            @if($isOutOfStock)
                            <span class="position-absolute top-0 start-0 badge m-2 rounded-pill badge-out-of-stock-home text-white shadow-sm">
                                <i class="bi bi-slash-circle me-1"></i>Hết hàng
                            </span>
                            @else
                            <span class="position-absolute top-0 start-0 badge m-2 rounded-pill bg-danger shadow-sm">
                                <i class="bi bi-fire me-1"></i>Nổi bật
                            </span>
                            @endif
                        </div>
                    </a>

                    <!-- Product Info linking to detail -->
                    <a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="text-decoration-none">
                        <h5 class="fw-bold text-dark fs-6 mb-1 brand-font text-truncate" title="{{ $product->name }}">{{ $product->name }}</h5>
                        <p class="fs-8 mb-3 text-truncate text-muted" style="font-size: 0.82rem;">
                            {{ $product->description ?: 'Son môi cao cấp dưỡng ẩm mịn mượt' }}
                        </p>
                    </a>

                    <!-- Price & Action Button -->
                    <div class="pt-3 border-top border-secondary mt-auto">
                        <div class="d-flex align-items-baseline justify-content-between mb-2">
                            <span class="fs-9" style="font-size: 0.75rem; color: #0f172a; font-weight: 600;">Giá bán:</span>
                            <strong class="fs-6 text-end text-warning">{{ $product->price_range_formatted }}</strong>
                        </div>

                        <a href="{{ route('products.show', $product->slug ?? $product->id) }}"
                            class="btn-add-cart w-100 d-flex align-items-center justify-content-center gap-1.5 py-2 text-decoration-none" style="white-space: nowrap;">
                            <i class="bi bi-eye fs-6"></i>
                            <span>Xem chi tiết</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- KHỐI 2: Sản Phẩm Mới Nhất -->
<section class="py-5" id="products-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 fw-bold text-dark mb-1 brand-font">Sản Phẩm Mới Nhất</h2>
            </div>
            <a href="{{ route('collection.index') }}" class="text-decoration-none fs-8 fw-semibold d-none d-sm-inline" style="color: #ec4899;">
                Xem tất cả <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($latestProducts as $product)
            @php
            $firstCategory = $product->variants->first()?->category;
            $catId = $firstCategory ? $firstCategory->id : 0;
            $isOutOfStock = ($product->total_stock <= 0);
            @endphp
            <div class="col-md-6 col-lg-3 product-item-col" data-category-id="{{ $catId }}">
                <div class="card-product h-100 p-3 d-flex flex-column {{ $isOutOfStock ? 'is-out-of-stock' : '' }}">

                    <!-- Floating Wishlist Button -->
                    @php
                        $isFav = Auth::check() && $product->isFavoritedBy(Auth::user());
                    @endphp
                    <button type="button" 
                            class="btn btn-sm rounded-circle position-absolute top-0 end-0 m-2 z-3 shadow-sm border-0 d-flex align-items-center justify-content-center btn-wishlist-toggle {{ $isFav ? 'active favorited' : '' }}" 
                            style="width: 34px; height: 34px; background: rgba(255, 255, 255, 0.92); backdrop-filter: blur(4px);"
                            data-wishlist-product-id="{{ $product->id }}"
                            onclick="event.preventDefault(); event.stopPropagation(); window.toggleWishlist({{ $product->id }}, this);"
                            title="{{ $isFav ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích' }}">
                        <i class="bi {{ $isFav ? 'bi-heart-fill text-danger' : 'bi-heart text-muted' }} fs-6"></i>
                    </button>

                    <!-- Image Wrapper linking to detail -->
                    <a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="text-decoration-none">
                        <div class="product-img-wrapper mb-3">
                            @if($product->first_image_url)
                            <img src="{{ $product->first_image_url }}" alt="{{ $product->name }}">
                            @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary">
                                <i class="bi bi-image fs-1"></i>
                            </div>
                            @endif
                            
                            @if($isOutOfStock)
                            <span class="position-absolute top-0 start-0 badge m-2 rounded-pill badge-out-of-stock-home text-white shadow-sm">
                                <i class="bi bi-slash-circle me-1"></i>Hết hàng
                            </span>
                            @else
                            <span class="position-absolute top-0 start-0 badge m-2 rounded-pill shadow-sm" style="background: rgba(236,72,153,0.92); color: #ffffff !important; backdrop-filter: blur(4px); font-weight: 600;">
                                {{ $product->variants->count() }} màu sắc
                            </span>
                            @endif
                        </div>
                    </a>

                    <!-- Product Info linking to detail -->
                    <a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="text-decoration-none">
                        <h5 class="fw-bold text-dark fs-6 mb-1 brand-font text-truncate" title="{{ $product->name }}">{{ $product->name }}</h5>
                        <p class="fs-8 mb-3 text-truncate text-muted" style="font-size: 0.82rem;">
                            {{ $product->description ?: 'Son môi cao cấp dưỡng ẩm mịn mượt' }}
                        </p>
                    </a>

                    <!-- Price & Action Button -->
                    <div class="pt-3 border-top border-secondary mt-auto">
                        <div class="d-flex align-items-baseline justify-content-between mb-2">
                            <span class="fs-9" style="font-size: 0.75rem; color: #0f172a; font-weight: 600;">Giá bán:</span>
                            <strong class="fs-6 text-end text-warning">{{ $product->price_range_formatted }}</strong>
                        </div>

                        <a href="{{ route('products.show', $product->slug ?? $product->id) }}"
                            class="btn-add-cart w-100 d-flex align-items-center justify-content-center gap-1.5 py-2 text-decoration-none" style="white-space: nowrap;">
                            <i class="bi bi-eye fs-6"></i>
                            <span>Xem chi tiết</span>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-bag-x fs-1 d-block mb-2" style="color: #ec4899;"></i>
                <span class="fs-6 fw-medium">Chưa có sản phẩm nào.</span>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
@extends('user.layouts.app')

@section('title', 'Bộ Sưu Tập Mỹ Phẩm & Son Môi Cao Cấp | LuxeLips PRO')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/pages/collection.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0 fs-8">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="bi bi-house-door me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Bộ Sưu Tập Sản Phẩm</li>
            @if(request('q'))
            <li class="breadcrumb-item active" aria-current="page">Tìm kiếm: "{{ request('q') }}"</li>
            @endif
        </ol>
    </nav>

    <div class="row g-4">
        <!-- CỘT TRÁI: BỘ LỌC (CHUẨN COCOLUX) -->
        <div class="col-lg-3">
            <form action="{{ route('collection.index') }}" method="GET" id="filterForm" class="filter-sidebar">
                @if(request('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <!-- Khoảng giá -->
                <div class="mb-4">
                    <div class="filter-title">
                        <span><i class="bi bi-cash-stack me-1.5" style="color: #ec4899;"></i> Khoảng giá</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" class="filter-input w-50" placeholder="Từ giá" min="0" step="5000">
                        <span class="text-muted">-</span>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" class="filter-input w-50" placeholder="Đến giá" min="0" step="5000">
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <button type="submit" class="btn-apply-filter">
                            Áp dụng
                        </button>
                        <a href="{{ route('collection.index') }}" class="btn-reset-filter">
                            Xóa tất cả
                        </a>
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <!-- Danh mục sản phẩm -->
                <div class="mb-4">
                    <div class="filter-title">
                        <span><i class="bi bi-tag me-1.5" style="color: #ec4899;"></i> Danh mục</span>
                    </div>
                    <div class="d-flex flex-column gap-1">
                        <a href="{{ route('collection.index', array_merge(request()->except('category', 'page'), ['category' => 'all'])) }}"
                            class="filter-item-label {{ !request('category') || request('category') === 'all' ? 'active' : '' }}">
                            <span>Tất cả danh mục</span>
                            <span class="badge rounded-pill bg-light border border-secondary text-dark">{{ $products->total() }}</span>
                        </a>
                        @foreach($categories as $category)
                        <a href="{{ route('collection.index', array_merge(request()->except('category', 'page'), ['category' => $category->id])) }}"
                            class="filter-item-label {{ request('category') == $category->id ? 'active' : '' }}">
                            <span class="text-truncate">{{ $category->name }}</span>
                            <span class="badge rounded-pill bg-light border border-secondary text-dark">{{ $category->products_count ?? $category->variants_count ?? 0 }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <!-- Trạng thái hàng -->
                <div>
                    <div class="filter-title">
                        <span><i class="bi bi-check2-circle me-1.5" style="color: #ec4899;"></i> Trạng thái</span>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="inStockCheck" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label text-dark fs-8" for="inStockCheck">
                            Chỉ hiện còn hàng
                        </label>
                    </div>
                </div>
            </form>
        </div>

        <!-- CỘT PHẢI: THANH SẮP XẾP & LƯỚI SẢN PHẨM -->
        <div class="col-lg-9">
            <!-- THANH SẮP XẾP -->
            <div class="sort-toolbar">
                <span class="text-muted me-2 d-inline-flex align-items-center" style="font-size: 0.875rem; font-weight: 500;"><i class="bi bi-sort-down me-1"></i>Sắp xếp:</span>

                @php
                $currentSort = request('sort', 'popular');
                @endphp

                <a href="{{ route('collection.index', array_merge(request()->except('sort', 'page'), ['sort' => 'popular'])) }}"
                    class="sort-btn {{ $currentSort === 'popular' ? 'active' : '' }}">
                    Phổ biến
                </a>

                <a href="{{ route('collection.index', array_merge(request()->except('sort', 'page'), ['sort' => 'latest'])) }}"
                    class="sort-btn {{ $currentSort === 'latest' ? 'active' : '' }}">
                    Mới nhất
                </a>

                <a href="{{ route('collection.index', array_merge(request()->except('sort', 'page'), ['sort' => 'best_seller'])) }}"
                    class="sort-btn {{ $currentSort === 'best_seller' ? 'active' : '' }}">
                    Bán chạy
                </a>

                <a href="{{ route('collection.index', array_merge(request()->except('sort', 'page'), ['sort' => 'price_asc'])) }}"
                    class="sort-btn {{ $currentSort === 'price_asc' ? 'active' : '' }}">
                    Giá <i class="bi bi-arrow-up"></i>
                </a>

                <a href="{{ route('collection.index', array_merge(request()->except('sort', 'page'), ['sort' => 'price_desc'])) }}"
                    class="sort-btn {{ $currentSort === 'price_desc' ? 'active' : '' }}">
                    Giá <i class="bi bi-arrow-down"></i>
                </a>

                <span class="ms-auto text-muted fs-8">
                    Tìm thấy <strong class="text-dark">{{ $products->total() }}</strong> sản phẩm
                </span>
            </div>

            <!-- LƯỚI SẢN PHẨM (CARD COCOLUX STYLE) -->
            @if($products->isEmpty())
            <div class="text-center py-5 filter-sidebar">
                <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-dark fw-bold mb-2">Không tìm thấy sản phẩm nào</h5>
                <p class="text-muted fs-8 mb-4">Hãy thử điều chỉnh khoảng giá hoặc chọn danh mục khác để xem kết quả.</p>
                <a href="{{ route('collection.index') }}" class="btn btn-apply-filter px-4 py-2 rounded-pill fs-8 text-decoration-none d-inline-block" style="width: auto;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Xóa tất cả bộ lọc
                </a>
            </div>
            @else
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-lg-4 mb-4">
                @foreach($products as $product)
                @php
                $firstVariant = $product->variants->first();
                $thumbImage = $product->first_image_url ?? ($firstVariant ? $firstVariant->image_url : null) ?? 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=600&q=80';
                $categoryName = $product->variants->first()?->category?->name ?? 'LUXELIPS';
                $minVariantPrice = $product->variants->min('effective_price') ?: $product->original_price;
                $isOutOfStock = ($product->total_stock <= 0);
                @endphp
                <div class="col">
                    <div class="cocolux-card {{ $isOutOfStock ? 'is-out-of-stock' : '' }} position-relative">
                        <!-- Floating Wishlist Button -->
                        @php
                            $isFav = Auth::check() && $product->isFavoritedBy(Auth::user());
                        @endphp
                        <button type="button" 
                                class="btn btn-sm rounded-circle shadow-sm border-0 d-flex align-items-center justify-content-center btn-wishlist-toggle {{ $isFav ? 'active favorited' : '' }}" 
                                data-wishlist-product-id="{{ $product->id }}"
                                onclick="event.preventDefault(); event.stopPropagation(); window.toggleWishlist({{ $product->id }}, this);"
                                title="{{ $isFav ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích' }}">
                            <i class="bi {{ $isFav ? 'bi-heart-fill text-danger' : 'bi-heart text-muted' }} fs-6" style="pointer-events: none;"></i>
                        </button>

                        <!-- Image Wrap linking to detail -->
                        <a href="{{ route('products.show', $product->slug ?: $product->id) }}" class="cocolux-card-img-wrap text-decoration-none">
                            @if($isOutOfStock)
                            <span class="badge-out-of-stock"><i class="bi bi-slash-circle me-1"></i>HẾT HÀNG</span>
                            @else
                            <span class="badge-new-arrival">HÀNG MỚI</span>
                            @endif
                            @if($product->variants->count() > 1)
                            <span class="badge-hot-tag">{{ $product->variants->count() }} màu</span>
                            @endif
                            <img src="{{ $thumbImage }}" alt="{{ $product->name }}" class="cocolux-card-img" loading="lazy">
                        </a>

                        <!-- Content -->
                        <div class="cocolux-card-body">
                            <span class="cocolux-brand">{{ $categoryName }}</span>
                            <a href="{{ route('products.show', $product->slug ?: $product->id) }}" class="text-decoration-none">
                                <h6 class="cocolux-title" title="{{ $product->name }}">{{ $product->name }}</h6>
                            </a>

                            <div class="d-flex align-items-baseline mb-2">
                                <span class="cocolux-price">{{ number_format($minVariantPrice, 0, ',', '.') }}₫</span>
                                @if($product->original_price > $minVariantPrice)
                                <span class="cocolux-old-price">{{ number_format($product->original_price, 0, ',', '.') }}₫</span>
                                @endif
                            </div>

                            <div class="cocolux-meta">
                                @if($isOutOfStock)
                                <span class="text-danger fw-semibold">
                                    <i class="bi bi-x-circle me-1"></i>Hết hàng
                                </span>
                                @else
                                <span class="text-warning">
                                    <i class="bi bi-star-fill"></i> 5.0
                                </span>
                                @endif
                                <span>
                                    {{ $product->variants->count() }} biến thể
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
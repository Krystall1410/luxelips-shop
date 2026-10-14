@extends('user.layouts.app')

@section('title', 'Sản Phẩm Yêu Thích | LuxeLips PRO')

@section('content')
<div class="py-4 py-md-5" style="background: linear-gradient(180deg, #fff5f7 0%, #ffffff 100%);">
    <div class="container">
        <!-- Breadcrumb & Header -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb fs-8">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Danh sách yêu thích</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1 brand-font d-flex align-items-center gap-2">
                    <i class="bi bi-heart-fill text-danger"></i>
                    Sản Phẩm Yêu Thích
                </h1>
                <p class="text-muted fs-7 mb-0">Các dòng son môi bạn đã lưu để xem và đặt mua sau</p>
            </div>
            <div class="mt-2 mt-md-0">
                <span class="badge rounded-pill px-3 py-2 fs-7" style="background: rgba(236, 72, 153, 0.12); color: #ec4899; font-weight: 600;">
                    <span id="wishlist-total-count">{{ $wishlists->total() }}</span> sản phẩm đã lưu
                </span>
            </div>
        </div>

        @if($wishlists->isEmpty())
        <!-- Empty State -->
        <div class="text-center py-5 my-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 90px; height: 90px; background: #fff0f3;">
                <i class="bi bi-heartbreak fs-1 text-danger"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Danh sách yêu thích đang trống</h4>
            <p class="text-muted fs-7 mx-auto mb-4" style="max-width: 460px;">
                Bạn chưa lưu thỏi son nào vào danh sách. Hãy dạo quanh bộ sưu tập son cao cấp của LuxeLips và bấm vào biểu tượng trái tim để lưu lại nhé!
            </p>
            <a href="{{ route('collection.index') }}" class="btn px-4 py-2 text-white fw-semibold rounded-pill shadow-sm" style="background: linear-gradient(135deg, #ec4899, #e11d48);">
                <i class="bi bi-stars me-1"></i> Khám Phá Bộ Sưu Tập Ngay
            </a>
        </div>
        @else
        <!-- Wishlist Grid -->
        <div class="row g-4" id="wishlist-grid">
            @foreach($wishlists as $item)
                @php
                    $product = $item->product;
                    if (!$product) continue;
                    $isOutOfStock = ($product->total_stock <= 0);
                @endphp
                <div class="col-sm-6 col-md-4 col-lg-3 wishlist-item-col" id="wishlist-item-{{ $product->id }}">
                    <div class="card-product h-100 p-3 d-flex flex-column position-relative {{ $isOutOfStock ? 'is-out-of-stock' : '' }}">
                        <!-- Remove button -->
                        <form action="{{ route('wishlist.destroy', $product->id) }}" method="POST" class="position-absolute top-0 end-0 m-3 z-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm border-0" 
                                    style="width: 32px; height: 32px; background: rgba(255, 255, 255, 0.9); color: #dc2626;" 
                                    title="Xóa khỏi yêu thích"
                                    onclick="return confirm('Bạn có chắc muốn bỏ sản phẩm này khỏi danh sách yêu thích?');">
                                <i class="bi bi-x-lg fs-7"></i>
                            </button>
                        </form>

                        <!-- Product Image Link -->
                        <a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="text-decoration-none">
                            <div class="product-img-wrapper mb-3">
                                @if($product->first_image_url)
                                    <img src="{{ $product->first_image_url }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                        <i class="bi bi-image fs-1"></i>
                                    </div>
                                @endif
                                
                                @if($isOutOfStock)
                                    <span class="badge bg-secondary position-absolute top-0 start-0 m-2">Hết hàng</span>
                                @elseif($product->brand)
                                    <span class="badge position-absolute top-0 start-0 m-2" style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px);">
                                        {{ $product->brand->name }}
                                    </span>
                                @endif
                            </div>
                        </a>

                        <!-- Product Info -->
                        <div class="d-flex flex-column flex-grow-1">
                            <div class="mb-1">
                                <span class="fs-9 text-uppercase text-muted fw-semibold">{{ $product->brand?->name ?? 'LuxeLips' }}</span>
                            </div>
                            <h5 class="fw-bold text-dark fs-6 mb-2 text-truncate" title="{{ $product->name }}">
                                <a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="text-decoration-none text-dark hover-rose">
                                    {{ $product->name }}
                                </a>
                            </h5>
                            
                            <div class="mt-auto pt-2">
                                <div class="fw-bold fs-6 mb-3" style="color: #e11d48;">
                                    {{ $product->price_range_formatted }}
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="btn btn-sm text-white fw-semibold py-2 rounded-3 shadow-sm" style="background: linear-gradient(135deg, #ec4899, #e11d48);">
                                        <i class="bi bi-eye-fill me-1"></i> Xem Chi Tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $wishlists->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

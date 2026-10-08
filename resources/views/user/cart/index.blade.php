@extends('user.layouts.app')

@section('title', 'Giỏ Hàng Của Bạn | LuxeLips PRO')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/pages/cart.css') }}">
@endpush

@section('content')
<div class="container py-5" id="cart-container"
    data-update-url="{{ route('cart.update') }}"
    data-remove-url="{{ route('cart.remove') }}">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 brand-font">Giỏ Hàng Của Bạn</h1>
            <p class="fs-7 mb-0 text-muted">Chọn các sản phẩm bạn muốn tiến hành thanh toán</p>
        </div>
        <span class="badge bg-danger fs-7 px-3 py-2 rounded-pill">
            {{ count($cart) }} Sản phẩm trong giỏ
        </span>
    </div>

    @if(count($cart) > 0)
    <div class="row g-4">
        <!-- Left Side: Products Table with Checkboxes -->
        <div class="col-lg-8">
            <div class="cart-card p-3 p-md-4">
                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 36px;">
                                    <input class="form-check-input" type="checkbox" id="select-all-checkbox" checked title="Chọn tất cả">
                                </th>
                                <th>Sản Phẩm</th>
                                <th style="width: 95px;">Đơn Giá</th>
                                <th style="width: 110px;" class="text-center">Số Lượng</th>
                                <th style="width: 110px;" class="text-end">Thành Tiền</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $id => $item)
                            @php
                                $isOutOfStock = (($item['stock'] ?? 1) <= 0);
                            @endphp
                            <tr data-id="{{ $id }}" data-price="{{ $item['price'] }}" class="{{ $isOutOfStock ? 'opacity-75' : '' }}">
                                <td>
                                    @if($isOutOfStock)
                                        <input class="form-check-input item-checkbox" type="checkbox" value="{{ $id }}" disabled title="Sản phẩm hiện đã hết hàng">
                                    @else
                                        <input class="form-check-input item-checkbox" type="checkbox" value="{{ $id }}" checked>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-3 overflow-hidden bg-light flex-shrink-0" style="width: 54px; height: 54px;">
                                            @if(isset($item['image']) && $item['image'])
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-100 h-100 object-fit-cover {{ $isOutOfStock ? 'opacity-50' : '' }}">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6" style="font-size: 0.9rem !important;">{{ $item['name'] }}</h6>
                                            @if(!empty($item['variant_name']))
                                                <div class="mb-1">
                                                    <span class="badge px-2 py-0.5 text-wrap text-start" style="background: rgba(236,72,153,0.18); color: #db2777; border: 1px solid rgba(236,72,153,0.3); font-size: 0.73rem; max-width: 250px; line-height: 1.3;">
                                                        <i class="bi bi-palette2 me-1"></i>Phân loại: {{ $item['variant_name'] }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                <span class="badge bg-light border border-secondary fs-9 text-muted">{{ $item['category_name'] ?? 'Mỹ phẩm' }}</span>
                                                @if($isOutOfStock)
                                                    <span class="badge bg-danger text-white fs-9"><i class="bi bi-slash-circle me-1"></i>Hết hàng</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold fs-7" style="font-size: 0.85rem; color: #0f172a;">{{ number_format($item['price'], 0, ',', '.') }}đ</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-1.5">
                                        <button type="button" class="qty-btn btn-minus" {{ $isOutOfStock ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}>-</button>
                                        <input type="number" class="form-control form-control-sm text-center input-qty fw-bold" style="width: 40px; background-color: #ffffff; border-color: rgba(236,72,153,0.3); color: #0f172a; padding: 0.25rem 0;" value="{{ $item['quantity'] }}" min="1" {{ $isOutOfStock ? 'disabled' : '' }}>
                                        <button type="button" class="qty-btn btn-plus" {{ $isOutOfStock ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}>+</button>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <strong class="fs-6 item-subtotal" style="color: #ec4899; font-size: 0.9rem !important;">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ</strong>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm text-danger btn-remove p-1" title="Xóa khỏi giỏ">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Side: Selected Items Checkout Summary -->
        <div class="col-lg-4">
            <div class="cart-card p-4 sticky-top" style="top: 100px; z-index: 10;">
                <h5 class="fw-bold text-dark mb-3 brand-font border-bottom border-secondary pb-3">Tóm Tắt Đơn Hàng</h5>
                
                <div class="d-flex justify-content-between mb-2 fs-7 text-muted">
                    <span>Số sản phẩm đã chọn:</span>
                    <strong class="text-dark" id="selected-count-display">0 món</strong>
                </div>

                <div class="d-flex justify-content-between mb-3 fs-7 text-muted">
                    <span>Phí vận chuyển:</span>
                    <span class="text-success fw-semibold">Miễn phí</span>
                </div>

                <hr class="border-secondary my-3">

                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold fs-6 text-dark">Tổng tiền thanh toán:</span>
                    <strong class="fs-4 text-warning" id="selected-total-display" style="color: #f59e0b !important;">0đ</strong>
                </div>

                <form action="{{ route('checkout.index') }}" method="GET" id="checkout-form">
                    <input type="hidden" name="selected_items" id="selected-items-input" value="">
                    <button type="submit" class="btn-rose-submit w-100 d-flex align-items-center justify-content-center gap-2" id="btn-proceed-checkout">
                        <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                        <span>Tiến Hành Thanh Toán</span>
                    </button>
                </form>

                <div class="text-center mt-3 text-muted fs-8" style="font-size: 0.78rem;">
                    <i class="bi bi-shield-lock me-1"></i> Bảo mật thanh toán SSL 256-bit
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Empty Cart -->
    <div class="cart-card p-5 text-center my-4">
        <i class="bi bi-bag-x" style="font-size: 4.5rem; color: #ec4899; filter: drop-shadow(0 0 15px rgba(236,72,153,0.4));"></i>
        <h4 class="fw-bold text-dark mt-3 mb-2 brand-font">Giỏ hàng của bạn đang trống!</h4>
        <p class="mb-4 text-muted">Hãy thêm những thỏi son yêu thích vào giỏ hàng ngay nhé.</p>
        <a href="{{ route('home') }}" class="btn-rose-submit text-decoration-none px-4 py-2 d-inline-block">
            <i class="bi bi-arrow-left me-2"></i> Khám Phá Sản Phẩm
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/user/pages/cart.js') }}"></script>
@endpush

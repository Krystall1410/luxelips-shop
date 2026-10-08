@extends('user.layouts.app')

@section('title', 'Thanh Toán Đơn Hàng | LuxeLips PRO')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/pages/checkout.css') }}">
@endpush

@section('content')
<div class="container py-5" id="checkout-container"
    data-ghn-provinces-url="{{ route('ghn.provinces') }}"
    data-ghn-districts-base-url="{{ url('/ghn/districts') }}"
    data-ghn-wards-base-url="{{ url('/ghn/wards') }}"
    data-ghn-fee-url="{{ route('ghn.fee') }}"
    data-csrf-token="{{ csrf_token() }}">

    <div class="mb-4">
        <h1 class="h3 fw-bold text-dark mb-1 brand-font">Thanh Toán Đơn Hàng</h1>
        <p class="fs-7 mb-0 text-muted">Vui lòng nhập thông tin giao hàng và chọn phương thức thanh toán</p>
    </div>

    <div class="row g-4">
        <!-- Left Column: Delivery Form & Payment Methods -->
        <div class="col-lg-7">
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 mb-4 text-white border-0 shadow-lg" style="background: linear-gradient(135deg, #e11d48, #be123c);">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <ul class="mb-0 ps-3 d-inline-block align-middle">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="checkout-card p-4 p-md-4">
                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="selected_items" value="{{ implode(',', array_keys($selectedCart)) }}">

                    <!-- 1. Thông Tin Nhận Hàng -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-3 brand-font d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt-fill" style="color: #ec4899;"></i> 
                            1. Thông Tin Nhận Hàng
                        </h5>

                        <div class="mb-3">
                            <label class="form-label fs-7 fw-medium">Họ và tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" name="fullname" class="form-control form-control-custom" placeholder="Ví dụ: Nguyễn Văn A" value="{{ $user ? $user->name : '' }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fs-7 fw-medium">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control form-control-custom" placeholder="Ví dụ: 0987654321" value="{{ $user ? $user->phone : '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fs-7 fw-medium">Email</label>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="email@example.com" value="{{ $user ? $user->email : '' }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fs-7 fw-medium">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                                <select id="ghn-province-select" class="form-select form-control-custom" required>
                                    <option value="">-- Chọn Tỉnh / Thành --</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fs-7 fw-medium">Quận / Huyện <span class="text-danger">*</span></label>
                                <select id="ghn-district-select" class="form-select form-control-custom" disabled required>
                                    <option value="">-- Chọn Quận / Huyện --</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fs-7 fw-medium">Phường / Xã <span class="text-danger">*</span></label>
                                <select id="ghn-ward-select" class="form-select form-control-custom" disabled required>
                                    <option value="">-- Chọn Phường / Xã --</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-7 fw-medium">Địa chỉ chi tiết (Số nhà, tên đường...) <span class="text-danger">*</span></label>
                            <input type="text" id="address-detail-input" class="form-control form-control-custom" placeholder="Ví dụ: Số 123 Đường Nguyễn Trãi..." required>
                            <input type="hidden" name="address" id="full-address-hidden" value="Số 123 Đường Nguyễn Trãi, Quận 1, TP. Hồ Chí Minh">
                            <input type="hidden" name="to_district_id" id="to-district-id-hidden" value="">
                            <input type="hidden" name="to_ward_code" id="to-ward-code-hidden" value="">
                        </div>
                    </div>

                    <hr class="border-secondary my-4">

                    <!-- 2. Hình Thức Thanh Toán -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-3 brand-font d-flex align-items-center gap-2">
                            <i class="bi bi-wallet2" style="color: #ec4899;"></i> 
                            2. Hình Thức Thanh Toán
                        </h5>

                        <div class="d-flex flex-column gap-3">
                            <label class="payment-radio-box active" id="payment-box-cod">
                                <input type="radio" name="payment_method" value="cod" checked required>
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                            <i class="bi bi-cash-stack fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Thanh toán trực tiếp khi nhận hàng (COD)</h6>
                                            <span class="fs-8 text-muted" style="font-size: 0.8rem;">Thanh toán tiền mặt cho nhân viên giao hàng khi nhận hàng.</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-success fs-9 ms-2">Phổ biến</span>
                                </div>
                            </label>

                            <label class="payment-radio-box" id="payment-box-momo">
                                <input type="radio" name="payment_method" value="momo" required>
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background-color: #a50064; color: #ffffff;">
                                            <i class="bi bi-qr-code-scan fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Thanh toán qua ví MoMo (Ví MoMo)</h6>
                                            <span class="fs-8 text-muted" style="font-size: 0.8rem;">Thanh toán nhanh bằng cách quét mã QR MoMo tiện lợi.</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-danger fs-9 ms-2" style="background-color: #a50064 !important;">Tự động</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-order d-flex align-items-center justify-content-center gap-2 mt-4">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <span>Xác Nhận & Đặt Hàng</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Order Summary -->
        <div class="col-lg-5">
            <div class="checkout-card p-4 sticky-top" style="top: 100px; z-index: 10;">
                <h5 class="fw-bold text-dark mb-3 brand-font border-bottom border-secondary pb-3">Đơn Hàng Của Bạn</h5>

                <div class="checkout-items-list mb-3" style="max-height: 320px; overflow-y: auto;">
                    @forelse($selectedCart as $item)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-secondary" style="border-color: rgba(236,72,153,0.15) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 overflow-hidden bg-light flex-shrink-0" style="width: 50px; height: 50px;">
                                @if(isset($item['image']) && $item['image'])
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 fs-7">{{ $item['name'] }}</h6>
                                <span class="fs-8 text-muted" style="font-size: 0.8rem;">Số lượng: {{ $item['quantity'] }}</span>
                            </div>
                        </div>
                        <strong class="fs-7" style="color: #ec4899;">
                            {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ
                        </strong>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted fs-7">
                        Chưa có sản phẩm nào được chọn.
                    </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-between mb-2 fs-7 text-muted">
                    <span>Tạm tính:</span>
                    <strong class="text-dark" id="subtotal-display" data-subtotal="{{ $totalAmount }}">{{ number_format($totalAmount, 0, ',', '.') }}đ</strong>
                </div>

                <div class="d-flex justify-content-between mb-3 fs-7 text-muted">
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-truck" style="color: #ec4899;"></i> Phí vận chuyển (GHN):
                    </span>
                    <span class="fw-bold" id="shipping-fee-display" style="color: #ec4899;">Vui lòng chọn địa chỉ</span>
                </div>

                <hr class="border-secondary my-3">

                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold fs-6 text-dark">Tổng cộng:</span>
                    <strong class="fs-4 text-warning" id="grand-total-display" style="color: #f59e0b !important;">{{ number_format($totalAmount, 0, ',', '.') }}đ</strong>
                </div>

                <div class="p-3 rounded-3 mt-3 fs-8" style="background-color: #fdf2f8; color: #475569; border: 1px solid rgba(236, 72, 153, 0.25);">
                    <i class="bi bi-info-circle text-warning me-1"></i> Sản phẩm sẽ được đóng gói cẩn thận và giao tận tay bạn trong vòng 1-3 ngày làm việc.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/user/pages/checkout.js') }}"></script>
@endpush

@extends('auth.layouts.app')

@section('title', 'Xác Thực OTP | LuxeLips PRO')

@section('content')
<h3 class="fw-bold text-center mb-1 text-dark">Xác Thực Mã OTP</h3>
<p class="fs-7 text-center mb-4 text-muted">
    Mã OTP 6 chữ số đã được gửi đến email:<br>
    <strong class="fs-6" style="color: #ec4899;">{{ session('pending_registration.email') }}</strong>
</p>

<form action="{{ route('otp.verify') }}" method="POST">
    @csrf

    <div class="mb-4 text-center">
        <label class="form-label fs-7 fw-semibold mb-2 text-dark">NHẬP MÃ OTP 6 CHỮ SỐ</label>
        <input type="text" 
               name="otp_code" 
               class="form-control otp-input" 
               placeholder="123456" 
               maxlength="6" 
               autocomplete="off"
               required 
               autofocus>
        @error('otp_code')
            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
        @enderror
    </div>

    <div class="text-center fs-8 mb-4 text-muted">
        <i class="bi bi-clock-history me-1"></i> Mã OTP có hiệu lực trong vòng <strong class="text-dark">10 phút</strong>.
    </div>

    <button type="submit" class="btn-submit-rose mb-3">
        <i class="bi bi-check-circle-fill fs-5"></i> Xác Nhận OTP & Hoàn Tất
    </button>
</form>

<div class="text-center fs-7 border-top pt-3 mt-2 text-muted" style="border-color: rgba(236, 72, 153, 0.15) !important;">
    Chưa nhận được mã? 
    <form action="{{ route('otp.resend') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn-resend-link ms-1">Gửi lại mã OTP</button>
    </form>
</div>
@endsection

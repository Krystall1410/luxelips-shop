@extends('auth.layouts.app')

@section('title', 'Đổi Mật Khẩu | LuxeLips PRO')

@section('content')
<h3 class="fw-bold text-center mb-1 text-dark">Xác Thực OTP Khôi Phục</h3>
<p class="fs-7 text-center mb-4 text-muted">Email: <strong class="text-dark">{{ session('reset_password_email') }}</strong></p>

<form action="{{ route('password.update') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label-custom">MÃ OTP 6 CHỮ SỐ <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-key"></i></span>
            <input type="text" name="otp" class="form-control otp-input" placeholder="000000" maxlength="6" required autofocus autocomplete="off">
        </div>
        @error('otp')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label-custom">MẬT KHẨU MỚI <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
        </div>
        @error('password')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label class="form-label-custom">XÁC NHẬN MẬT KHẨU MỚI <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới" required>
        </div>
    </div>

    <button type="submit" class="btn-submit-rose mb-3">
        <i class="bi bi-check-circle-fill fs-6"></i> Cập Nhật Mật Khẩu Mới
    </button>
</form>

<form action="{{ route('password.resend_otp') }}" method="POST" class="text-center">
    @csrf
    <span class="fs-7 text-muted">Chưa nhận được mã OTP?</span>
    <button type="submit" class="btn btn-link link-rose p-0 fs-7 border-0 align-baseline fw-bold">Gửi lại mã OTP mới</button>
</form>
@endsection

@extends('auth.layouts.app')

@section('title', 'Quên Mật Khẩu | LuxeLips PRO')

@section('content')
<h3 class="fw-bold text-center mb-1 text-dark">Quên Mật Khẩu?</h3>
<p class="fs-7 text-center mb-4 text-muted">Nhập địa chỉ Email đăng ký để nhận mã OTP khôi phục</p>

<form action="{{ route('password.email') }}" method="POST">
    @csrf

    <div class="mb-4">
        <label class="form-label-custom">ĐỊA CHỈ EMAIL ĐÃ ĐĂNG KÝ <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" placeholder="example@domain.com" value="{{ old('email') }}" required autofocus>
        </div>
        @error('email')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn-submit-rose mb-3">
        <i class="bi bi-send-fill fs-6"></i> Gửi Mã OTP Khôi Phục
    </button>

    <div class="text-center fs-7 text-muted">
        Nhớ mật khẩu rồi? <a href="{{ route('login') }}" class="link-rose ms-1"><i class="bi bi-arrow-left me-1"></i>Đăng nhập ngay</a>
    </div>
</form>
@endsection

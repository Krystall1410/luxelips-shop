@extends('auth.layouts.app')

@section('title', 'Đăng Nhập | LuxeLips PRO')

@section('content')
<h3 class="fw-bold text-center mb-1 text-dark">Đăng Nhập Tài Khoản</h3>
<p class="fs-7 text-center mb-4 text-muted">Đăng nhập để trải nghiệm đầy đủ tính năng mua sắm & theo dõi đơn hàng</p>

<form action="{{ route('login') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label-custom">TÊN ĐĂNG NHẬP HOẶC EMAIL <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="login" class="form-control" placeholder="admin hoặc admin@luxelips.com" value="{{ old('login') }}" required autofocus>
        </div>
        @error('login')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label-custom">MẬT KHẨU <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        @error('password')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" style="cursor: pointer;">
            <label class="form-check-label fs-7" for="remember" style="color: #475569; cursor: pointer;">
                Ghi nhớ đăng nhập
            </label>
        </div>
        <a href="{{ route('password.forgot') }}" class="link-rose fs-7">Quên mật khẩu?</a>
    </div>

    <button type="submit" class="btn-submit-rose mb-3">
        <i class="bi bi-box-arrow-in-right fs-5"></i> Đăng Nhập Hệ Thống
    </button>

    <div class="text-center fs-7 text-muted">
        Chưa có tài khoản? <a href="{{ route('register') }}" class="link-rose ms-1">Đăng ký ngay</a>
    </div>
</form>
@endsection
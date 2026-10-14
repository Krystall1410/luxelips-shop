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

    <div class="d-flex align-items-center my-3">
        <hr class="flex-grow-1 border-secondary">
        <span class="px-3 text-muted fs-8 fw-semibold" style="letter-spacing: 0.05em;">HOẶC ĐĂNG NHẬP VỚI</span>
        <hr class="flex-grow-1 border-secondary">
    </div>

    <a href="{{ route('auth.google') }}" class="btn w-100 d-flex align-items-center justify-content-center gap-2 py-2.5 mb-3 rounded-3 shadow-sm border text-decoration-none" style="background: #ffffff; color: #1f2937; border-color: #e2e8f0; font-size: 0.92rem; font-weight: 600; transition: all 0.2s ease;">
        <svg width="20" height="20" viewBox="0 0 48 48">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
        </svg>
        <span>Tiếp tục với Google</span>
    </a>

    <div class="text-center fs-7 text-muted">
        Chưa có tài khoản? <a href="{{ route('register') }}" class="link-rose ms-1">Đăng ký ngay</a>
    </div>
</form>
@endsection
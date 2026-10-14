@extends('auth.layouts.app')

@section('title', 'Đăng Ký Tài Khoản | LuxeLips PRO')

@section('content')
<h3 class="fw-bold text-center mb-1 text-dark">Đăng Ký Tài Khoản</h3>
<p class="fs-7 text-center mb-4 text-muted">Tạo tài khoản mới để trải nghiệm đầy đủ tính năng hệ thống</p>

@if ($errors->any())
<div class="alert alert-danger rounded-3 mb-3 fs-7" style="background: rgba(225,29,72,0.08); border: none; border-left: 4px solid #f43f5e; color: #e11d48;">
    <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Có lỗi xảy ra:</div>
    <ul class="mb-0 ps-3 fs-7">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('register') }}" method="POST" id="form-register">
    @csrf

    <div class="mb-2">
        <label class="form-label-custom">HỌ VÀ TÊN</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="name" class="form-control" placeholder="Nguyễn Văn A" value="{{ old('name') }}" autofocus>
        </div>
    </div>

    <div class="mb-2">
        <label class="form-label-custom">ĐỊA CHỈ EMAIL <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" placeholder="user@gmail.com" value="{{ old('email') }}" required>
        </div>
    </div>

    <div class="mb-2">
        <label class="form-label-custom">SỐ ĐIỆN THOẠI <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
            <input type="text" name="phone" class="form-control" placeholder="0373033512" value="{{ old('phone') }}" required>
        </div>
    </div>

    <div class="mb-2">
        <label class="form-label-custom">TÊN ĐĂNG NHẬP <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
            <input type="text" name="username" class="form-control" placeholder="luxelover99" value="{{ old('username') }}" required>
        </div>
    </div>

    <div class="mb-2">
        <label class="form-label-custom">MẬT KHẨU <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label-custom">XÁC NHẬN MẬT KHẨU <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
            <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
        </div>
    </div>

    <button type="submit" class="btn-submit-rose mb-3" id="btn-register">
        <i class="bi bi-envelope-paper-heart-fill fs-5 me-1"></i> Tạo Tài Khoản & Nhận OTP Email
    </button>

    <div class="d-flex align-items-center my-3">
        <hr class="flex-grow-1 border-secondary">
        <span class="px-3 text-muted fs-8 fw-semibold" style="letter-spacing: 0.05em;">HOẶC ĐĂNG KÝ VỚI</span>
        <hr class="flex-grow-1 border-secondary">
    </div>

    <a href="{{ route('auth.google') }}" class="btn w-100 d-flex align-items-center justify-content-center gap-2 py-2.5 mb-3 rounded-3 shadow-sm border text-decoration-none" style="background: #ffffff; color: #1f2937; border-color: #e2e8f0; font-size: 0.92rem; font-weight: 600; transition: all 0.2s ease;">
        <svg width="20" height="20" viewBox="0 0 48 48">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
        </svg>
        <span>Đăng ký nhanh với Google</span>
    </a>

    <div class="text-center fs-7 text-muted">
        Đã có tài khoản? <a href="{{ route('login') }}" class="link-rose ms-1">Đăng nhập tại đây</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-register');
        const btn = document.getElementById('btn-register');
        if (form && btn) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang gửi mã OTP đến Email...';
            });
        }
    });
</script>
@endpush
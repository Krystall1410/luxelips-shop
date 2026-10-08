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
@extends('user.layouts.app')

@section('title', 'Thông Tin Tài Khoản | LuxeLips PRO')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/pages/profile.css') }}">
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            @if ($errors->any())
                <div class="alert alert-danger rounded-4 mb-4 text-white border-0 shadow-lg" style="background: linear-gradient(135deg, #e11d48, #be123c);">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <ul class="mb-0 ps-3 d-inline-block align-middle">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="profile-card p-4 p-md-5">
                <h3 class="fw-bold text-dark mb-2 brand-font text-center">Thông Tin Tài Khoản</h3>
                <p class="fs-7 text-center mb-4 text-muted">Quản lý ảnh đại diện, thông tin cá nhân và cài đặt Email</p>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Avatar Preview & Input -->
                    <div class="text-center mb-4">
                        <div class="avatar-preview-box mb-2">
                            <img src="{{ $user->avatar_url }}" id="avatar-img-preview" alt="Avatar User">
                            <label for="avatar-file-input" class="btn-upload-avatar">
                                <i class="bi bi-camera-fill me-1"></i> Đổi ảnh
                            </label>
                        </div>
                        <input type="file" name="avatar" id="avatar-file-input" class="d-none" accept="image/*">
                        <small class="d-block fs-8 text-muted">Định dạng: JPG, PNG, WEBP (Tối đa 4MB)</small>
                    </div>

                    <!-- Form inputs -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-semibold">HỌ VÀ TÊN <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-custom" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-semibold">TÊN ĐĂNG NHẬP <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control form-control-custom" value="{{ old('username', $user->username) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-semibold">SỐ ĐIỆN THOẠI</label>
                            <input type="tel" name="phone" class="form-control form-control-custom" value="{{ old('phone', $user->phone) }}" placeholder="Ví dụ: 0987654321">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-semibold">
                                ĐỊA CHỈ EMAIL <span class="text-danger">*</span>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success fs-9 ms-1 text-white">Đã xác thực</span>
                                @else
                                    <span class="badge bg-warning fs-9 ms-1 text-white">Chưa xác thực</span>
                                @endif
                            </label>
                            <input type="email" name="email" class="form-control form-control-custom" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>

                    <!-- Note about Email change OTP -->
                    <div class="p-3 rounded-3 mt-4 fs-8 d-flex align-items-center gap-2" style="background-color: #fdf2f8; color: #b45309; border: 1px solid rgba(236, 72, 153, 0.25);">
                        <i class="bi bi-shield-lock-fill fs-5 flex-shrink-0"></i>
                        <span>Lưu ý: Nếu bạn nhập địa chỉ Email mới, hệ thống sẽ tự động gửi mã OTP 6 chữ số đến hộp thư mới để xác nhận đổi Email.</span>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn-rose-submit d-inline-flex align-items-center gap-2">
                            <i class="bi bi-floppy-fill"></i>
                            <span>Lưu Thay Đổi Thông Tin</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/user/pages/profile.js') }}"></script>
@endpush

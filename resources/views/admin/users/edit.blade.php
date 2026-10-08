@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Người Dùng | LuxeLips Admin')
@section('header_title', 'Chỉnh Sửa Tài Khoản')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">
            <i class="bi bi-pencil-square me-2" style="color: #ec4899;"></i>
            Chỉnh Sửa Người Dùng: {{ $user->name }}
        </h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    {{-- Error Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-3 mb-4" role="alert" style="background-color: rgba(225, 29, 72, 0.15); border: none; border-left: 4px solid #f43f5e; color: #fb7185;">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Có lỗi nhập liệu vui lòng kiểm tra lại:</div>
            <ul class="mb-0 ps-3 fs-7">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-elegant p-4">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium text-light">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium text-light">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium text-light">Mật Khẩu Mới <span class="text-muted fw-normal">(Đổi nếu muốn, để trống nếu giữ nguyên)</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Bỏ trống nếu không đổi..." minlength="6">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-medium text-light">Số Điện Thoại</label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="0901234567">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label fw-medium text-light">Vai Trò Tài Khoản <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required style="background-color: #131d38; color: #ffffff;" @if($user->id === auth()->id()) disabled @endif>
                            <option value="user" @selected(old('role', $user->role) === 'user')>Khách hàng (User)</option>
                            <option value="admin" @selected(old('role', $user->role) === 'admin')>Quản trị viên (Admin)</option>
                        </select>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="role" value="admin">
                            <div class="form-text text-warning mt-1 fs-7">
                                <i class="bi bi-shield-lock-fill me-1"></i> Bạn đang chỉnh sửa tài khoản cá nhân. Hệ thống tự động khóa vai trò Admin để tránh bị khóa tài khoản.
                            </div>
                        @endif
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top border-secondary" style="border-color: #233054 !important;">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-rose px-4 fw-semibold" style="background-color: #ec4899; border: none;">
                            <i class="bi bi-save-fill me-1"></i> Cập Nhật Người Dùng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

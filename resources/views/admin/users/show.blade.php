@extends('admin.layouts.app')

@section('title', 'Chi tiết Tài Khoản | LuxeLips Admin')
@section('header_title', 'Quản Lý Tài Khoản')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">
            <i class="bi bi-person-bounding-box"></i>
            Chi tiết Tài Khoản người dùng
        </h1>
        <a href="{{ route('admin.users.index') }}" class="btn-outline-rose">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card-elegant mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-info-square me-2"></i> Thông tin cá nhân
            </div>
            <span class="badge bg-dark border border-secondary text-light">ID: #{{ $user->id }}</span>
        </div>

        <div class="card-body p-4">
            <div class="row align-items-center mb-4 pb-4 border-bottom border-secondary">
                <div class="col-auto">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $user->isAdmin() ? '8b5cf6' : 'ec4899' }}&color=fff&size=96" class="rounded-circle border border-rose shadow-lg" style="width: 80px; height: 80px;" alt="{{ $user->name }}">
                </div>
                <div class="col">
                    <h3 class="fw-bold text-light mb-1">{{ $user->name }}</h3>
                    <code class="text-rose fs-6">@ {{ $user->username ?? 'chua_co_username' }}</code>
                    <div class="mt-2">
                        @if($user->isAdmin())
                            <span class="badge bg-purple-subtle text-purple border border-purple px-3 py-1" style="background: rgba(139,92,246,0.15); color: #a78bfa; border-color: #8b5cf6 !important;">
                                <i class="bi bi-shield-check me-1"></i> Admin (Quản trị viên)
                            </span>
                        @else
                            <span class="badge px-3 py-2 text-white shadow-sm" style="background: linear-gradient(135deg, #2563eb, #1d4ed8) !important; border: none; font-weight: 600; font-size: 0.85rem;">
                                <i class="bi bi-person me-1"></i> Khách hàng (User)
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <th style="width: 220px; padding: 1rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Tên đăng nhập (Username)</th>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">
                                <strong class="text-light fs-6">{{ $user->username }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Địa chỉ Email</th>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">
                                <span class="text-info fs-6 fw-medium">{{ $user->email }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Số điện thoại</th>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">
                                <span class="text-light fs-6">{{ $user->phone ?? 'Chưa cung cấp' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">Trạng thái Email OTP</th>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">
                                @if($user->email_verified_at)
                                    <span class="badge text-white px-3 py-2" style="background: rgba(34,197,94,0.25); border: 1px solid #22c55e !important;">
                                        <i class="bi bi-check-circle-fill me-1 text-success"></i> Đã xác thực lúc {{ $user->email_verified_at->format('d/m/Y H:i:s') }}
                                    </span>
                                @else
                                    <span class="badge text-white px-3 py-2" style="background: rgba(245,158,11,0.25); border: 1px solid #f59e0b !important;">
                                        <i class="bi bi-clock me-1 text-warning"></i> Chưa xác thực Email
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-color);">Ngày khởi tạo tài khoản</th>
                            <td style="padding: 1rem;">
                                <span class="text-slate-300">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'Không rõ' }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer p-4 border-top border-secondary d-flex justify-content-end gap-2" style="background: transparent;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
            @if($user->id !== Auth::id())
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn tài khoản này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger px-4">
                        <i class="bi bi-trash3 me-1"></i> Xóa tài khoản
                    </button>
                </form>
            @endif
        </div>
    </div>

@endsection

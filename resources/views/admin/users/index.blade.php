@extends('admin.layouts.app')

@section('title', 'Quản lý Tài Khoản | LuxeLips Admin')
@section('header_title', 'Quản Lý Tài Khoản')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">
            <i class="bi bi-people"></i>
            Quản Lý Tài Khoản Người Dùng
        </h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-rose fw-semibold d-inline-flex align-items-center" style="background-color: #ec4899; border: none; font-size: 0.82rem; padding: 6px 14px;">
            <i class="bi bi-person-plus-fill me-1.5"></i> + Thêm người dùng
        </a>
    </div>

    {{-- Alerts --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success rounded-3 mb-4 d-flex align-items-center" role="alert" style="background-color: rgba(34, 197, 94, 0.15); border: none; border-left: 4px solid #22c55e; color: #4ade80;">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            {{ $message }}
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger rounded-3 mb-4 d-flex align-items-center" role="alert" style="background-color: rgba(225, 29, 72, 0.15); border: none; border-left: 4px solid #f43f5e; color: #fb7185;">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            {{ $message }}
        </div>
    @endif

    {{-- Summary Cards Row (4 Cards) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #ec4899;">
                    <i class="bi bi-people-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Tổng số tài khoản</div>
                    <div class="fw-bold text-light" style="font-size: 1.15rem;">{{ $totalUsers }} tài khoản</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #8b5cf6;">
                    <i class="bi bi-shield-lock-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Quản trị viên (Admin)</div>
                    <div class="fw-bold text-info" style="font-size: 1.15rem;">{{ $adminUsers }} admin</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #3b82f6;">
                    <i class="bi bi-person-badge-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Khách hàng (User)</div>
                    <div class="fw-bold text-light" style="font-size: 1.15rem;">{{ $clientUsers }} thành viên</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #10b981;">
                    <i class="bi bi-patch-check-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Đã xác thực Email</div>
                    <div class="fw-bold text-success" style="font-size: 1.15rem;">{{ $verifiedUsers }} xác thực</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Form --}}
    <div class="mb-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2.5 align-items-end">
            <div class="col-md-5">
                <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Tìm kiếm người dùng</label>
                <div class="input-group">
                    <span class="input-group-text border-secondary text-muted" style="background-color: #131d38; height: 38px; border-radius: 8px 0 0 8px;"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control text-light border-secondary admin-filter-input" style="background-color: #131d38; border-radius: 0 8px 8px 0 !important;" placeholder="Tìm theo tên, username, email hoặc SĐT..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Vai trò tài khoản</label>
                <select name="role" class="form-select text-light border-secondary admin-filter-input" style="background-color: #131d38;">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin (Quản trị)</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User (Khách hàng)</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-rose admin-filter-btn flex-grow-1" style="background-color: #ec4899; border: none;">
                    <i class="bi bi-funnel-fill me-1"></i> Lọc dữ liệu
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary admin-filter-btn" title="Đặt lại bộ lọc">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="card-elegant">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-table me-2"></i>
                Danh sách tài khoản ({{ $users->total() }})
            </div>
        </div>

        @if($users->isEmpty())
            <div class="card-body p-5 text-center text-muted">
                <i class="bi bi-person-x" style="font-size: 3rem; color: #475569;"></i>
                <p class="mt-3 mb-0">Không tìm thấy tài khoản nào khớp với điều kiện tìm kiếm.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: var(--card-header-bg);">
                        <tr>
                            <th style="padding-left: 1.5rem; color: var(--text-muted); font-weight: 600;">Người dùng</th>
                            <th class="text-nowrap" style="color: var(--text-muted); font-weight: 600;">Tên đăng nhập</th>
                            <th class="text-nowrap" style="color: var(--text-muted); font-weight: 600;">Email</th>
                            <th class="text-nowrap" style="color: var(--text-muted); font-weight: 600;">Số điện thoại</th>
                            <th class="text-nowrap" style="color: var(--text-muted); font-weight: 600;">Vai trò</th>
                            <th class="text-nowrap" style="color: var(--text-muted); font-weight: 600;">Email OTP</th>
                            <th class="text-nowrap" style="color: var(--text-muted); font-weight: 600;">Ngày tạo</th>
                            <th class="text-nowrap text-center" style="width: 180px; color: var(--text-muted); font-weight: 600;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding-left: 1.5rem;">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $user->isAdmin() ? '8b5cf6' : 'ec4899' }}&color=fff" class="rounded-circle me-2 border border-secondary" style="width: 32px; height: 32px;" alt="{{ $user->name }}">
                                        <div>
                                            <div class="fw-semibold text-light text-nowrap" style="font-size: 0.85rem;">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <code class="text-rose" style="font-size: 0.78rem;">@ {{ $user->username ?? 'N/A' }}</code>
                                </td>
                                <td class="text-nowrap">
                                    <span class="text-slate-300" style="font-size: 0.8rem;" title="{{ $user->email }}">{{ \Illuminate\Support\Str::before($user->email, '@') }}@...</span>
                                </td>
                                <td class="text-nowrap">
                                    <span class="text-slate-300" style="font-size: 0.8rem;">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                                </td>
                                <td class="text-nowrap">
                                    @if($user->isAdmin())
                                        <span class="badge border" style="background: rgba(139,92,246,0.15); color: #a78bfa; border-color: #8b5cf6 !important; font-size: 0.75rem; padding: 4px 8px;">
                                            <i class="bi bi-shield-check me-1"></i> Admin
                                        </span>
                                    @else
                                        <span class="badge text-white" style="background: #2563eb; font-size: 0.75rem; padding: 4px 8px;">
                                            <i class="bi bi-person me-1"></i> Khách hàng
                                        </span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    @if($user->email_verified_at)
                                        <span class="badge text-white" style="background: rgba(34,197,94,0.25); border: 1px solid #22c55e !important; font-size: 0.72rem; padding: 3px 8px;">
                                            <i class="bi bi-check-circle-fill me-1 text-success"></i> Đã xác thực
                                        </span>
                                    @else
                                        <span class="badge text-white" style="background: rgba(245,158,11,0.25); border: 1px solid #f59e0b !important; font-size: 0.72rem; padding: 3px 8px;">
                                            <i class="bi bi-clock me-1 text-warning"></i> Chưa xác thực
                                        </span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <span class="text-muted fs-7">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                </td>
                                <td class="text-nowrap text-center">
                                    <div class="d-inline-flex gap-1 justify-content-center py-1">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-light border-secondary px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Sửa thông tin">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>
                                        @if($user->id !== Auth::id())
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xoá tài khoản {{ $user->username ?? $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Xóa tài khoản">
                                                    <i class="bi bi-trash3"></i> Xóa
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer p-3 border-top border-secondary">
                {{ $users->links() }}
            </div>
        @endif
    </div>

@endsection

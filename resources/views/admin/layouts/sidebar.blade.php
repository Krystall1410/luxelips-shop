<!-- Sidebar Component [Part 1] -->
<nav id="sidebar">
    <div class="p-4 d-flex justify-content-center border-bottom" style="border-color: var(--sidebar-border) !important;">
        <a href="{{ route('admin.dashboard') }}" class="brand-logo">
            <i class="bi bi-stars"></i>
            LuxeLips
        </a>
    </div>

    <div class="p-3 flex-grow-1 overflow-y-auto">

        <!-- SECTION 1: Tổng quan & Bán hàng -->
        <p class="mb-2" style="color: var(--primary-color); font-size: 0.78rem; margin-left: 0.75rem; font-weight: 600;">
            <i class="bi bi-graph-up-arrow me-1"></i> Tổng quan & Bán hàng
        </p>
        <ul class="sidebar-menu mb-4">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-check-fill"></i>
                    <span>Đơn Hàng</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Doanh Thu</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.finance.index') }}" class="{{ request()->routeIs('admin.finance.index') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Thống Kê Tài Chính</span>
                </a>
            </li>
        </ul>

        <!-- SECTION 2: Sản phẩm & Mua hàng -->
        <p class="mb-2" style="color: var(--primary-color); font-size: 0.78rem; margin-left: 0.75rem; font-weight: 600;">
            <i class="bi bi-box-seam-fill me-1"></i> Sản phẩm & Mua hàng
        </p>
        <ul class="sidebar-menu mb-4">
            <li>
                <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="bi bi-bag-heart-fill"></i>
                    <span>Sản Phẩm</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill"></i>
                    <span>Danh Mục</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                    <i class="bi bi-award-fill"></i>
                    <span>Thương Hiệu</span>
                </a>
            </li>
        </ul>

        <!-- SECTION 3: Hệ thống & Tài khoản -->
        <p class="mb-2" style="color: var(--primary-color); font-size: 0.78rem; margin-left: 0.75rem; font-weight: 600;">
            <i class="bi bi-gear-wide-connected me-1"></i> Hệ thống & Tài khoản
        </p>
        <ul class="sidebar-menu mb-2">
            <li>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Quản lý Tài khoản</span>
                </a>
            </li>
            <li>
                <a href="{{ route('home') }}" target="_blank">
                    <i class="bi bi-globe"></i>
                    <span>Xem Trang Client</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar Footer (Admin User & Real Logout Form) -->
    <div class="sidebar-footer">
        @auth
        <div class="admin-profile">
            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
            <div class="admin-info">
                <div class="name">{{ Auth::user()->name }}</div>
                <div class="role">@ {{ Auth::user()->username ?? 'admin' }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout border-0 w-100" style="cursor: pointer;">
                <i class="bi bi-box-arrow-left"></i> Đăng xuất
            </button>
        </form>
        @else
        <a href="{{ route('login') }}" class="btn-rose w-100 text-center py-2">
            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
        </a>
        @endauth
    </div>
</nav>
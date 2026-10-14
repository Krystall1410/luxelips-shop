<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LuxeLips PRO | Thời Trang Son Môi Cao Cấp')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Shared Client Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/user/theme.css') }}?v={{ filemtime(public_path('css/user/theme.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}?v={{ filemtime(public_path('css/user/navbar.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/user/chat-widget.css') }}?v={{ filemtime(public_path('css/user/chat-widget.css')) }}">

    @stack('styles')
</head>
<body data-csrf-token="{{ csrf_token() }}">

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top lux-navbar py-3">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand brand-logo fs-3 fw-bold text-white d-flex align-items-center gap-2" href="{{ route('home') }}">
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 40px; height: 40px; background: linear-gradient(135deg, #ec4899, #e11d48);">
                    <i class="bi bi-stars fs-4" style="color: #ffffff !important;"></i>
                </div>
                <span>LUXELIPS <span class="badge bg-danger fs-9 align-middle" style="color: #ffffff !important;">PRO</span></span>
            </a>

            <!-- Mobile Toggler -->
            <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#userNav" aria-controls="userNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>

            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="userNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1 gap-lg-2 ms-lg-2 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1.5"></i> Trang chủ
                        </a>
                    </li>

                    <!-- Mega Dropdown Danh Mục -->
                    <li class="nav-item dropdown">
                        <a class="nav-link cursor-pointer {{ request()->routeIs('collection.index') && request()->has('category') ? 'active' : '' }}" href="#" id="categoryMegaMenuBtn" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-grid-3x3-gap me-1.5"></i> Danh mục
                        </a>
                        <div class="dropdown-menu category-dropdown-menu shadow-2xl" aria-labelledby="categoryMegaMenuBtn">
                            @if(isset($categories) && count($categories) > 0)
                                @foreach($categories as $cat)
                                <a href="{{ route('collection.index', ['category' => $cat->id]) }}" class="category-dropdown-item d-block text-decoration-none">
                                    {{ $cat->name }}
                                </a>
                                @endforeach
                            @else
                                <span class="text-muted small px-3 py-2 d-block">Đang cập nhật danh mục...</span>
                            @endif
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('collection.index') && !request()->has('category') ? 'active' : '' }}" href="{{ route('collection.index') }}">
                            <i class="bi bi-grid me-1.5"></i> Bộ Sưu Tập
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2 gap-lg-3 ms-auto">
                    <!-- Thanh Tìm Kiếm Capsule -->
                    <form action="{{ route('collection.index') }}" method="GET" class="d-none d-md-flex m-0">
                        <div class="nav-search-box">
                            <input type="text" name="q" value="{{ request('q') }}" class="search-input" placeholder="Tìm kiếm sản phẩm...">
                            <button type="submit" class="search-btn" title="Tìm kiếm">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Nút Danh Sách Yêu Thích -->
                    <a href="{{ route('wishlist.index') }}" class="btn nav-header-btn rounded-circle d-flex align-items-center justify-content-center position-relative" style="width: 38px; height: 38px; padding: 0 !important; flex-shrink: 0;" id="wishlist-nav-btn" title="Danh Sách Yêu Thích">
                        <i class="bi bi-heart fs-5 text-danger"></i>
                        <span class="cart-badge badge position-absolute top-0 start-100 translate-middle wishlist-count-badge {{ ($wishlistCount ?? 0) > 0 ? '' : 'd-none' }}" id="wishlist-count-badge" style="background: #e11d48; color: #ffffff !important;">{{ $wishlistCount ?? 0 }}</span>
                    </a>

                    <!-- Nút Giỏ Hàng -->
                    @php
                        $cart = session()->get('cart', []);
                        $cartCount = array_sum(array_column($cart, 'quantity'));
                    @endphp
                    <a href="{{ route('cart.index') }}" class="btn nav-header-btn rounded-circle d-flex align-items-center justify-content-center position-relative" style="width: 38px; height: 38px; padding: 0 !important; flex-shrink: 0;" id="cart-nav-btn" title="Giỏ Hàng">
                        <i class="bi bi-cart3 fs-5" style="color: #ec4899;"></i>
                        <span class="cart-badge badge position-absolute top-0 start-100 translate-middle cart-count-badge {{ $cartCount > 0 ? '' : 'd-none' }}" id="cart-count-badge" style="color: #ffffff !important;">{{ $cartCount }}</span>
                    </a>

                    <!-- User Account / Guest Actions -->
                    @auth
                    <div class="dropdown">
                        <button class="btn nav-header-btn dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                            <span class="text-dark fw-medium">{{ Auth::user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg user-dropdown-menu" style="background-color: #ffffff; border-color: rgba(236, 72, 153, 0.25);">
                            @if(Auth::user()->isAdmin())
                            <li>
                                <a class="dropdown-item py-2 text-warning fw-medium" href="{{ route('admin.products.index') }}">
                                    <i class="bi bi-speedometer2 me-2"></i>Trang Quản Trị (Admin)
                                </a>
                            </li>
                            <li><hr class="dropdown-divider border-secondary"></li>
                            @endif
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('profile.index') }}">
                                    <i class="bi bi-person-gear me-2 text-pink" style="color: #ec4899;"></i>Thông tin tài khoản
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('wishlist.index') }}">
                                    <i class="bi bi-heart me-2 text-danger"></i>Sản phẩm yêu thích
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('user.orders.index') }}">
                                    <i class="bi bi-bag-check me-2 text-warning"></i>Đơn hàng của tôi
                                </a>
                            </li>
                            <li><hr class="dropdown-divider border-secondary"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="nav-header-btn">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Đăng nhập</span>
                    </a>
                    <a href="{{ route('register') }}" class="nav-header-btn-primary">
                        <i class="bi bi-person-plus"></i>
                        <span>Đăng ký</span>
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Global Flash Alerts -->
    <div class="container mt-3">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #10b981, #059669);" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #ef4444, #dc2626);" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 align-middle"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>

    <!-- Main Page Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Global Footer -->
    <footer class="mt-auto py-4 border-top text-center fs-8" style="background-color: #ffffff; border-color: rgba(236, 72, 153, 0.18) !important; color: #64748b;">
        <div class="container">
            <p class="mb-1 fw-semibold text-dark">LUXELIPS COSMETICS & BEAUTY STORE</p>
            <p class="mb-0 text-muted" style="font-size: 0.8rem;">Bản quyền © {{ date('Y') }} LuxeLips PRO. Hệ thống mỹ phẩm son môi chính hãng.</p>
        </div>
    </footer>

    <!-- Global Toast Container -->
    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;"></div>

    <!-- Livechat Widget across entire client portal -->
    @include('user.chat_widget')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Shared Client Main JS -->
    <script src="{{ asset('js/user/main.js') }}"></script>

    @stack('scripts')
</body>
</html>

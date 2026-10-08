<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tài Khoản | LuxeLips')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Auth Shared Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
    @stack('styles')
</head>
<body>

    <div class="auth-container">
        <!-- Brand Logo Header -->
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="text-decoration-none d-inline-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 44px; height: 44px; background: linear-gradient(135deg, #ec4899, #e11d48);">
                    <i class="bi bi-stars fs-4" style="color: #ffffff !important;"></i>
                </div>
                <span class="fs-3 fw-bold text-dark brand-font">LUXELIPS <span class="badge bg-danger fs-9 align-middle" style="color: #ffffff !important;">PRO</span></span>
            </a>
            <p class="text-muted fs-8 mt-1 mb-0">Hệ thống son môi & mỹ phẩm cao cấp</p>
        </div>

        <!-- Auth Card -->
        <div class="auth-card">
            <div class="top-accent-bar"></div>
            <div class="p-4 p-md-45">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm text-white fs-8 mb-3" style="background: linear-gradient(135deg, #10b981, #059669);" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm text-white fs-8 mb-3" style="background: linear-gradient(135deg, #ef4444, #dc2626);" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @yield('content')
            </div>
        </div>

        <!-- Footer Back Link -->
        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="text-muted text-decoration-none fs-8 d-inline-flex align-items-center gap-1 hover-rose">
                <i class="bi bi-arrow-left"></i> Quay lại trang chủ LuxeLips
            </a>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

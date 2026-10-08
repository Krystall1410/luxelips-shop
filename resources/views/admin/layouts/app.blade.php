<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản Trị Cửa Hàng Son Môi | LuxeLips Admin')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Admin Master Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin/theme.css') }}">
    @yield('styles')
</head>

<body data-admin-id="{{ Auth::id() }}">

    <!-- [Phần 1]: Include Sidebar -->
    @include('admin.layouts.sidebar')

    <!-- Page Content Wrapper -->
    <div id="content-wrapper">

        <!-- [Phần 2]: Include Header Topbar -->
        @include('admin.layouts.header')

        <!-- [Phần 3]: Dynamic Render Content theo đường dẫn route -->
        <main class="main-content">
            @yield('content')
        </main>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')

    <!-- Admin Livechat Floating Multitasking Widget (Permanent Bottom-Right Pill) -->
    <div id="admin-chat-widget" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050;">

        <!-- 1. Expanded Floating Panel (Full Window) -->
        <div id="admin-chat-panel" class="shadow-lg border-0 rounded-4 overflow-hidden d-none" style="width: 840px; max-width: 94vw; height: 560px; max-height: 82vh; background-color: #0b1329; border: 1px solid #233054 !important; display: flex; flex-direction: column;">
            <!-- Panel Header -->
            <div class="py-2.5 px-4 border-bottom border-secondary d-flex align-items-center justify-content-between" style="background-color: #131d38; border-color: #233054 !important; flex-shrink: 0; min-height: 46px; padding-left: 20px !important;">
                <div class="d-flex align-items-center gap-2" style="margin-left: 6px;">
                    <i class="bi bi-chat-dots-fill fs-5" style="color: #ec4899;"></i>
                    <span class="fw-bold text-white" style="font-size: 0.92rem; letter-spacing: 0.3px;">Hỗ Trợ Khách Hàng </span>
                    <span id="admin-chat-widget-unread-badge" class="badge bg-danger rounded-pill d-none ms-1" style="font-size: 0.7rem; padding: 4px 8px;">0</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" id="admin-chat-minimize-btn" class="btn btn-sm btn-link text-slate-400 p-1 me-1 hover-white" title="Thu gọn" style="color: #94a3b8; text-decoration: none;">
                        <i class="bi bi-dash-lg fs-5"></i>
                    </button>
                    <button type="button" id="admin-chat-close-btn" class="btn btn-sm btn-link text-slate-400 p-1 hover-white" title="Thu gọn về nút nổi" style="color: #94a3b8; text-decoration: none;">
                        <i class="bi bi-x-lg fs-6"></i>
                    </button>
                </div>
            </div>

            <!-- Panel Body: Left Customer List & Right Active Conversation -->
            <div class="p-0 d-flex flex-grow-1 overflow-hidden">
                <!-- Left Sidebar: Customer List -->
                <div class="d-flex flex-column border-end border-secondary" style="width: 290px; flex-shrink: 0; background-color: #131d38; border-color: #233054 !important;">
                    <div class="p-2 border-bottom border-secondary" style="border-color: #233054 !important;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text border-secondary text-slate-400 py-1 px-2.5" style="background-color: #0b1329 !important; border-color: #233054 !important;"><i class="bi bi-search fs-8"></i></span>
                            <input type="text" id="admin-chat-search-user" class="form-control form-control-sm border-secondary text-white py-1 pe-3" placeholder="Tìm tên / email..." style="background-color: #0b1329 !important; border-color: #233054 !important; font-size: 0.82rem;">
                        </div>
                    </div>

                    <div id="admin-chat-user-list" class="flex-grow-1 overflow-y-auto d-flex flex-column">
                        <div class="text-center text-muted p-4 my-auto fs-8">
                            <div class="spinner-border spinner-border-sm text-pink mb-2" style="color: #ec4899;" role="status"></div>
                            <div>Đang tải danh sách...</div>
                        </div>
                    </div>
                </div>

                <!-- Right Pane: Active Conversation -->
                <div class="flex-grow-1 d-flex flex-column" style="background-color: rgba(11, 19, 41, 0.75);">
                    <!-- Active Customer Header -->
                    <div id="admin-chat-active-header" class="py-2.5 px-4 border-bottom border-secondary d-flex align-items-center justify-content-between" style="background-color: #0b1329; border-color: #233054 !important; min-height: 46px; padding-left: 20px !important;">
                        <div class="text-slate-400" style="color: #94a3b8; font-size: 0.82rem; margin-left: 4px;">
                            <i class="bi bi-hand-index-thumb me-1.5" style="color: #ec4899;"></i> Chọn một khách hàng bên trái để bắt đầu trao đổi.
                        </div>
                    </div>

                    <!-- Messages Box -->
                    <div id="admin-chat-messages" class="flex-grow-1 p-4 overflow-y-auto d-flex flex-column gap-2.5" style="scroll-behavior: smooth;">
                        <div class="text-center text-muted my-auto py-5">
                            <i class="bi bi-chat-quote fs-1 opacity-50" style="color: #3b82f6;"></i>
                            <div class="mt-2" style="color: #94a3b8; font-size: 0.85rem;">Chưa chọn cuộc hội thoại nào.</div>
                        </div>
                    </div>

                    <!-- Input Footer -->
                    <div class="p-3 bg-dark border-top border-secondary" style="background-color: #131d38 !important; border-color: #233054 !important;">
                        <div class="input-group">
                            <input type="text" id="admin-chat-input" class="form-control border-secondary text-white ps-3 py-2" placeholder="Nhập câu trả lời cho khách hàng..." disabled style="background-color: #0b1329; border-color: #233054; font-size: 0.88rem; border-radius: 8px 0 0 8px;">
                            <button id="admin-chat-send-btn" class="btn btn-rose px-4 fw-semibold" disabled style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); border: none; font-size: 0.88rem; border-radius: 0 8px 8px 0;">
                                <i class="bi bi-send-fill text-white me-1"></i> Gửi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Permanent Minimized Launcher Bar -->
        <div id="admin-chat-minimized" class="shadow-lg rounded-pill py-2 px-4 d-flex align-items-center gap-2 cursor-pointer" style="background-color: #131d38; border: 1px solid #ec4899 !important; color: #ffffff; cursor: pointer; transition: transform 0.2s;">
            <i class="bi bi-chat-dots-fill fs-5" style="color: #ec4899;"></i>

            <span id="admin-chat-minimized-badge" class="badge bg-danger rounded-pill d-none ms-1" style="font-size: 0.72rem; padding: 4px 7px;">0</span>
            <i class="bi bi-chevron-up ms-1 text-slate-400" style="font-size: 0.8rem; color: #94a3b8;"></i>
        </div>
    </div>

    <!-- Admin Livechat Script -->
    <script src="{{ asset('js/admin/chat.js') }}"></script>
</body>

</html>
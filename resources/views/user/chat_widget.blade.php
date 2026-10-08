<!-- Floating Livechat Widget for LuxeLips -->
<div id="user-livechat-container"
    data-logged-in="{{ Auth::check() ? '1' : '0' }}"
    data-user-id="{{ Auth::id() }}"
    data-user-name="{{ Auth::check() ? Auth::user()->name : '' }}"
    data-messages-url="{{ Auth::check() ? route('user.chat.messages') : '' }}"
    data-send-url="{{ Auth::check() ? route('user.chat.send') : '' }}"
    data-unread-url="{{ Auth::check() ? route('user.chat.unread_count') : '' }}"
    data-login-url="{{ route('login') }}">

    <!-- Floating Trigger Button -->
    <button id="user-chat-toggle" class="btn rounded-circle d-flex align-items-center justify-content-center"
        aria-label="Trò chuyện với LuxeLips">
        <i class="bi bi-chat-dots-fill fs-4" id="chat-icon-open" style="color: #ffffff !important;"></i>
        <i class="bi bi-x-lg fs-4 d-none" id="chat-icon-close" style="color: #ffffff !important;"></i>
        <span id="user-chat-badge" class="badge rounded-pill bg-danger position-absolute border border-white d-none">0</span>
    </button>

    @auth
    <!-- Chat Popup Window for Logged-in Users -->
    <div id="user-chat-popup" class="card shadow-lg border-0 d-none">
        <!-- Header -->
        <div class="card-header p-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                        style="width: 38px; height: 38px; background: linear-gradient(135deg, #ec4899, #8b5cf6);">
                        <i class="bi bi-headset"></i>
                    </div>
                    <span class="position-absolute bottom-0 end-0 rounded-circle bg-success border border-white" style="width: 10px; height: 10px;"></span>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold fs-6 text-dark">Hỗ Trợ LuxeLips</h6>
                    <small class="text-success fs-8 d-flex align-items-center gap-1">
                        <span class="spinner-grow spinner-grow-sm" style="width: 6px; height: 6px;" role="status"></span> Trực tuyến 24/7
                    </small>
                </div>
            </div>
            <button id="user-chat-close-btn" class="btn btn-sm btn-link p-1 text-muted text-decoration-none">
                <i class="bi bi-x-lg fs-6"></i>
            </button>
        </div>

        <!-- Chat Messages Area -->
        <div id="user-chat-messages" class="card-body p-3 flex-grow-1 overflow-y-auto d-flex flex-column gap-2">
            <div class="text-center text-muted my-auto py-4">
                <div class="spinner-border spinner-border-sm text-pink mb-2" role="status" style="color: #ec4899;"></div>
                <div class="fs-8 text-secondary">Đang kết nối lịch sử trò chuyện...</div>
            </div>
        </div>

        <!-- Footer Input Area -->
        <div class="card-footer p-2.5 border-top bg-white">
            <div class="input-group">
                <input type="text" id="user-chat-input" class="form-control form-control-sm rounded-pill-start ps-3"
                    placeholder="Nhập câu hỏi của bạn..." autocomplete="off">
                <button id="user-chat-send-btn" class="btn btn-rose rounded-pill-end px-3 d-flex align-items-center justify-content-center">
                    <i class="bi bi-send-fill text-white fs-7"></i>
                </button>
            </div>
        </div>
    </div>
    @else
    <!-- Guest Notification Popup -->
    <div id="user-chat-guest-popup" class="card shadow-lg border-0 d-none"
        style="position: fixed; bottom: 92px; right: 24px; width: 340px; max-width: calc(100vw - 32px); border-radius: 20px; overflow: hidden; z-index: 10000; border: 1px solid rgba(236,72,153,0.25);">
        <div class="card-header p-3 border-bottom d-flex align-items-center justify-content-between"
            style="background: linear-gradient(135deg, #ffffff 0%, #fff1f2 100%);">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                    style="width: 36px; height: 36px; background: linear-gradient(135deg, #ec4899, #8b5cf6);">
                    <i class="bi bi-chat-heart-fill"></i>
                </div>
                <h6 class="mb-0 fw-bold fs-6 text-dark">Hỗ Trợ Khách Hàng</h6>
            </div>
            <button id="user-chat-guest-close-btn" class="btn btn-sm btn-link p-1 text-muted text-decoration-none">
                <i class="bi bi-x-lg fs-6"></i>
            </button>
        </div>
        <div class="card-body p-4 text-center">
            <div class="mb-3">
                <i class="bi bi-person-lock fs-1 text-pink" style="color: #ec4899;"></i>
            </div>
            <h6 class="fw-bold mb-2">Đăng nhập để trò chuyện</h6>
            <p class="text-secondary fs-8 mb-4">Vui lòng đăng nhập tài khoản để tư vấn viên LuxeLips có thể hỗ trợ và lưu lại lịch sử tin nhắn của bạn.</p>
            <div class="d-grid gap-2">
                <a href="{{ route('login') }}" class="btn btn-rose rounded-pill py-2 fw-semibold text-white">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Đăng Nhập Ngay
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-secondary rounded-pill py-1.5 fs-8">
                    Chưa có tài khoản? Đăng ký
                </a>
            </div>
        </div>
    </div>
    @endauth
</div>

<script src="{{ asset('js/user/chat-widget.js') }}"></script>
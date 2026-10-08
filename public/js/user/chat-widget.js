/**
 * LuxeLips Livechat Widget
 * Handles user chat floating toggle, polling, message rendering, sending, and guest prompt.
 */
document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("user-livechat-container");
    if (!container) return;

    const isLoggedIn = container.dataset.loggedIn === "1";
    const currentUserId = container.dataset.userId || null;
    const currentUserName = container.dataset.userName || "Quý khách";
    const messagesUrl = container.dataset.messagesUrl || "";
    const sendUrl = container.dataset.sendUrl || "";
    const unreadUrl = container.dataset.unreadUrl || "";
    const loginUrl = container.dataset.loginUrl || "/login";

    const toggleBtn = document.getElementById("user-chat-toggle");
    const chatPopup = document.getElementById("user-chat-popup");
    const guestPopup = document.getElementById("user-chat-guest-popup");
    const closeBtn = document.getElementById("user-chat-close-btn");
    const guestCloseBtn = document.getElementById("user-chat-guest-close-btn");
    const sendBtn = document.getElementById("user-chat-send-btn");
    const input = document.getElementById("user-chat-input");
    const messagesBox = document.getElementById("user-chat-messages");
    const badge = document.getElementById("user-chat-badge");
    const iconOpen = document.getElementById("chat-icon-open");
    const iconClose = document.getElementById("chat-icon-close");

    if (!toggleBtn) return;

    let isChatOpen = false;

    // Toggle Chat Window
    toggleBtn.onclick = function () {
        if (!isLoggedIn) {
            if (guestPopup) {
                if (guestPopup.classList.contains("d-none")) {
                    guestPopup.classList.remove("d-none");
                    iconOpen.classList.add("d-none");
                    iconClose.classList.remove("d-none");
                } else {
                    closeGuestPopup();
                }
            } else {
                window.location.href = loginUrl;
            }
            return;
        }

        if (isChatOpen) {
            closeChat();
        } else {
            openChat();
        }
    };

    if (closeBtn) closeBtn.onclick = closeChat;
    if (guestCloseBtn) guestCloseBtn.onclick = closeGuestPopup;

    function openChat() {
        if (!chatPopup) return;
        isChatOpen = true;
        chatPopup.classList.remove("d-none");
        if (iconOpen) iconOpen.classList.add("d-none");
        if (iconClose) iconClose.classList.remove("d-none");
        if (badge) {
            badge.classList.add("d-none");
            badge.innerText = "0";
        }
        loadUserMessages();
        if (input) setTimeout(() => input.focus(), 150);
    }

    function closeChat() {
        isChatOpen = false;
        if (chatPopup) chatPopup.classList.add("d-none");
        if (iconOpen) iconOpen.classList.remove("d-none");
        if (iconClose) iconClose.classList.add("d-none");
    }

    function closeGuestPopup() {
        if (guestPopup) guestPopup.classList.add("d-none");
        if (iconOpen) iconOpen.classList.remove("d-none");
        if (iconClose) iconClose.classList.add("d-none");
    }

    // Load Messages via AJAX
    function loadUserMessages() {
        if (!isLoggedIn || !messagesUrl || !messagesBox) return;

        fetch(messagesUrl, {
            headers: { "Accept": "application/json" }
        })
            .then(res => res.json())
            .then(messages => {
                let html = "";
                if (!messages || messages.length === 0) {
                    html = `
                        <div class="text-center text-muted my-auto py-4">
                            <div class="rounded-circle d-inline-flex p-3 mb-2" style="background-color: rgba(236, 72, 153, 0.1); color: #ec4899;">
                                <i class="bi bi-chat-heart fs-3"></i>
                            </div>
                            <h6 class="fw-bold fs-7 mb-1" style="color: #0f172a;">Xin chào ${escapeHtml(currentUserName)}!</h6>
                            <p class="fs-8 mb-0" style="color: #64748b;">Gửi câu hỏi để nhận hỗ trợ nhanh chóng từ tư vấn viên LuxeLips.</p>
                        </div>
                    `;
                } else {
                    messages.forEach(msg => {
                        const isMe = (String(msg.sender_id) === String(currentUserId));
                        if (isMe) {
                            html += `
                                <div class="d-flex flex-column align-items-end mb-2">
                                    <div class="p-2.5 px-3 rounded-3 text-white fs-7 shadow-sm max-w-85" 
                                         style="background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%); border-bottom-right-radius: 4px !important; word-break: break-word;">
                                        ${escapeHtml(msg.content)}
                                    </div>
                                    <span class="mt-1" style="font-size: 0.68rem; color: #64748b;">${escapeHtml(msg.time_formatted || '')}</span>
                                </div>
                            `;
                        } else {
                            html += `
                                <div class="d-flex flex-column align-items-start mb-2">
                                    <div class="fw-semibold mb-1" style="font-size: 0.72rem; color: #ec4899;">
                                        <i class="bi bi-shield-check me-1"></i>LuxeLips Chuyên Viên
                                    </div>
                                    <div class="p-2.5 px-3 rounded-3 fs-7 shadow-sm max-w-85" 
                                         style="background-color: #ffffff; color: #0f172a; border: 1px solid rgba(236, 72, 153, 0.22); border-bottom-left-radius: 4px !important; word-break: break-word;">
                                        ${escapeHtml(msg.content)}
                                    </div>
                                    <span class="mt-1" style="font-size: 0.68rem; color: #64748b;">${escapeHtml(msg.time_formatted || '')}</span>
                                </div>
                            `;
                        }
                    });
                }

                messagesBox.innerHTML = html;
                messagesBox.scrollTop = messagesBox.scrollHeight;
            })
            .catch(err => console.error("Lỗi tải tin nhắn:", err));
    }

    // Send Message
    function sendUserMessage() {
        if (!isLoggedIn || !sendUrl || !input || !sendBtn) return;
        const text = input.value.trim();
        if (!text) return;

        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

        input.disabled = true;
        sendBtn.disabled = true;

        fetch(sendUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({ message: text })
        })
            .then(res => res.json())
            .then(data => {
                input.value = "";
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
                loadUserMessages();
            })
            .catch(err => {
                console.error("Lỗi gửi tin nhắn:", err);
                input.disabled = false;
                sendBtn.disabled = false;
            });
    }

    if (sendBtn) sendBtn.onclick = sendUserMessage;
    if (input) {
        input.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                sendUserMessage();
            }
        });
    }

    // Check Unread Badge Count
    function checkUnreadCount() {
        if (!isLoggedIn || !unreadUrl || isChatOpen || !badge) return;
        fetch(unreadUrl)
            .then(res => res.json())
            .then(data => {
                if (data && data.unread_count > 0) {
                    badge.innerText = data.unread_count;
                    badge.classList.remove("d-none");
                } else {
                    badge.classList.add("d-none");
                }
            })
            .catch(err => console.error("Lỗi đếm tin nhắn:", err));
    }

    function escapeHtml(text) {
        if (!text) return "";
        const div = document.createElement("div");
        div.innerText = text;
        return div.innerHTML;
    }

    // Live Polling every 3.5 seconds
    if (isLoggedIn) {
        setInterval(function () {
            if (isChatOpen) {
                loadUserMessages();
            } else {
                checkUnreadCount();
            }
        }, 3500);

        checkUnreadCount();
    }
});

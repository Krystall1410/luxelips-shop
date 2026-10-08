/**
 * LuxeLips - Admin Livechat Floating Multitasking Widget Script
 */
document.addEventListener("DOMContentLoaded", function () {
    const widgetContainer = document.getElementById("admin-chat-widget");
    const widgetPanel = document.getElementById("admin-chat-panel");
    const widgetMinimized = document.getElementById("admin-chat-minimized");
    const minimizeBtn = document.getElementById("admin-chat-minimize-btn");
    const closeBtn = document.getElementById("admin-chat-close-btn");
    const widgetUnreadBadge = document.getElementById("admin-chat-widget-unread-badge");
    const minimizedBadge = document.getElementById("admin-chat-minimized-badge");

    const userListContainer = document.getElementById("admin-chat-user-list");
    const activeHeader = document.getElementById("admin-chat-active-header");
    const messagesContainer = document.getElementById("admin-chat-messages");
    const chatInput = document.getElementById("admin-chat-input");
    const sendBtn = document.getElementById("admin-chat-send-btn");
    const searchInput = document.getElementById("admin-chat-search-user");

    if (!widgetContainer) return;

    let activeUserId = null;
    let activeUserObj = null;
    let allUsersData = [];

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const currentAdminId = document.body.dataset.adminId || '';

    function minimizePanel() {
        if (widgetPanel) widgetPanel.classList.add("d-none");
        if (widgetMinimized) widgetMinimized.classList.remove("d-none");
    }

    function expandPanel() {
        if (widgetPanel) widgetPanel.classList.remove("d-none");
        if (widgetMinimized) widgetMinimized.classList.add("d-none");
        loadAdminUsers();
    }

    if (minimizeBtn) minimizeBtn.onclick = minimizePanel;
    if (closeBtn) closeBtn.onclick = minimizePanel;
    if (widgetMinimized) widgetMinimized.onclick = expandPanel;

    // Load Customer Conversations List
    function loadAdminUsers() {
        fetch("/admin/chat/users", {
            headers: {
                "Accept": "application/json"
            }
        })
            .then(res => res.json())
            .then(users => {
                allUsersData = users || [];
                renderUserList(allUsersData);
            })
            .catch(err => console.error("Lỗi tải danh sách khách hàng:", err));
    }

    function renderUserList(users) {
        if (!userListContainer) return;
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const filtered = users.filter(u => u.name.toLowerCase().includes(searchTerm) || (u.email && u.email.toLowerCase().includes(searchTerm)));

        if (filtered.length === 0) {
            userListContainer.innerHTML = '<div class="text-center text-muted p-4 fs-8" style="color: #94a3b8;">Không tìm thấy cuộc hội thoại.</div>';
            return;
        }

        let html = "";
        filtered.forEach(u => {
            const isActive = (activeUserId == u.id) ? "style='background-color: #1c2a4f; border-left: 3px solid #ec4899;'" : "";
            const badgeHtml = u.unread_count > 0 ? `<span class="badge bg-danger rounded-pill" style="font-size: 0.68rem; padding: 2px 6px;">${u.unread_count}</span>` : '';

            html += `
            <div class="p-2.5 px-3 border-bottom user-chat-item cursor-pointer d-flex align-items-center" 
                 data-user-id="${u.id}" ${isActive} style="border-color: rgba(255,255,255,0.06) !important; transition: all 0.2s;">
                <div class="position-relative flex-shrink-0 me-2.5" style="width: 38px; height: 38px;">
                    <img src="${u.avatar_url}" alt="${escapeHtml(u.name)}" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover; display: block; border: 1px solid rgba(255,255,255,0.1);">
                </div>
                <div class="flex-grow-1 overflow-hidden d-flex flex-column justify-content-center" style="line-height: 1.2;">
                    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 2px;">
                        <span class="fw-bold text-white text-truncate" style="font-size: 0.85rem; line-height: 1.2; max-width: 140px;">${escapeHtml(u.name)}</span>
                        ${badgeHtml}
                    </div>
                    <div class="text-truncate" style="color: #94a3b8; font-size: 0.76rem; line-height: 1.2;">${escapeHtml(u.latest_message || 'Chưa có tin nhắn')}</div>
                </div>
            </div>
        `;
        });

        userListContainer.innerHTML = html;

        // Bind Click Events on User Items
        userListContainer.querySelectorAll('.user-chat-item').forEach(el => {
            el.onclick = function () {
                const uId = this.getAttribute('data-user-id');
                const targetUser = allUsersData.find(x => x.id == uId);
                if (targetUser) {
                    selectCustomer(targetUser);
                }
            };
        });
    }

    if (searchInput) {
        searchInput.oninput = function () {
            renderUserList(allUsersData);
        };
    }

    // Select Customer Conversation
    function selectCustomer(user) {
        activeUserId = user.id;
        activeUserObj = user;

        // Update Header Info
        if (activeHeader) {
            activeHeader.innerHTML = `
            <div class="d-flex align-items-center" style="margin-left: 4px;">
                <img src="${user.avatar_url}" alt="${escapeHtml(user.name)}" class="rounded-circle me-2.5" style="width: 32px; height: 32px; object-fit: cover; border: 1.5px solid #ec4899;">
                <div style="line-height: 1.2;">
                    <span class="fw-bold text-white me-2" style="font-size: 0.88rem;">${escapeHtml(user.name)}</span>
                    <small style="color: #94a3b8; font-size: 0.75rem;">${escapeHtml(user.email || '')}</small>
                </div>
            </div>
            `;
        }

        if (chatInput) {
            chatInput.disabled = false;
            chatInput.focus();
        }
        if (sendBtn) sendBtn.disabled = false;

        renderUserList(allUsersData);
        loadAdminMessages();
    }

    // Load Messages for Active Customer
    function loadAdminMessages() {
        if (!activeUserId || !messagesContainer) return;

        fetch(`/admin/chat/messages/${activeUserId}`, {
            headers: {
                "Accept": "application/json"
            }
        })
            .then(res => res.json())
            .then(messages => {
                let html = "";
                if (!messages || messages.length === 0) {
                    html = '<div class="text-center text-muted my-auto py-4" style="font-size: 0.82rem; color: #94a3b8;">Chưa có tin nhắn nào trong cuộc trò chuyện này.</div>';
                } else {
                    messages.forEach(msg => {
                        const isAdminMsg = (msg.sender_id == currentAdminId);
                        if (isAdminMsg) {
                            html += `
                            <div class="d-flex flex-column align-items-end mb-2">
                                <div class="p-2.5 px-3 rounded-3 text-white shadow-sm" 
                                     style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); border-bottom-right-radius: 2px !important; word-break: break-word; font-size: 0.88rem; line-height: 1.45; max-width: 82%;">
                                    ${escapeHtml(msg.content)}
                                </div>
                                <span class="mt-1" style="font-size: 0.68rem; color: #64748b;">${msg.time_formatted || ''}</span>
                            </div>
                        `;
                        } else {
                            html += `
                            <div class="d-flex flex-column align-items-start mb-2">
                                <div class="fw-semibold mb-1" style="font-size: 0.72rem; color: #60a5fa;"><i class="bi bi-person me-1"></i>${escapeHtml(activeUserObj ? activeUserObj.name : 'Khách hàng')}</div>
                                <div class="p-2.5 px-3 rounded-3 text-white shadow-sm" 
                                     style="background-color: #1a274c; border: 1px solid #233054; border-bottom-left-radius: 2px !important; word-break: break-word; font-size: 0.88rem; line-height: 1.45; max-width: 82%;">
                                    ${escapeHtml(msg.content)}
                                </div>
                                <span class="mt-1" style="font-size: 0.68rem; color: #64748b;">${msg.time_formatted || ''}</span>
                            </div>
                        `;
                        }
                    });
                }

                messagesContainer.innerHTML = html;
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            })
            .catch(err => console.error("Lỗi tải tin nhắn Admin:", err));
    }

    // Send Reply from Admin
    function sendAdminReply() {
        if (!chatInput) return;
        const text = chatInput.value.trim();
        if (!text || !activeUserId) return;

        chatInput.disabled = true;
        if (sendBtn) sendBtn.disabled = true;

        fetch("/admin/chat/send", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                user_id: activeUserId,
                message: text
            })
        })
            .then(res => res.json())
            .then(data => {
                chatInput.value = "";
                chatInput.disabled = false;
                if (sendBtn) sendBtn.disabled = false;
                chatInput.focus();
                loadAdminMessages();
                loadAdminUsers();
            })
            .catch(err => {
                console.error("Lỗi gửi tin nhắn Admin:", err);
                chatInput.disabled = false;
                if (sendBtn) sendBtn.disabled = false;
            });
    }

    if (sendBtn) sendBtn.onclick = sendAdminReply;
    if (chatInput) {
        chatInput.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                sendAdminReply();
            }
        });
    }

    // Check Global Admin Unread Count & Update Dynamic Badges
    function checkAdminUnreadCount() {
        fetch("/admin/chat/unread-count")
            .then(res => res.json())
            .then(data => {
                const count = data.unread_count || 0;

                if (count > 0) {
                    if (widgetUnreadBadge) {
                        widgetUnreadBadge.innerText = count;
                        widgetUnreadBadge.classList.remove("d-none");
                    }
                    if (minimizedBadge) {
                        minimizedBadge.innerText = count;
                        minimizedBadge.classList.remove("d-none");
                    }
                } else {
                    if (widgetUnreadBadge) widgetUnreadBadge.classList.add("d-none");
                    if (minimizedBadge) minimizedBadge.classList.add("d-none");
                }
            })
            .catch(err => console.error("Lỗi kiểm tra tin nhắn chưa đọc Admin:", err));
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text || '';
        return div.innerHTML;
    }

    // Polling every 3 seconds
    setInterval(function () {
        checkAdminUnreadCount();
        if (widgetPanel && !widgetPanel.classList.contains('d-none')) {
            loadAdminUsers();
            if (activeUserId) {
                loadAdminMessages();
            }
        }
    }, 3000);

    checkAdminUnreadCount();
});

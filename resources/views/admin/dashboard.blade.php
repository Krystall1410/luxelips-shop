@extends('admin.layouts.app')

@section('title', 'Dashboard Tổng Hợp | LuxeLips Admin')
@section('header_title', 'Báo Cáo & Thống Kê Tổng Hợp')

@section('content')
<div class="container-fluid px-0">

    <!-- Hero Welcome Banner -->
    <div class="card border-0 rounded-4 mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, rgba(236,72,153,0.18) 0%, rgba(19,29,56,0.95) 100%); border: 1px solid var(--border-color) !important;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-7 mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge rounded-pill px-3 py-1 fs-8 fw-semibold" style="background: linear-gradient(135deg, #ec4899, #e11d48) !important; color: #ffffff !important;"> LuxeLips Admin System</span>
                    </div>
                    <h3 class="fw-bold text-white mb-1 brand-logo" style="font-family: 'Plus Jakarta Sans', sans-serif;">Xin chào, {{ Auth::user()->name }}! </h3>
                    <p class="text-slate-300 fs-7 mb-0" style="color: #cbd5e1;"> Cập nhật mới nhất: <span id="last-synced-time" class="fw-semibold text-white">{{ now()->format('H:i:s d/m/Y') }}</span></p>
                </div>
                <div class="col-md-5 text-md-end d-flex align-items-center justify-content-md-end gap-2">
                    <button type="button" class="btn btn-outline-light rounded-2 px-3 text-white fw-semibold d-inline-flex align-items-center justify-content-center" id="btn-manual-refresh" title="Làm mới dữ liệu ngay lập tức" style="height: 36px; font-size: 0.85rem; border: 1px solid rgba(255,255,255,0.25); box-sizing: border-box;">
                        <i class="bi bi-arrow-clockwise me-1.5" id="refresh-icon"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-rose rounded-2 px-3 text-white fw-semibold shadow-sm d-inline-flex align-items-center justify-content-center" style="height: 36px; background: linear-gradient(135deg, #ec4899, #d946ef); border: 1px solid transparent; font-size: 0.85rem; box-sizing: border-box;">
                        <i class="bi bi-plus-lg me-1.5"></i> Thêm Sản Phẩm
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Metric Cards Row (4 Cards) -->
    <div class="row g-3 mb-4">
        <!-- 1. Doanh Thu -->
        <div class="col-sm-6 col-xl-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #10b981;">
                    <i class="bi bi-currency-dollar fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Doanh thu thực nhận</div>
                    <div class="fw-bold text-success" id="stat-total-revenue" style="font-size: 1.15rem;">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</div>
                </div>
            </div>
        </div>

        <!-- 2. Tổng Đơn Hàng -->
        <div class="col-sm-6 col-xl-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #ec4899;">
                    <i class="bi bi-bag-check-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Tổng đơn hàng</div>
                    <div class="fw-bold text-light" id="stat-total-orders" style="font-size: 1.15rem;">{{ number_format($totalOrders) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 3. Tổng Sản Phẩm -->
        <div class="col-sm-6 col-xl-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #8b5cf6;">
                    <i class="bi bi-box-seam-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Sản phẩm trong kho</div>
                    <div class="fw-bold text-light" id="stat-total-products" style="font-size: 1.15rem;">{{ number_format($totalProducts) }} mã</div>
                </div>
            </div>
        </div>

        <!-- 4. Khách Hàng -->
        <div class="col-sm-6 col-xl-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #3b82f6;">
                    <i class="bi bi-people-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Khách hàng đăng ký</div>
                    <div class="fw-bold text-light" id="stat-total-users" style="font-size: 1.15rem;">{{ number_format($totalUsers) }} người</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Breakdown Row (4 Counter Pills) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="rounded-3 d-flex align-items-center justify-content-between" style="padding: 0.65rem 1rem !important; background-color: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-hourglass-split text-warning" style="font-size: 0.95rem;"></i>
                    <span class="text-slate-300 fw-medium" style="font-size: 0.78rem;">Chờ xử lý</span>
                </div>
                <span class="badge bg-warning text-dark fw-bold rounded-pill" style="font-size: 0.75rem; padding: 2px 8px;" id="stat-pending-orders">{{ $pendingOrdersCount }}</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="rounded-3 d-flex align-items-center justify-content-between" style="padding: 0.65rem 1rem !important; background-color: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.25);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-truck text-info" style="font-size: 0.95rem;"></i>
                    <span class="text-slate-300 fw-medium" style="font-size: 0.78rem;">Đang vận chuyển</span>
                </div>
                <span class="badge bg-info text-white fw-bold rounded-pill" style="font-size: 0.75rem; padding: 2px 8px;" id="stat-shipping-orders">{{ $shippingOrdersCount }}</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="rounded-3 d-flex align-items-center justify-content-between" style="padding: 0.65rem 1rem !important; background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 0.95rem;"></i>
                    <span class="text-slate-300 fw-medium" style="font-size: 0.78rem;">Đã hoàn tất</span>
                </div>
                <span class="badge bg-success text-white fw-bold rounded-pill" style="font-size: 0.75rem; padding: 2px 8px;" id="stat-completed-orders">{{ $completedOrdersCount }}</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="rounded-3 d-flex align-items-center justify-content-between" style="padding: 0.65rem 1rem !important; background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 0.95rem;"></i>
                    <span class="text-slate-300 fw-medium" style="font-size: 0.78rem;">Đã hủy / Hoàn</span>
                </div>
                <span class="badge bg-danger text-white fw-bold rounded-pill" style="font-size: 0.75rem; padding: 2px 8px;" id="stat-cancelled-orders">{{ $cancelledOrdersCount }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Left Column: Recent Orders & Recent Products -->
        <div class="col-lg-7">
            <!-- 1. Recent Orders Card -->
            <div class="card border-0 rounded-4 mb-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center" style="background-color: transparent; border-color: var(--border-color) !important;">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.05rem;">
                        <i class="bi bi-receipt-cutoff text-rose" style="color: #ec4899;"></i> Đơn Hàng Mới Nhất
                    </h5>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-rose rounded-pill px-3 fs-8">Xem tất cả đơn</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="--bs-table-bg: transparent; color: #f8fafc;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border-color); color: #cbd5e1; font-size: 0.8rem;">
                                    <th class="ps-4 py-3">Mã đơn</th>
                                    <th class="py-3">Khách hàng</th>
                                    <th class="py-3">Tổng tiền</th>
                                    <th class="text-end pe-4 py-3">Thời gian</th>
                                </tr>
                            </thead>
                            <tbody id="recent-orders-tbody">
                                @forelse($recentOrders as $o)
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="ps-4 py-3">
                                        <span class="fw-bold text-info fs-7">#{{ $o->order_code ?? $o->id }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-white fs-7">{{ $o->name }}</div>
                                    </td>
                                    <td>
                                        <strong class="text-success fs-7" style="color: #34d399 !important;">{{ number_format($o->total_price, 0, ',', '.') }} ₫</strong>
                                    </td>
                                    <td class="text-end pe-4">
                                        <span class="text-slate-400 fs-8">{{ $o->created_at ? $o->created_at->format('d/m H:i') : '' }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted fs-7">Chưa có đơn hàng nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. Recent Products Card -->
            <div class="card border-0 rounded-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center" style="background-color: transparent; border-color: var(--border-color) !important;">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.05rem;">
                        <i class="bi bi-box-seam-fill text-warning" style="color: #f59e0b;"></i> Sản Phẩm Mới Đăng
                    </h5>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-rose rounded-pill px-3 fs-8">Xem kho hàng</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="--bs-table-bg: transparent; color: #f8fafc;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border-color); color: #cbd5e1; font-size: 0.8rem;">
                                    <th class="ps-4 py-3">Sản phẩm</th>
                                    <th class="py-3">Danh mục</th>
                                    <th class="py-3">Giá bán</th>
                                    <th class="text-end pe-4 py-3">Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody id="recent-products-tbody">
                                @forelse($recentProducts as $p)
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 overflow-hidden bg-dark flex-shrink-0" style="width: 40px; height: 40px; border: 1px solid var(--border-color);">
                                                @if($p->first_image_url)
                                                <img src="{{ $p->first_image_url }}" alt="{{ $p->name }}" class="w-100 h-100 object-fit-cover">
                                                @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-white mb-0 fs-7" style="font-family: 'Plus Jakarta Sans', sans-serif;">{{ $p->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark border border-secondary text-slate-300 fs-9" style="color: #cbd5e1; border-color: var(--border-color) !important;">
                                            {{ $p->variants->first()?->category?->name ?? 'Mỹ phẩm' }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-warning fs-7" style="color: #f59e0b !important;">{{ $p->price_range_formatted }}</strong>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($p->is_active)
                                        <span class="badge bg-success fs-9 text-white px-2 py-1">Đang bán</span>
                                        @else
                                        <span class="badge bg-secondary fs-9 text-white px-2 py-1">Tạm ẩn</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted fs-7">Chưa có sản phẩm nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Mini Revenue Chart & Recent Users -->
        <div class="col-lg-5">
            <!-- 1. Mini 7-Day Revenue Chart -->
            <div class="card border-0 rounded-4 mb-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center" style="background-color: transparent; border-color: var(--border-color) !important;">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.05rem;">
                        <i class="bi bi-graph-up text-info" style="color: #38bdf8;"></i> Doanh Thu 7 Ngày Qua
                    </h5>
                    <a href="{{ route('admin.reports.charts') }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fs-8">Xem biểu đồ lớn</a>
                </div>
                <div class="card-body p-4" style="height: 250px; position: relative;">
                    <canvas id="dashboard7DayChart" data-labels="{{ json_encode($chart7DayLabels) }}" data-values="{{ json_encode($chart7DayData) }}"></canvas>
                </div>
            </div>

            <!-- 2. Recent Users Card -->
            <div class="card border-0 rounded-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center" style="background-color: transparent; border-color: var(--border-color) !important;">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.05rem;">
                        <i class="bi bi-people-fill text-pink" style="color: #ec4899;"></i> Khách Hàng Mới
                    </h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-rose rounded-pill px-3 fs-8">Quản lý tài khoản</a>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3" id="recent-users-list">
                        @forelse($recentUsers as $u)
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background-color: rgba(11,19,41,0.7); border: 1px solid var(--border-color);">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover; border-color: var(--border-color) !important;">
                                <div>
                                    <h6 class="fw-bold text-white mb-0 fs-7" style="font-family: 'Plus Jakarta Sans', sans-serif;">{{ $u->name }}</h6>
                                    <span class="fs-8 text-slate-300" style="color: #cbd5e1; font-size: 0.78rem;">{{ $u->email }}</span>
                                </div>
                            </div>
                            <div>
                                @if($u->isAdmin())
                                <span class="badge bg-danger fs-9 text-white px-2 py-1 rounded-pill" style="background-color: #e11d48 !important;">Admin</span>
                                @else
                                <span class="badge bg-info fs-9 text-white px-2 py-1 rounded-pill" style="background-color: #06b6d4 !important;">Khách</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted fs-7">Chưa có người dùng nào.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statsApiUrl = '{{ route("admin.dashboard.stats") }}';
        const refreshBtn = document.getElementById('btn-manual-refresh');
        const refreshIcon = document.getElementById('refresh-icon');
        const lastSyncedEl = document.getElementById('last-synced-time');

        // Initial Chart Initialization
        let miniChartInstance = null;
        const chartCtx = document.getElementById('dashboard7DayChart');

        const initialLabels = JSON.parse(chartCtx ? (chartCtx.dataset.labels || '[]') : '[]');
        const initialData = JSON.parse(chartCtx ? (chartCtx.dataset.values || '[]') : '[]');

        if (chartCtx && typeof Chart !== 'undefined') {
            miniChartInstance = new Chart(chartCtx, {
                type: 'line',
                data: {
                    labels: initialLabels,
                    datasets: [{
                        label: 'Doanh thu (₫)',
                        data: initialData,
                        borderColor: '#38bdf8',
                        backgroundColor: 'rgba(56, 189, 248, 0.15)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#ec4899',
                        pointRadius: 4,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                        style: 'currency',
                                        currency: 'VND'
                                    }).format(ctx.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 10
                                },
                                callback: function(val) {
                                    if (val >= 1000000) return (val / 1000000) + 'Tr';
                                    if (val >= 1000) return (val / 1000) + 'k';
                                    return val;
                                }
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.06)'
                            }
                        }
                    }
                }
            });
        }

        function fetchLiveStats() {
            if (refreshIcon) refreshIcon.classList.add('spin');

            fetch(statsApiUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update Stat Cards
                        if (document.getElementById('stat-total-revenue')) document.getElementById('stat-total-revenue').textContent = data.total_revenue_formatted;
                        if (document.getElementById('stat-total-orders')) document.getElementById('stat-total-orders').textContent = data.total_orders + ' đơn';
                        if (document.getElementById('stat-total-products')) document.getElementById('stat-total-products').textContent = data.total_products + ' mã';
                        if (document.getElementById('stat-total-users')) document.getElementById('stat-total-users').textContent = data.total_users + ' người';
                        if (lastSyncedEl) lastSyncedEl.textContent = data.timestamp;

                        // Update Status Counters
                        if (document.getElementById('stat-pending-orders')) document.getElementById('stat-pending-orders').textContent = data.pending_orders_count;
                        if (document.getElementById('stat-shipping-orders')) document.getElementById('stat-shipping-orders').textContent = data.shipping_orders_count;
                        if (document.getElementById('stat-completed-orders')) document.getElementById('stat-completed-orders').textContent = data.completed_orders_count;
                        if (document.getElementById('stat-cancelled-orders')) document.getElementById('stat-cancelled-orders').textContent = data.cancelled_orders_count;

                        // Update Mini Chart
                        if (miniChartInstance && data.chart7_labels && data.chart7_data) {
                            miniChartInstance.data.labels = data.chart7_labels;
                            miniChartInstance.data.datasets[0].data = data.chart7_data;
                            miniChartInstance.update();
                        }

                        // Update Recent Orders Table
                        if (data.recent_orders && document.getElementById('recent-orders-tbody')) {
                            let html = '';
                            if (data.recent_orders.length === 0) {
                                html = '<tr><td colspan="4" class="text-center py-4 text-muted fs-7">Chưa có đơn hàng nào.</td></tr>';
                            } else {
                                data.recent_orders.forEach(o => {
                                    html += `
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td class="ps-4 py-3">
                                            <span class="fw-bold text-info fs-7">#${o.order_code}</span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-white fs-7">${o.name}</div>
                                        </td>
                                        <td>
                                            <strong class="text-success fs-7" style="color: #34d399 !important;">${o.total_price}</strong>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="text-slate-400 fs-8">${o.created_at_formatted}</span>
                                        </td>
                                    </tr>
                                    `;
                                });
                            }
                            document.getElementById('recent-orders-tbody').innerHTML = html;
                        }

                        // Update Recent Products
                        if (data.recent_products && document.getElementById('recent-products-tbody')) {
                            let html = '';
                            if (data.recent_products.length === 0) {
                                html = '<tr><td colspan="4" class="text-center py-4 text-muted fs-7">Chưa có sản phẩm nào.</td></tr>';
                            } else {
                                data.recent_products.forEach(p => {
                                    const imgHtml = p.image_url ?
                                        `<img src="${p.image_url}" alt="${p.name}" class="w-100 h-100 object-fit-cover">` :
                                        `<div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>`;
                                    const statusBadge = p.is_active ?
                                        '<span class="badge fs-9 text-white px-2 py-1" style="background-color: #10b981 !important;">Đang bán</span>' :
                                        '<span class="badge fs-9 text-white px-2 py-1" style="background-color: #64748b !important;">Tạm ẩn</span>';

                                    html += `
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-3 overflow-hidden bg-dark flex-shrink-0" style="width: 40px; height: 40px; border: 1px solid var(--border-color);">
                                                    ${imgHtml}
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-white mb-0 fs-7" style="font-family: 'Plus Jakarta Sans', sans-serif;">${p.name}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark border border-secondary text-slate-300 fs-9" style="color: #cbd5e1; border-color: var(--border-color) !important;">
                                                ${p.category_name}
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-warning fs-7" style="color: #f59e0b !important;">${p.price_formatted}</strong>
                                        </td>
                                        <td class="text-end pe-4">
                                            ${statusBadge}
                                        </td>
                                    </tr>
                                `;
                                });
                            }
                            document.getElementById('recent-products-tbody').innerHTML = html;
                        }

                        // Update Recent Users
                        if (data.recent_users && document.getElementById('recent-users-list')) {
                            let html = '';
                            if (data.recent_users.length === 0) {
                                html = '<div class="text-center py-4 text-muted fs-7">Chưa có người dùng nào.</div>';
                            } else {
                                data.recent_users.forEach(u => {
                                    const roleBadge = u.is_admin ?
                                        '<span class="badge fs-9 text-white px-2 py-1 rounded-pill" style="background-color: #e11d48 !important;">Admin</span>' :
                                        '<span class="badge fs-9 text-white px-2 py-1 rounded-pill" style="background-color: #06b6d4 !important;">Khách</span>';

                                    html += `
                                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background-color: rgba(11,19,41,0.7); border: 1px solid var(--border-color);">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="${u.avatar_url}" alt="${u.name}" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover; border-color: var(--border-color) !important;">
                                            <div>
                                                <h6 class="fw-bold text-white mb-0 fs-7" style="font-family: 'Plus Jakarta Sans', sans-serif;">${u.name}</h6>
                                                <span class="fs-8 text-slate-300" style="color: #cbd5e1; font-size: 0.78rem;">${u.email}</span>
                                            </div>
                                        </div>
                                        <div>
                                            ${roleBadge}
                                        </div>
                                    </div>
                                `;
                                });
                            }
                            document.getElementById('recent-users-list').innerHTML = html;
                        }
                    }
                })
                .catch(err => console.error('Error fetching live stats:', err))
                .finally(() => {
                    if (refreshIcon) refreshIcon.classList.remove('spin');
                });
        }

        // Manual Refresh Button Event
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                fetchLiveStats();
            });
        }

        // Auto Poll every 10 seconds for real-time updates
        setInterval(fetchLiveStats, 10000);
    });
</script>
@endsection
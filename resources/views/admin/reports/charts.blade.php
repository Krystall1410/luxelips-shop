@extends('admin.layouts.app')

@section('title', 'Biểu Đồ Báo Cáo Doanh Thu | LuxeLips Admin')
@section('header_title', 'Biểu Đồ Báo Cáo Doanh Thu')

@section('content')
    <style>
        .chart-wrap {
            position: relative;
            min-height: 340px;
            width: 100%;
        }

        .chart-wrap canvas {
            width: 100% !important;
            height: 340px !important;
        }
    </style>

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="page-title mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-pie-chart-fill" style="color: #ec4899;"></i>
                <span>Biểu Đồ Trực Quan Doanh Thu</span>
            </h1>
            <p class="text-muted mb-0 fs-7" style="color: #94a3b8 !important;">
                Biểu đồ thống kê theo danh mục, ngày, tháng, năm và phương thức thanh toán.
            </p>
        </div>
        <div>
            <ul class="nav nav-pills p-1.5 rounded-3 border" style="background-color: #131d38; border-color: #233054 !important;">
                <li class="nav-item">
                    <a class="nav-link px-3.5 py-2 fw-semibold d-flex align-items-center gap-2" style="color: #94a3b8; border-radius: 8px; transition: all 0.2s ease;" href="{{ route('admin.reports.index', request()->query()) }}">
                        <i class="bi bi-table"></i> <span>Bảng số liệu</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active px-3.5 py-2 fw-semibold d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%); color: #ffffff; box-shadow: 0 4px 12px rgba(236,72,153,0.35); border-radius: 8px;" href="{{ route('admin.reports.charts', request()->query()) }}">
                        <i class="bi bi-pie-chart-fill"></i> <span>Biểu đồ</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Seamless Filter Form --}}
    <div class="mb-4">
        <form action="{{ route('admin.reports.charts') }}" method="GET" class="row g-2.5 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Danh mục sản phẩm</label>
                <select name="category_id" class="form-select text-light admin-filter-input" style="background-color: #131d38; border: 1px solid #233054;">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($allCategories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Khoảng thời gian</label>
                <select name="preset" class="form-select text-light admin-filter-input" style="background-color: #131d38; border: 1px solid #233054;">
                    <option value="">-- Theo ngày --</option>
                    <option value="today" @selected(request('preset') == 'today')>Hôm nay</option>
                    <option value="7days" @selected(request('preset') == '7days')>7 ngày qua</option>
                    <option value="30days" @selected(request('preset') == '30days')>30 ngày qua</option>
                    <option value="this_month" @selected(request('preset') == 'this_month')>Tháng này</option>
                    <option value="this_year" @selected(request('preset') == 'this_year')>Năm nay</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Từ ngày</label>
                <input type="date" name="from_date" class="form-control text-light admin-filter-input" style="background-color: #131d38; border: 1px solid #233054;" value="{{ request('from_date') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Đến ngày</label>
                <input type="date" name="to_date" class="form-control text-light admin-filter-input" style="background-color: #131d38; border: 1px solid #233054;" value="{{ request('to_date') }}">
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-rose admin-filter-btn flex-grow-1" style="background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%); border: none;">
                    <i class="bi bi-funnel-fill me-1"></i> Lọc dữ liệu
                </button>
                @if(request('category_id') || request('preset') || request('from_date') || request('to_date'))
                    <a href="{{ route('admin.reports.charts') }}" class="btn btn-outline-secondary admin-filter-btn" title="Đặt lại bộ lọc">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div id="report-chart-error" class="alert alert-warning rounded-3 d-none mb-4" role="alert" style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid #f59e0b; color: #fbbf24;">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        Không tải được thư viện biểu đồ Chart.js. Bạn vẫn có thể xem số liệu dạng bảng tại trang 
        <a href="{{ route('admin.reports.index') }}" class="fw-bold text-decoration-underline text-warning">Bảng số liệu</a>.
    </div>

    <div class="row g-4 mb-4">
        {{-- Category Revenue Chart --}}
        <div class="col-lg-6">
            <div class="card-elegant h-100 rounded-4 overflow-hidden" style="background-color: #131d38; border: 1px solid #233054;">
                <div class="card-header py-3.5 px-4 border-bottom border-secondary" style="background-color: #131d38; border-color: #233054 !important;">
                    <strong class="text-white fs-6 d-flex align-items-center gap-2">
                        <i class="bi bi-tags-fill" style="color: #ec4899;"></i>
                        <span>Doanh thu theo danh mục</span>
                    </strong>
                </div>
                <div class="card-body p-4 chart-wrap">
                    <canvas id="categoryRevenueChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Daily Revenue Chart (30 days) --}}
        <div class="col-lg-6">
            <div class="card-elegant h-100 rounded-4 overflow-hidden" style="background-color: #131d38; border: 1px solid #233054;">
                <div class="card-header py-3.5 px-4 border-bottom border-secondary" style="background-color: #131d38; border-color: #233054 !important;">
                    <strong class="text-white fs-6 d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-date-fill" style="color: #3b82f6;"></i>
                        <span>Doanh thu theo ngày (30 ngày gần nhất)</span>
                    </strong>
                </div>
                <div class="card-body p-4 chart-wrap">
                    <canvas id="revenueByDateChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Monthly Revenue Chart (12 months) --}}
        <div class="col-lg-6">
            <div class="card-elegant h-100 rounded-4 overflow-hidden" style="background-color: #131d38; border: 1px solid #233054;">
                <div class="card-header py-3.5 px-4 border-bottom border-secondary" style="background-color: #131d38; border-color: #233054 !important;">
                    <strong class="text-white fs-6 d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-month-fill" style="color: #10b981;"></i>
                        <span>Doanh thu theo tháng (12 tháng gần nhất)</span>
                    </strong>
                </div>
                <div class="card-body p-4 chart-wrap">
                    <canvas id="revenueByMonthChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Yearly Revenue Chart --}}
        <div class="col-lg-6">
            <div class="card-elegant h-100 rounded-4 overflow-hidden" style="background-color: #131d38; border: 1px solid #233054;">
                <div class="card-header py-3.5 px-4 border-bottom border-secondary" style="background-color: #131d38; border-color: #233054 !important;">
                    <strong class="text-white fs-6 d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-range-fill" style="color: #8b5cf6;"></i>
                        <span>Doanh thu theo năm</span>
                    </strong>
                </div>
                <div class="card-body p-4 chart-wrap">
                    <canvas id="revenueByYearChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Payment Method Chart --}}
        <div class="col-lg-12">
            <div class="card-elegant rounded-4 overflow-hidden" style="background-color: #131d38; border: 1px solid #233054;">
                <div class="card-header py-3.5 px-4 border-bottom border-secondary" style="background-color: #131d38; border-color: #233054 !important;">
                    <strong class="text-white fs-6 d-flex align-items-center gap-2">
                        <i class="bi bi-credit-card-2-front-fill" style="color: #f43f5e;"></i>
                        <span>Doanh thu theo phương thức thanh toán</span>
                    </strong>
                </div>
                <div class="card-body p-4 chart-wrap" style="height: 380px;">
                    <canvas id="revenueByPaymentMethodChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Data Container --}}
    <div id="report-chart-data" hidden data-chart-data="{{ json_encode([
        'catLabels' => $catLabels ?? [],
        'catRevenue' => $catRevenue ?? [],
        'revDateLabels' => $revDateLabels ?? [],
        'revDateData' => $revDateData ?? [],
        'revMonthLabels' => $revMonthLabels ?? [],
        'revMonthData' => $revMonthData ?? [],
        'revYearLabels' => $revYearLabels ?? [],
        'revYearData' => $revYearData ?? [],
        'paymentMethodLabels' => $paymentMethodLabels ?? [],
        'paymentMethodRevenue' => $paymentMethodRevenue ?? [],
    ]) }}"></div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (typeof Chart === 'undefined') {
                const errBox = document.getElementById('report-chart-error');
                if (errBox) errBox.classList.remove('d-none');
                return;
            }

            // Dark Theme Chart Defaults
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.borderColor = '#233054';
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";

            const dataElement = document.getElementById('report-chart-data');
            if (!dataElement) return;

            const reportData = JSON.parse(dataElement.dataset.chartData);

            const catLabels = reportData.catLabels;
            const catRevenue = reportData.catRevenue.map(Number);
            const revDateLabels = reportData.revDateLabels;
            const revDateData = reportData.revDateData.map(Number);
            const revMonthLabels = reportData.revMonthLabels;
            const revMonthData = reportData.revMonthData.map(Number);
            const revYearLabels = reportData.revYearLabels;
            const revYearData = reportData.revYearData.map(Number);
            const payLabels = reportData.paymentMethodLabels;
            const payRevenue = reportData.paymentMethodRevenue.map(Number);

            const formatVND = (value) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

            // Chart Helper Generator
            const mk = (el, type, labels, data, label, bgColor, borderColor) => {
                if (!el) return;
                return new Chart(el, {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: data,
                            backgroundColor: bgColor || 'rgba(236, 72, 153, 0.65)',
                            borderColor: borderColor || '#ec4899',
                            borderWidth: 2,
                            fill: type === 'line',
                            tension: 0.35,
                            pointBackgroundColor: '#ec4899',
                            pointRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return (context.dataset.label || '') + ': ' + formatVND(context.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) return (value / 1000000) + ' Tr';
                                        if (value >= 1000) return (value / 1000) + ' k';
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });
            };

            mk(document.getElementById('categoryRevenueChart'), 'bar', catLabels, catRevenue, 'Doanh thu danh mục', 'rgba(236, 72, 153, 0.7)', '#ec4899');
            mk(document.getElementById('revenueByDateChart'), 'line', revDateLabels, revDateData, 'Doanh thu ngày', 'rgba(59, 130, 246, 0.25)', '#3b82f6');
            mk(document.getElementById('revenueByMonthChart'), 'bar', revMonthLabels, revMonthData, 'Doanh thu tháng', 'rgba(16, 185, 129, 0.7)', '#10b981');
            mk(document.getElementById('revenueByYearChart'), 'bar', revYearLabels, revYearData, 'Doanh thu năm', 'rgba(139, 92, 246, 0.7)', '#8b5cf6');

            // Pie Chart for Payment Methods
            const pieEl = document.getElementById('revenueByPaymentMethodChart');
            if (pieEl) {
                new Chart(pieEl, {
                    type: 'pie',
                    data: {
                        labels: payLabels,
                        datasets: [{
                            label: 'Doanh thu',
                            data: payRevenue,
                            backgroundColor: ['rgba(236, 72, 153, 0.8)', 'rgba(59, 130, 246, 0.8)'],
                            borderColor: ['#ec4899', '#3b82f6'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { font: { size: 13, family: "'Plus Jakarta Sans', sans-serif" } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return (context.label || '') + ': ' + formatVND(context.raw);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection

@extends('admin.layouts.app')

@section('title', 'Báo Cáo Doanh Thu | LuxeLips Admin')
@section('header_title', 'Báo Cáo Doanh Thu')

@section('content')
    <style>
        .report-table-scroll {
            max-height: 340px;
            overflow-y: auto;
        }
        .report-table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background-color: #131d38 !important;
            border-bottom: 1px solid #233054 !important;
        }
    </style>

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="page-title mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow" style="color: #ec4899;"></i>
                <span>Báo Cáo & Thống Kê Doanh Thu</span>
            </h1>
            <p class="text-muted mb-0 fs-7" style="color: #94a3b8 !important;">
                Doanh thu tính theo ngày tạo đơn, chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy/hoàn hàng.
            </p>
        </div>
        <div>
            <ul class="nav nav-pills p-1.5 rounded-3 border" style="background-color: #131d38; border-color: #233054 !important;">
                <li class="nav-item">
                    <a class="nav-link active px-3.5 py-2 fw-semibold d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%); color: #ffffff; box-shadow: 0 4px 12px rgba(236,72,153,0.35); border-radius: 8px;" href="{{ route('admin.reports.index', request()->query()) }}">
                        <i class="bi bi-table"></i> <span>Bảng số liệu</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3.5 py-2 fw-semibold d-flex align-items-center gap-2" style="color: #94a3b8; border-radius: 8px; transition: all 0.2s ease;" href="{{ route('admin.reports.charts', request()->query()) }}">
                        <i class="bi bi-pie-chart-fill"></i> <span>Biểu đồ</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Seamless Filter Form --}}
    <div class="mb-4">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-2.5 align-items-end">
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
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary admin-filter-btn" title="Đặt lại bộ lọc">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Summary Cards Row (3 Cards) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #3b82f6;">
                    <i class="bi bi-cart-check-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Tổng số đơn hàng</div>
                    <div class="fw-bold text-light" style="font-size: 1.15rem;">
                        {{ number_format($totalOrders) }} đơn
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #8b5cf6;">
                    <i class="bi bi-people-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Tổng số khách hàng</div>
                    <div class="fw-bold text-light" style="font-size: 1.15rem;">
                        {{ number_format($totalCustomers) }} người
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #10b981;">
                    <i class="bi bi-currency-dollar fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Tổng doanh thu thực nhận</div>
                    <div class="fw-bold text-success" style="font-size: 1.15rem;">
                        {{ number_format($totalRevenue, 0, ',', '.') }} ₫
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Category Revenue Table Card --}}
    <div class="card-elegant mb-4 rounded-4 overflow-hidden" style="background-color: #131d38; border: 1px solid #233054;">
        <div class="card-header py-3.5 px-4 border-bottom border-secondary d-flex align-items-center justify-content-between" style="background-color: #131d38; border-color: #233054 !important;">
            <div>
                <strong class="text-white fs-6 d-flex align-items-center gap-2">
                    <i class="bi bi-tags-fill" style="color: #ec4899;"></i>
                    <span>Doanh thu theo danh mục sản phẩm</span>
                </strong>
                <div class="small text-muted mt-1" style="color: #94a3b8 !important;">Tính theo giá sản phẩm khi đặt hàng, không bao gồm phí vận chuyển.</div>
            </div>
        </div>
        <div class="table-responsive report-table-scroll">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 45%; padding-left: 1.5rem; color: #94a3b8; font-size: 0.85rem; font-weight: 600;" class="py-3">Danh mục</th>
                        <th style="width: 25%; color: #94a3b8; font-size: 0.85rem; font-weight: 600;" class="text-center py-3">Số lượng đã bán</th>
                        <th style="width: 30%; padding-right: 1.5rem; color: #94a3b8; font-size: 0.85rem; font-weight: 600;" class="text-end py-3">Tổng doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryRevenue as $revenue)
                        <tr style="border-bottom: 1px solid #233054;">
                            <td style="width: 45%; padding-left: 1.5rem;" class="fw-medium text-white">
                                <span class="d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-folder-fill" style="color: #ec4899;"></i>
                                    <span>{{ $revenue->category_name ?? ('Danh mục #'.$revenue->category_id) }}</span>
                                </span>
                            </td>
                            <td style="width: 25%;" class="text-center fw-medium text-slate-300">
                                {{ number_format($revenue->total_qty) }} sản phẩm
                            </td>
                            <td style="width: 30%; padding-right: 1.5rem;" class="text-end fw-bold fs-6" style="color: #34d399 !important;">
                                {{ number_format($revenue->total_revenue, 0, ',', '.') }} ₫
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Chưa có dữ liệu doanh thu theo danh mục.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Period Breakdown Tables --}}
    @foreach([
        ['Doanh thu theo ngày', 'Ngày', 'date', $revenueByDate, 'd/m/Y', 'bi-calendar-date-fill', '#3b82f6'],
        ['Doanh thu theo tháng', 'Tháng', 'month', $revenueByMonth, 'm/Y', 'bi-calendar-month-fill', '#10b981'],
        ['Doanh thu theo năm', 'Năm', 'year', $revenueByYear, null, 'bi-calendar-range-fill', '#8b5cf6'],
    ] as [$title, $label, $field, $rows, $format, $icon, $iconColor])
        <div class="card-elegant mb-4 rounded-4 overflow-hidden" style="background-color: #131d38; border: 1px solid #233054;">
            <div class="card-header py-3.5 px-4 border-bottom border-secondary" style="background-color: #131d38; border-color: #233054 !important;">
                <strong class="text-white fs-6 d-flex align-items-center gap-2">
                    <i class="bi {{ $icon }}" style="color: {{ $iconColor }};"></i>
                    <span>{{ $title }}</span>
                </strong>
            </div>
            <div class="table-responsive report-table-scroll">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 45%; padding-left: 1.5rem; color: #94a3b8; font-size: 0.85rem; font-weight: 600;" class="py-3">{{ $label }}</th>
                            <th style="width: 25%; color: #94a3b8; font-size: 0.85rem; font-weight: 600;" class="text-center py-3">Số đơn đã thanh toán</th>
                            <th style="width: 30%; padding-right: 1.5rem; color: #94a3b8; font-size: 0.85rem; font-weight: 600;" class="text-end py-3">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $revenue)
                            <tr style="border-bottom: 1px solid #233054;">
                                <td style="width: 45%; padding-left: 1.5rem;" class="fw-medium text-white">
                                    {{ $format ? \Carbon\Carbon::parse($revenue->{$field}.($field === 'month' ? '-01' : ''))->format($format) : $revenue->{$field} }}
                                </td>
                                <td style="width: 25%;" class="text-center fw-medium text-slate-300">
                                    {{ number_format($revenue->order_count) }} đơn
                                </td>
                                <td style="width: 30%; padding-right: 1.5rem;" class="text-end fw-bold fs-6" style="color: #34d399 !important;">
                                    {{ number_format($revenue->total_revenue, 0, ',', '.') }} ₫
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Chưa có dữ liệu doanh thu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

@endsection

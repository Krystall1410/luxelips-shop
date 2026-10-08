@extends('admin.layouts.app')

@section('title', 'Thống Kê Tài Chính | LuxeLips Admin')
@section('header_title', 'Thống Kê Tài Chính')

@section('content')

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="page-title mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-cash-coin" style="color: #ec4899;"></i>
                <span>Thống Kê Tài Chính</span>
            </h1>
            <p class="text-muted mb-0 fs-7" style="color: #94a3b8 !important;">
                Tổng hợp giá trị thanh toán theo trạng thái và phương thức.
            </p>
        </div>
        <div>
            <ul class="nav nav-pills p-1.5 rounded-3 border" style="background-color: #131d38; border-color: #233054 !important;">
                <li class="nav-item">
                    <a class="nav-link active px-3.5 py-2 fw-semibold d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%); color: #ffffff; box-shadow: 0 4px 12px rgba(236,72,153,0.35); border-radius: 8px;" href="{{ route('admin.finance.index', request()->query()) }}">
                        <i class="bi bi-bar-chart-line-fill"></i> <span>Thống kê chỉ số</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3.5 py-2 fw-semibold d-flex align-items-center gap-2" style="color: #94a3b8; border-radius: 8px; transition: all 0.2s ease;" href="{{ route('admin.finance.transactions', request()->query()) }}">
                        <i class="bi bi-receipt"></i> <span>Giao dịch thanh toán</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Seamless Filter Form --}}
    <div class="card-elegant rounded-3 mb-4" style="background-color: #131d38; border: 1px solid #233054; padding: 1.25rem 1.5rem !important;">
        <form action="{{ route('admin.finance.index') }}" method="GET">
            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-4">
                    <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Tìm đơn hàng</label>
                    <div class="input-group">
                        <span class="input-group-text border-secondary text-muted" style="background-color: #0b1329; height: 38px; border-radius: 8px 0 0 8px;"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control text-light border-secondary admin-filter-input" style="background-color: #0b1329; border-radius: 0 8px 8px 0 !important;" placeholder="Mã đơn, tên hoặc số điện thoại" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Từ ngày tạo đơn</label>
                    <input type="date" name="date_from" class="form-control text-light border-secondary admin-filter-input" style="background-color: #0b1329;" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Đến ngày tạo đơn</label>
                    <input type="date" name="date_to" class="form-control text-light border-secondary admin-filter-input" style="background-color: #0b1329;" value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Số tiền từ (₫)</label>
                    <input type="number" step="any" min="0" name="min_amount" class="form-control text-light border-secondary admin-filter-input" style="background-color: #0b1329;" placeholder="Không giới hạn" value="{{ request('min_amount') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Số tiền đến (₫)</label>
                    <input type="number" step="any" min="0" name="max_amount" class="form-control text-light border-secondary admin-filter-input" style="background-color: #0b1329;" placeholder="Không giới hạn" value="{{ request('max_amount') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Phương thức</label>
                    <select name="gateway" class="form-select text-light border-secondary admin-filter-input" style="background-color: #0b1329;">
                        <option value="">Tất cả</option>
                        @foreach($methods as $key => $name)
                            <option value="{{ $key }}" @selected(request('gateway') === $key)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Trạng thái thanh toán</label>
                    <select name="payment_status" class="form-select text-light border-secondary admin-filter-input" style="background-color: #0b1329;">
                        <option value="">Tất cả</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" @selected(request('payment_status') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3 pt-2 border-top border-secondary" style="border-color: rgba(255,255,255,0.08) !important;">
                <button type="submit" class="btn btn-rose admin-filter-btn px-4" style="background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%); border: none;">
                    <i class="bi bi-funnel-fill me-1.5"></i> Áp dụng bộ lọc
                </button>
                @if(request()->hasAny(['search', 'date_from', 'date_to', 'min_amount', 'max_amount', 'gateway', 'payment_status']))
                    <a href="{{ route('admin.finance.index') }}" class="btn btn-outline-secondary admin-filter-btn" title="Xóa bộ lọc">
                        <i class="bi bi-arrow-counterclockwise me-1.5"></i> Xóa bộ lọc
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Filter Notice --}}
    <div class="d-flex align-items-center gap-2 mb-4 text-slate-300 fs-7" style="color: #cbd5e1;">
        <i class="bi bi-info-circle-fill text-info"></i>
        <span>Có <strong>{{ number_format($summary->order_count ?? 0) }}</strong> đơn phù hợp. Số tiền bao gồm phí vận chuyển, thống kê theo ngày tạo đơn trên toàn bộ kết quả lọc.</span>
    </div>

    {{-- 8 Summary Metric Cards Grid --}}
    <div class="row g-2.5 mb-4">
        <!-- 1. Tổng giá trị đơn hàng -->
        <div class="col-sm-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #3b82f6;">
                    <i class="bi bi-receipt fs-6"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-slate-400 fs-8 fw-medium">Tổng giá trị đơn hàng</div>
                    <div class="fw-bold text-light" style="font-size: 1.15rem; line-height: 1.2;">{{ number_format($summary->total_amount ?? 0, 0, ',', '.') }} ₫</div>
                    <div class="text-slate-400" style="font-size: 0.72rem; line-height: 1.2; margin-top: 2px;">{{ number_format($summary->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 2. Chờ thanh toán -->
        <div class="col-sm-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #f59e0b;">
                    <i class="bi bi-hourglass-split fs-6"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-slate-400 fs-8 fw-medium">Chờ thanh toán</div>
                    <div class="fw-bold text-warning" style="font-size: 1.15rem; line-height: 1.2;">{{ number_format($statusTotals['pending']->total_amount ?? 0, 0, ',', '.') }} ₫</div>
                    <div class="text-slate-400" style="font-size: 0.72rem; line-height: 1.2; margin-top: 2px;">{{ number_format($statusTotals['pending']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 3. Đang chờ MoMo -->
        <div class="col-sm-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #ec4899;">
                    <i class="bi bi-qr-code fs-6"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-slate-400 fs-8 fw-medium">Đang chờ MoMo</div>
                    <div class="fw-bold text-rose" style="font-size: 1.15rem; line-height: 1.2;">{{ number_format($statusTotals['initiated']->total_amount ?? 0, 0, ',', '.') }} ₫</div>
                    <div class="text-slate-400" style="font-size: 0.72rem; line-height: 1.2; margin-top: 2px;">{{ number_format($statusTotals['initiated']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 4. Đã thanh toán -->
        <div class="col-sm-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #10b981;">
                    <i class="bi bi-check-circle-fill fs-6"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-slate-400 fs-8 fw-medium">Đã thanh toán</div>
                    <div class="fw-bold text-success" style="font-size: 1.15rem; line-height: 1.2;">{{ number_format($statusTotals['paid']->total_amount ?? 0, 0, ',', '.') }} ₫</div>
                    <div class="text-slate-400" style="font-size: 0.72rem; line-height: 1.2; margin-top: 2px;">{{ number_format($statusTotals['paid']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 5. Thanh toán thất bại -->
        <div class="col-sm-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #ef4444;">
                    <i class="bi bi-x-circle-fill fs-6"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-slate-400 fs-8 fw-medium">Thanh toán thất bại</div>
                    <div class="fw-bold text-danger" style="font-size: 1.15rem; line-height: 1.2;">{{ number_format($statusTotals['failed']->total_amount ?? 0, 0, ',', '.') }} ₫</div>
                    <div class="text-slate-400" style="font-size: 0.72rem; line-height: 1.2; margin-top: 2px;">{{ number_format($statusTotals['failed']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 6. Đã hủy -->
        <div class="col-sm-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #64748b;">
                    <i class="bi bi-slash-circle-fill fs-6"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-slate-400 fs-8 fw-medium">Đơn đã hủy</div>
                    <div class="fw-bold text-slate-300" style="font-size: 1.15rem; line-height: 1.2;">{{ number_format($statusTotals['cancelled']->total_amount ?? 0, 0, ',', '.') }} ₫</div>
                    <div class="text-slate-400" style="font-size: 0.72rem; line-height: 1.2; margin-top: 2px;">{{ number_format($statusTotals['cancelled']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 7. Chờ hoàn tiền -->
        <div class="col-sm-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #06b6d4;">
                    <i class="bi bi-arrow-counterclockwise fs-6"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-slate-400 fs-8 fw-medium">Chờ hoàn tiền</div>
                    <div class="fw-bold text-info" style="font-size: 1.15rem; line-height: 1.2;">{{ number_format($statusTotals['refund_pending']->total_amount ?? 0, 0, ',', '.') }} ₫</div>
                    <div class="text-slate-400" style="font-size: 0.72rem; line-height: 1.2; margin-top: 2px;">{{ number_format($statusTotals['refund_pending']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 8. Đã hoàn tiền -->
        <div class="col-sm-6 col-lg-3">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #8b5cf6;">
                    <i class="bi bi-cash-coin fs-6"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-slate-400 fs-8 fw-medium">Đã hoàn tiền</div>
                    <div class="fw-bold" style="font-size: 1.15rem; line-height: 1.2; color: #a78bfa;">{{ number_format($statusTotals['refunded']->total_amount ?? 0, 0, ',', '.') }} ₫</div>
                    <div class="text-slate-400" style="font-size: 0.72rem; line-height: 1.2; margin-top: 2px;">{{ number_format($statusTotals['refunded']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Method Breakdown Section --}}
    <div class="card-elegant rounded-4 overflow-hidden mb-4" style="background-color: #131d38; border: 1px solid #233054;">
        <div class="card-header py-3.5 px-4 border-bottom border-secondary" style="background-color: #131d38; border-color: #233054 !important;">
            <strong class="text-white fs-6 d-flex align-items-center gap-2">
                <i class="bi bi-wallet2" style="color: #ec4899;"></i>
                <span>Thống kê theo phương thức</span>
            </strong>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="border-bottom: 1px solid #233054; color: #94a3b8; font-size: 0.85rem; font-weight: 600;">
                        <th class="ps-4 py-3" style="width: 30%;">Phương thức</th>
                        <th class="py-3 text-center" style="width: 20%;">Số đơn</th>
                        <th class="py-3 text-end" style="width: 25%;">Tổng giá trị</th>
                        <th class="pe-4 py-3 text-end" style="width: 25%;">Đã thanh toán</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($methods as $key => $name)
                        @php
                            $row = $methodTotals[$key] ?? null;
                            $count = $row->order_count ?? 0;
                            $total = $row->total_amount ?? 0;
                            $paid = $row->paid_amount ?? 0;
                        @endphp
                        <tr style="border-bottom: 1px solid #233054;">
                            <td class="ps-4 py-3 fw-semibold text-white">
                                @if($key === 'cod')
                                    <span class="badge px-2.5 py-1.5 rounded-2" style="background-color: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                                        <i class="bi bi-truck me-1"></i> COD
                                    </span>
                                @elseif($key === 'momo')
                                    <span class="badge px-2.5 py-1.5 rounded-2" style="background-color: rgba(236, 72, 153, 0.15); color: #f472b6; border: 1px solid rgba(236, 72, 153, 0.3);">
                                        <i class="bi bi-qr-code me-1"></i> MoMo
                                    </span>
                                @else
                                    <span class="badge px-2.5 py-1.5 rounded-2" style="background-color: rgba(148, 163, 184, 0.15); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.3);">
                                        <i class="bi bi-question-circle me-1"></i> Chưa xác định
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-center text-slate-300 fw-medium">
                                {{ number_format($count) }}
                            </td>
                            <td class="py-3 text-end fw-semibold text-white">
                                {{ number_format($total, 0, ',', '.') }} ₫
                            </td>
                            <td class="pe-4 py-3 text-end fw-bold text-success" style="color: #34d399 !important;">
                                {{ number_format($paid, 0, ',', '.') }} ₫
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

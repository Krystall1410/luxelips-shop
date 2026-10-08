@extends('admin.layouts.app')

@section('title', 'Giao Dịch Thanh Toán | LuxeLips Admin')
@section('header_title', 'Giao Dịch Thanh Toán')

@section('content')

<style>
    .badge-status-paid { background-color: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
    .badge-status-pending { background-color: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
    .badge-status-initiated { background-color: rgba(236, 72, 153, 0.15); color: #f472b6; border: 1px solid rgba(236, 72, 153, 0.3); }
    .badge-status-failed { background-color: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
    .badge-status-cancelled { background-color: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); }
    .badge-status-refund_pending { background-color: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); }
    .badge-status-refunded { background-color: rgba(167, 139, 250, 0.15); color: #a78bfa; border: 1px solid rgba(167, 139, 250, 0.3); }
    .badge-status-default { background-color: rgba(148, 163, 184, 0.15); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.3); }
</style>

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="page-title mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-receipt" style="color: #ec4899;"></i>
                <span>Giao Dịch Thanh Toán</span>
            </h1>
            <p class="text-muted mb-0 fs-7" style="color: #94a3b8 !important;">
                Tra cứu thanh toán theo đơn hàng và cập nhật trạng thái COD.
            </p>
        </div>
        <div>
            <ul class="nav nav-pills p-1.5 rounded-3 border" style="background-color: #131d38; border-color: #233054 !important;">
                <li class="nav-item">
                    <a class="nav-link px-3.5 py-2 fw-semibold d-flex align-items-center gap-2" style="color: #94a3b8; border-radius: 8px; transition: all 0.2s ease;" href="{{ route('admin.finance.index', request()->query()) }}">
                        <i class="bi bi-bar-chart-line-fill"></i> <span>Thống kê chỉ số</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active px-3.5 py-2 fw-semibold d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%); color: #ffffff; box-shadow: 0 4px 12px rgba(236,72,153,0.35); border-radius: 8px;" href="{{ route('admin.finance.transactions', request()->query()) }}">
                        <i class="bi bi-receipt"></i> <span>Giao dịch thanh toán</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Alerts --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success rounded-3 mb-4 d-flex align-items-center" role="alert" style="background-color: rgba(34, 197, 94, 0.15); border: none; border-left: 4px solid #22c55e; color: #4ade80;">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            {{ $message }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 mb-4 d-flex align-items-center" role="alert" style="background-color: rgba(225, 29, 72, 0.15); border: none; border-left: 4px solid #f43f5e; color: #fb7185;">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <ul class="mb-0 ps-2">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filter & Search Form --}}
    <div class="card-elegant rounded-3 mb-4" style="background-color: #131d38; border: 1px solid #233054; padding: 1.25rem 1.5rem !important;">
        <form action="{{ route('admin.finance.transactions') }}" method="GET">
            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-4">
                    <label class="form-label text-slate-300 fs-8 fw-semibold mb-1">Tìm đơn hàng</label>
                    <div class="input-group">
                        <span class="input-group-text border-secondary text-muted py-1" style="background-color: #0b1329; height: 38px; border-radius: 8px 0 0 8px;"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control text-light border-secondary admin-filter-input" style="background-color: #0b1329; border-radius: 0 8px 8px 0 !important; font-size: 0.82rem;" placeholder="Mã đơn, tên hoặc số điện thoại" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-slate-300 fs-8 fw-semibold mb-1">Từ ngày tạo đơn</label>
                    <input type="date" name="date_from" class="form-control text-light border-secondary admin-filter-input" style="background-color: #0b1329; font-size: 0.82rem;" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-slate-300 fs-8 fw-semibold mb-1">Đến ngày tạo đơn</label>
                    <input type="date" name="date_to" class="form-control text-light border-secondary admin-filter-input" style="background-color: #0b1329; font-size: 0.82rem;" value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-slate-300 fs-8 fw-semibold mb-1">Số tiền từ (₫)</label>
                    <input type="number" step="any" min="0" name="min_amount" class="form-control text-light border-secondary admin-filter-input" style="background-color: #0b1329; font-size: 0.82rem;" placeholder="Không giới hạn" value="{{ request('min_amount') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-slate-300 fs-8 fw-semibold mb-1">Số tiền đến (₫)</label>
                    <input type="number" step="any" min="0" name="max_amount" class="form-control text-light border-secondary admin-filter-input" style="background-color: #0b1329; font-size: 0.82rem;" placeholder="Không giới hạn" value="{{ request('max_amount') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label text-slate-300 fs-8 fw-semibold mb-1">Phương thức</label>
                    <select name="gateway" class="form-select text-light border-secondary admin-filter-input" style="background-color: #0b1329; font-size: 0.82rem;">
                        <option value="">Tất cả</option>
                        @foreach($methods as $key => $name)
                            <option value="{{ $key }}" @selected(request('gateway') === $key)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-slate-300 fs-8 fw-semibold mb-1">Trạng thái thanh toán</label>
                    <select name="payment_status" class="form-select text-light border-secondary admin-filter-input" style="background-color: #0b1329; font-size: 0.82rem;">
                        <option value="">Tất cả</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" @selected(request('payment_status') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-slate-300 fs-8 fw-semibold mb-1">Sắp xếp</label>
                    <select name="sort" class="form-select text-light border-secondary admin-filter-input" style="background-color: #0b1329; font-size: 0.82rem;">
                        <option value="newest" @selected(request('sort') === 'newest')>Mới nhất</option>
                        <option value="oldest" @selected(request('sort') === 'oldest')>Cũ nhất</option>
                        <option value="amount_asc" @selected(request('sort') === 'amount_asc')>Số tiền tăng dần</option>
                        <option value="amount_desc" @selected(request('sort') === 'amount_desc')>Số tiền giảm dần</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3 pt-3 border-top border-secondary" style="border-color: rgba(255,255,255,0.08) !important;">
                <button type="submit" class="btn btn-rose admin-filter-btn px-4" style="background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%); border: none; font-size: 0.82rem;">
                    <i class="bi bi-funnel-fill me-1.5"></i> Áp dụng bộ lọc
                </button>
                @if(request()->hasAny(['search', 'date_from', 'date_to', 'min_amount', 'max_amount', 'gateway', 'payment_status', 'sort']))
                    <a href="{{ route('admin.finance.transactions') }}" class="btn btn-outline-secondary admin-filter-btn" style="font-size: 0.82rem;" title="Xóa bộ lọc">
                        <i class="bi bi-arrow-counterclockwise me-1.5"></i> Xóa bộ lọc
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Filter Notice --}}
    <div class="d-flex align-items-center gap-2 mb-3 text-slate-300 fs-8" style="color: #cbd5e1; font-size: 0.8rem;">
        <i class="bi bi-info-circle-fill text-info"></i>
        <span>Có <strong>{{ number_format($orders->total()) }}</strong> đơn phù hợp bộ lọc. Số tiền bao gồm phí vận chuyển; ngày lọc là ngày tạo đơn.</span>
    </div>

    {{-- Table Card --}}
    <div class="card-elegant rounded-3 overflow-hidden mb-4" style="background-color: #131d38; border: 1px solid #233054;">
        <div class="card-header py-3 px-4 border-bottom border-secondary d-flex flex-column" style="background-color: #131d38; border-color: #233054 !important;">
            <strong class="text-white fs-7 d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                <i class="bi bi-table" style="color: #ec4899;"></i>
                <span>Danh sách giao dịch ({{ number_format($orders->total()) }} đơn)</span>
            </strong>
            <div class="small text-muted mt-1" style="color: #94a3b8 !important; font-size: 0.78rem;">
                COD: xác nhận thu tiền hoặc thất bại; đơn đã thu tiền có thể chuyển sang chờ hoàn tiền rồi xác nhận đã hoàn tiền.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="border-bottom: 1px solid #233054; color: #94a3b8; font-size: 0.8rem; font-weight: 600;">
                        <th class="ps-4 py-2.5" style="min-width: 130px;">Đơn hàng</th>
                        <th class="py-2.5" style="min-width: 150px;">Khách hàng</th>
                        <th class="py-2.5" style="min-width: 110px;">Phương thức</th>
                        <th class="py-2.5 text-end" style="min-width: 120px;">Số tiền</th>
                        <th class="py-2.5" style="min-width: 140px;">Thanh toán</th>
                        <th class="pe-4 py-2.5 text-end" style="min-width: 190px;">Cập nhật COD</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr style="border-bottom: 1px solid #233054;">
                            <!-- Đơn hàng -->
                            <td class="ps-4 py-2.5">
                                <div class="fw-bold text-info" style="font-size: 0.82rem;">#{{ $order->order_code ?? $order->id }}</div>
                                <div class="text-slate-400" style="font-size: 0.74rem;">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</div>
                            </td>

                            <!-- Khách hàng -->
                            <td class="py-2.5">
                                <div class="fw-semibold text-white" style="font-size: 0.82rem;">{{ $order->name }}</div>
                                <div class="text-slate-400" style="font-size: 0.74rem;">{{ $order->phone ?? 'Chưa cập nhật' }}</div>
                            </td>

                            <!-- Phương thức -->
                            <td class="py-2.5">
                                @if($order->gateway === 'cod')
                                    <span class="badge rounded-2" style="background-color: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); font-size: 0.75rem; padding: 3px 8px;">
                                        <i class="bi bi-truck me-1"></i> COD
                                    </span>
                                @elseif($order->gateway === 'momo')
                                    <span class="badge rounded-2" style="background-color: rgba(236, 72, 153, 0.15); color: #f472b6; border: 1px solid rgba(236, 72, 153, 0.3); font-size: 0.75rem; padding: 3px 8px;">
                                        <i class="bi bi-qr-code me-1"></i> MoMo
                                    </span>
                                @else
                                    <span class="badge rounded-2" style="background-color: rgba(148, 163, 184, 0.15); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.3); font-size: 0.75rem; padding: 3px 8px;">
                                        <i class="bi bi-question-circle me-1"></i> Chưa xác định
                                    </span>
                                @endif
                            </td>

                            <!-- Số tiền -->
                            <td class="py-2.5 text-end">
                                <span class="text-white fw-bold" style="font-size: 0.85rem;">{{ number_format($order->total_price, 0, ',', '.') }} ₫</span>
                            </td>

                            <!-- Thanh toán -->
                            <td class="py-2.5">
                                @php
                                    $st = $order->payment_status;
                                    $badgeClass = match($st) {
                                        'paid' => 'badge-status-paid',
                                        'pending' => 'badge-status-pending',
                                        'initiated' => 'badge-status-initiated',
                                        'failed' => 'badge-status-failed',
                                        'cancelled' => 'badge-status-cancelled',
                                        'refund_pending' => 'badge-status-refund_pending',
                                        'refunded' => 'badge-status-refunded',
                                        default => 'badge-status-default',
                                    };
                                @endphp
                                <span class="badge rounded-2 {{ $badgeClass }}" style="font-size: 0.75rem; padding: 3px 8px;">
                                    {{ $statuses[$st] ?? $st }}
                                </span>
                                @if($order->paid_at)
                                    <div class="text-slate-400 mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($order->paid_at)->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Cập nhật COD -->
                            <td class="pe-4 py-2.5 text-end">
                                @php
                                    $isCod = ($order->gateway === 'cod');
                                    $currentSt = $order->payment_status;
                                    $allowedNext = $codTransitions[$currentSt] ?? [];
                                    $canChange = $isCod && count($allowedNext) > 1 && !in_array($order->status, ['cancelled'], true);
                                @endphp

                                @if($canChange)
                                    <form action="{{ route('admin.finance.update-status', $order->id) }}" method="POST" class="d-inline-flex align-items-center justify-content-end gap-1.5" onsubmit="return confirm('Xác nhận cập nhật trạng thái thanh toán đơn #{{ $order->id }}?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="current_payment_status" value="{{ $currentSt }}">
                                        <input type="hidden" name="current_order_status" value="{{ $order->status }}">
                                        <input type="hidden" name="current_payment_id" value="{{ $order->payment_id ?? 0 }}">

                                        <select name="payment_status" class="form-select form-select-sm text-light border-secondary" style="background-color: #0b1329; font-size: 0.75rem; width: auto; height: 30px !important; padding: 0.15rem 0.5rem; border-radius: 6px;">
                                            @foreach($allowedNext as $target)
                                                <option value="{{ $target }}" @selected($target === $currentSt)>
                                                    {{ $statuses[$target] ?? $target }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <button type="submit" class="btn btn-sm btn-outline-light px-2.5 py-1 text-nowrap d-inline-flex align-items-center justify-content-center" style="height: 30px; font-size: 0.75rem; border-color: rgba(255,255,255,0.25); border-radius: 6px;">
                                            Lưu
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-500" style="color: #64748b; font-size: 0.75rem;">
                                        @if(!$isCod)
                                            <i class="bi bi-shield-check me-1"></i>Tự động ({{ strtoupper($order->gateway) }})
                                        @else
                                            <i class="bi bi-lock me-1"></i>Đã đóng
                                        @endif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt mb-3 d-block" style="font-size: 2.5rem; color: #475569;"></i>
                                <span style="font-size: 0.85rem;">Không có đơn hàng phù hợp với bộ lọc.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="card-footer bg-transparent border-top border-secondary p-3 d-flex justify-content-between align-items-center" style="border-color: #233054 !important;">
                <div class="text-muted" style="font-size: 0.78rem;">
                    Hiển thị {{ $orders->firstItem() }} đến {{ $orders->lastItem() }} trên {{ $orders->total() }} đơn
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>

@endsection

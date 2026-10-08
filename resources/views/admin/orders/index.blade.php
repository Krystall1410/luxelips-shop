@extends('admin.layouts.app')

@section('title', 'Quản Lý Đơn Hàng | LuxeLips Admin')
@section('header_title', 'Quản Lý Đơn Hàng')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">
            <i class="bi bi-cart-check-fill"></i>
            Danh Sách Đơn Hàng
        </h1>
        <span class="badge rounded-pill px-3 py-2 fs-7" style="background-color: var(--primary-color) !important;">
            {{ $totalOrdersCount }} Đơn hàng tổng cộng
        </span>
    </div>

    {{-- Success Alert --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success rounded-3 mb-4 d-flex align-items-center" id="alert-success" role="alert" style="background-color: rgba(34, 197, 94, 0.15); border: none; border-left: 4px solid #22c55e; color: #4ade80;">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            {{ $message }}
        </div>
    @endif

    {{-- Summary Cards Row (5 Cards) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-2-4" style="flex: 0 0 auto; width: 20%;">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #ec4899;">
                    <i class="bi bi-bag-check-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Tổng số đơn</div>
                    <div class="fw-bold text-light" style="font-size: 1.15rem;">{{ $totalOrdersCount }} đơn</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-2-4" style="flex: 0 0 auto; width: 20%;">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #10b981;">
                    <i class="bi bi-patch-check-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Đã TT MoMo</div>
                    <div class="fw-bold text-success" style="font-size: 1.15rem;">{{ $paidOrdersCount }} đơn</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-2-4" style="flex: 0 0 auto; width: 20%;">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #06b6d4;">
                    <i class="bi bi-truck fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Đã tạo COD</div>
                    <div class="fw-bold text-info" style="font-size: 1.15rem;">{{ $codOrdersCount }} đơn</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-2-4" style="flex: 0 0 auto; width: 20%;">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #ef4444;">
                    <i class="bi bi-x-circle-fill fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Đã bị hủy</div>
                    <div class="fw-bold text-danger" style="font-size: 1.15rem;">{{ $cancelledOrdersCount }} đơn</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-2-4" style="flex: 0 0 auto; width: 20%;">
            <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
                <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #f59e0b;">
                    <i class="bi bi-currency-dollar fs-6"></i>
                </div>
                <div>
                    <div class="text-slate-400 fs-8 fw-medium">Tổng doanh thu</div>
                    <div class="fw-bold text-warning" style="font-size: 1.15rem;">{{ number_format($totalRevenue, 0, ',', '.') }}đ</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Form --}}
    <div class="card-elegant rounded-3 mb-4" style="background-color: #131d38; border: 1px solid #233054; padding: 1.25rem 1.5rem !important;">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-slate-300 fs-8 fw-semibold mb-1">Tìm đơn hàng</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-slate-400" style="background-color: #0b1329 !important; height: 38px; border-radius: 8px 0 0 8px;"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control admin-filter-input" style="background-color: #0b1329 !important; border-radius: 0 8px 8px 0 !important; font-size: 0.82rem;" placeholder="Tìm theo Mã Đơn, Mã GHN, Tên, SĐT..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-slate-300 fs-8 fw-semibold mb-1">Trạng thái đơn hàng</label>
                <select name="status" class="form-select admin-filter-input" style="background-color: #0b1329 !important; font-size: 0.82rem;" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ thanh toán (Pending)</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Đã thanh toán (Paid)</option>
                    <option value="cod_ordered" {{ request('status') === 'cod_ordered' ? 'selected' : '' }}>Đã tạo đơn COD</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Thất bại (Failed)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy (Cancelled)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-slate-300 fs-8 fw-semibold mb-1">Phương thức thanh toán</label>
                <select name="payment_method" class="form-select admin-filter-input" style="background-color: #0b1329 !important; font-size: 0.82rem;" onchange="this.form.submit()">
                    <option value="">-- Phương thức thanh toán --</option>
                    <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>COD (Tiền mặt)</option>
                    <option value="momo" {{ request('payment_method') === 'momo' ? 'selected' : '' }}>Ví MoMo</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-rose admin-filter-btn flex-grow-1" style="font-size: 0.82rem;"><i class="bi bi-funnel-fill me-1"></i>Lọc</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary admin-filter-btn" style="font-size: 0.82rem;" title="Đặt lại bộ lọc"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>

    {{-- Orders Table Card --}}
    <div class="card-elegant">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-table me-2"></i>
                Danh sách đơn hàng toàn hệ thống
            </div>
        </div>

        @if($orders->isEmpty())
            <div class="card-body p-5 text-center text-muted">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #475569;"></i>
                <p class="mt-3 mb-0">Không tìm thấy đơn hàng nào phù hợp.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="padding-left: 1.5rem;">Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Số Tiền</th>
                            <th>Thanh Toán</th>
                            <th>Trạng Thái Giao Hàng</th>
                            <th>Vận Chuyển GHN</th>
                            <th>Thời Gian</th>
                            <th class="text-end pe-3">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding-left: 1.5rem;">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-rose fs-6 text-decoration-none d-block" style="color: #ec4899 !important;" title="Xem chi tiết đơn hàng {{ $order->order_code }}">
                                        {{ $order->order_code }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-bold text-light">{{ $order->name }}</div>
                                    <span class="text-slate-400 fs-8">{{ $order->phone }}</span>
                                </td>
                                <td>
                                    <strong class="text-warning fs-6">{{ number_format($order->total_price, 0, ',', '.') }}đ</strong>
                                    <div class="text-slate-400 fs-9">{{ $order->items->count() }} món sản phẩm</div>
                                </td>
                                <td>
                                    @if($order->status === 'paid')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Đã TT MoMo</span>
                                    @elseif($order->status === 'cod_ordered')
                                        <span class="badge bg-info text-dark"><i class="bi bi-cash-stack me-1"></i>Đã tạo COD</span>
                                    @elseif($order->status === 'failed')
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Thất bại</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Đã Hủy</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Chờ thanh toán</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->status === 'cancelled' || $order->shipping_status === 'cancelled')
                                        <span class="badge bg-danger text-white"><i class="bi bi-x-octagon me-1"></i>Đã Hủy Giao</span>
                                    @elseif($order->shipping_status === 'delivered')
                                        <span class="badge bg-success"><i class="bi bi-check-all me-1"></i>Đã Giao Hàng</span>
                                    @elseif($order->shipping_status === 'delivering')
                                        <span class="badge bg-info text-dark"><i class="bi bi-truck me-1"></i>Đang Giao Hàng</span>
                                    @elseif($order->shipping_status === 'ready_to_pick')
                                        <span class="badge bg-primary"><i class="bi bi-box-seam me-1"></i>Sẵn Sàng Lấy Hàng</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-clock me-1"></i>Chờ Xử Lý</span>
                                    @endif
                                </td>
                                <td class="py-2.5">
                                    @if($order->ghn_order_code)
                                        <span class="badge rounded-2 px-2 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="background-color: #131d38; border: 1px solid rgba(236, 72, 153, 0.5); color: #ffffff !important; font-size: 0.75rem;">
                                            <i class="bi bi-truck" style="color: #ec4899; font-size: 0.8rem;"></i> <span class="text-white fw-bold">{{ $order->ghn_order_code }}</span>
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size: 0.75rem;">Chưa có mã GHN</span>
                                    @endif
                                </td>
                                <td class="py-2.5">
                                    <span class="text-slate-400" style="font-size: 0.74rem;">{{ $order->created_at->format('H:i - d/m/Y') }}</span>
                                </td>
                                <td class="text-end pe-4 py-2.5">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-light border-secondary" style="font-size: 0.75rem;" title="Xem chi tiết đơn hàng">
                                        <i class="bi bi-eye me-1"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 d-flex justify-content-center">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

@endsection

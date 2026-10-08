@extends('user.layouts.app')

@section('title', 'Chi Tiết Đơn Hàng ' . $order->order_code . ' | LuxeLips PRO')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/pages/orders.css') }}">
@endpush

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 brand-font">Chi Tiết Đơn Hàng <span class="text-pink" style="color: #ec4899 !important;">{{ $order->order_code }}</span></h1>
            <p class="fs-7 mb-0 text-muted">Thời gian khởi tạo: {{ $order->created_at->format('H:i:s - d/m/Y') }}</p>
        </div>
        
        <div class="d-flex gap-2">
            @if($order->payment_method === 'momo' && $order->status !== 'paid' && $order->status !== 'cancelled')
                <a href="{{ route('user.orders.momo.pay', $order->id) }}" class="btn btn-momo-pay text-decoration-none">
                    <i class="bi bi-qr-code-scan me-1"></i> Thanh Toán Lại MoMo
                </a>
            @endif

            @if($order->status !== 'cancelled' && !in_array($order->shipping_status, ['delivering', 'delivered']))
                <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger px-4 py-2 rounded-3">
                        <i class="bi bi-x-circle me-1"></i> Hủy Đơn Hàng
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Order Products & Transactions -->
        <div class="col-lg-8">
            <div class="order-card p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3 brand-font border-bottom border-secondary pb-3">Sản Phẩm Trong Đơn</h5>
                <div class="d-flex flex-column gap-3">
                    @foreach($order->items as $item)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-secondary" style="border-color: rgba(236,72,153,0.15) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 overflow-hidden bg-light flex-shrink-0" style="width: 54px; height: 54px;">
                                @if($item->product && $item->product->first_image_url)
                                    <img src="{{ $item->product->first_image_url }}" alt="{{ $item->product->name }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 fs-6">{{ $item->product ? $item->product->name : 'Sản phẩm son môi' }}</h6>
                                <span class="fs-8 text-muted" style="font-size: 0.82rem;">Đơn giá: {{ number_format($item->price, 0, ',', '.') }}đ | Số lượng: {{ $item->quantity }}</span>
                            </div>
                        </div>
                        <strong class="fs-6" style="color: #ec4899;">
                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                        </strong>
                    </div>
                    @endforeach
                </div>

                <div class="pt-3">
                    <div class="d-flex justify-content-between mb-2 fs-7 text-muted">
                        <span>Tạm tính tiền hàng:</span>
                        <strong class="text-dark">{{ number_format($order->subtotal, 0, ',', '.') }}đ</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 fs-7 text-muted">
                        <span>Phí vận chuyển (GHN):</span>
                        <strong class="text-pink" style="color: #ec4899;">{{ number_format($order->ghn_total_fee, 0, ',', '.') }}đ</strong>
                    </div>
                    <hr class="border-secondary my-3">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold text-dark fs-6">Tổng thanh toán:</span>
                        <strong class="fs-4 text-warning" style="color: #f59e0b !important;">{{ number_format($order->total_price, 0, ',', '.') }}đ</strong>
                    </div>
                </div>
            </div>

            <!-- Payment Transactions Log -->
            <div class="order-card p-4">
                <h5 class="fw-bold text-dark mb-3 brand-font border-bottom border-secondary pb-3">Nhật Ký Thanh Toán (Payment Transactions)</h5>
                @if($order->transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 fs-8" style="background-color: #ffffff !important; color: #1e293b !important;">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(236,72,153,0.2); background-color: #f8fafc !important;">
                                    <th class="py-2.5 text-muted">Cổng Thanh Toán</th>
                                    <th class="py-2.5 text-muted">Mã Đơn MoMo</th>
                                    <th class="py-2.5 text-muted">Số Tiền</th>
                                    <th class="py-2.5 text-muted">Trạng Thái</th>
                                    <th class="py-2.5 text-muted">Thời Gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->transactions as $trans)
                                <tr style="border-bottom: 1px solid rgba(236,72,153,0.12); background-color: #ffffff !important;">
                                    <td class="py-2.5"><span class="badge rounded-pill px-2.5 py-1" style="background-color: #fdf2f8; color: #db2777; border: 1px solid rgba(236,72,153,0.3);">{{ strtoupper($trans->gateway) }}</span></td>
                                    <td class="py-2.5"><code style="color: #0f172a !important; background-color: #f8fafc; padding: 3px 8px; border-radius: 6px; border: 1px solid rgba(236,72,153,0.25); font-weight: 600;">{{ $trans->gateway_order_id ?: '-' }}</code></td>
                                    <td class="py-2.5"><strong class="text-warning" style="color: #f59e0b !important;">{{ number_format($trans->amount, 0, ',', '.') }}đ</strong></td>
                                    <td class="py-2.5">
                                        @if($trans->status === 'paid')
                                            <span class="badge bg-success">Đã hoàn tất</span>
                                        @elseif($trans->status === 'failed')
                                            <span class="badge bg-danger">Thất bại</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 text-muted">{{ $trans->created_at->format('H:i - d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-muted fs-7">Chưa có bản ghi giao dịch nào.</div>
                @endif
            </div>

        </div>

        <!-- Delivery & Order Info -->
        <div class="col-lg-4">
            <div class="order-card p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3 brand-font border-bottom border-secondary pb-3">Thông Tin Giao Hàng</h5>
                
                <div class="mb-3">
                    <span class="fs-8 d-block text-muted">Người nhận:</span>
                    <strong class="text-dark fs-6">{{ $order->name }}</strong>
                </div>

                <div class="mb-3">
                    <span class="fs-8 d-block text-muted">Số điện thoại:</span>
                    <strong class="text-dark fs-6">{{ $order->phone }}</strong>
                </div>

                <div class="mb-3">
                    <span class="fs-8 d-block text-muted">Địa chỉ nhận hàng:</span>
                    <div class="fs-7 text-dark">{{ $order->address }}</div>
                </div>

                <hr class="border-secondary my-3">

                <div class="mb-3">
                    <span class="fs-8 d-block mb-1 text-muted">Trạng thái thanh toán:</span>
                    @if($order->status === 'paid')
                        <span class="badge bg-success fs-8 px-3 py-2 rounded-pill">
                            <i class="bi bi-check-all me-1"></i> Đã Thanh Toán (MoMo)
                        </span>
                    @elseif($order->status === 'cod_ordered')
                        <span class="badge bg-info text-dark fs-8 px-3 py-2 rounded-pill">
                            <i class="bi bi-cash-stack me-1"></i> Đặt Hàng COD
                        </span>
                    @elseif($order->status === 'failed')
                        <span class="badge bg-danger fs-8 px-3 py-2 rounded-pill">
                            <i class="bi bi-exclamation-triangle me-1"></i> Thanh Toán Thất Bại
                        </span>
                    @elseif($order->status === 'cancelled')
                        <span class="badge bg-danger fs-8 px-3 py-2 rounded-pill">
                            <i class="bi bi-x-octagon me-1"></i> Đã Hủy Đơn Hàng
                        </span>
                    @else
                        <span class="badge bg-warning text-dark fs-8 px-3 py-2 rounded-pill">
                            <i class="bi bi-hourglass-split me-1"></i> Chờ Thanh Toán
                        </span>
                    @endif
                </div>

                <div class="mb-3">
                    <span class="fs-8 d-block mb-1 text-muted">Trạng thái giao hàng:</span>
                    @if($order->status === 'cancelled' || $order->shipping_status === 'cancelled')
                        <span class="badge bg-danger text-white fs-8 px-3 py-2 rounded-pill">
                            <i class="bi bi-x-circle me-1"></i> Đã Hủy Giao Hàng
                        </span>
                    @elseif($order->shipping_status === 'delivered')
                        <span class="badge bg-success fs-8 px-3 py-2 rounded-pill">
                            <i class="bi bi-check-all me-1"></i> Đã Giao Hàng Thành Công
                        </span>
                    @elseif($order->shipping_status === 'delivering')
                        <span class="badge bg-info text-dark fs-8 px-3 py-2 rounded-pill">
                            <i class="bi bi-truck me-1"></i> Đang Giao Hàng
                        </span>
                    @elseif($order->shipping_status === 'ready_to_pick')
                        <span class="badge bg-primary fs-8 px-3 py-2 rounded-pill">
                            <i class="bi bi-box-seam me-1"></i> Sẵn Sàng Lấy Hàng
                        </span>
                    @else
                        <span class="badge bg-secondary fs-8 px-3 py-2 rounded-pill">
                            <i class="bi bi-clock me-1"></i> Chờ Xử Lý
                        </span>
                    @endif
                </div>

                <div class="mb-3">
                    <span class="fs-8 d-block mb-1 text-muted">Phương thức thanh toán:</span>
                    @if($order->payment_method === 'momo')
                        <span class="badge bg-danger fs-8 px-3 py-2 rounded-pill" style="background-color: #a50064 !important;">
                            <i class="bi bi-qr-code-scan me-1"></i> Ví MoMo (Tự động)
                        </span>
                    @else
                        <span class="badge bg-success fs-8 px-3 py-2 rounded-pill">
                            <i class="bi bi-cash-stack me-1"></i> COD (Tiền mặt)
                        </span>
                    @endif
                </div>

                <div>
                    <span class="fs-8 d-block mb-1 text-muted">Mã đơn hàng:</span>
                    <span class="badge rounded-pill px-2.5 py-1.5 fs-8 fw-semibold d-inline-flex align-items-center gap-1.5" style="background-color: #ffffff; border: 1px solid rgba(236, 72, 153, 0.4); color: #0f172a !important; font-size: 0.85rem;">
                        <i class="bi bi-bag-check" style="color: #ec4899;"></i> <span class="text-dark fw-bold">{{ $order->order_code }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

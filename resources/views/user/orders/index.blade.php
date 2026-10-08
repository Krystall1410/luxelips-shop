@extends('user.layouts.app')

@section('title', 'Lịch Sử Đơn Hàng | LuxeLips PRO')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/pages/orders.css') }}">
@endpush

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 brand-font">Lịch Sử Đơn Hàng</h1>
            <p class="fs-7 mb-0 text-muted">Theo dõi trạng thái đơn hàng và thông tin vận chuyển GHN</p>
        </div>
        <span class="badge bg-danger fs-7 px-3 py-2 rounded-pill">
            {{ $orders->total() }} Đơn hàng
        </span>
    </div>

    @if($orders->count() > 0)
        <div class="d-flex flex-column gap-4">
            @foreach($orders as $order)
                <div class="order-card p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-3 border-bottom border-secondary" style="border-color: rgba(236,72,153,0.15) !important;">
                        <div>
                            <span class="fs-8 me-2 text-muted">Mã đơn hàng:</span>
                            <strong class="fs-6 text-pink" style="color: #ec4899 !important;">{{ $order->order_code }}</strong>
                            <span class="fs-8 ms-3 text-muted"><i class="bi bi-clock me-1"></i>{{ $order->created_at->format('H:i - d/m/Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            {{-- Payment Status Badge --}}
                            @if($order->status === 'paid')
                                <span class="badge bg-success fs-8 px-2.5 py-1.5 rounded-pill"><i class="bi bi-check-circle me-1"></i>Đã Thanh Toán</span>
                            @elseif($order->status === 'cod_ordered')
                                <span class="badge bg-info text-dark fs-8 px-2.5 py-1.5 rounded-pill"><i class="bi bi-cash-stack me-1"></i>Đặt Hàng COD</span>
                            @elseif($order->status === 'cancelled')
                                <span class="badge bg-danger fs-8 px-2.5 py-1.5 rounded-pill"><i class="bi bi-x-octagon me-1"></i>Đã Hủy</span>
                            @else
                                <span class="badge bg-warning text-dark fs-8 px-2.5 py-1.5 rounded-pill"><i class="bi bi-hourglass-split me-1"></i>Chờ Thanh Toán</span>
                            @endif

                            {{-- Shipping Status Badge --}}
                            @if($order->status === 'cancelled' || $order->shipping_status === 'cancelled')
                                <span class="badge bg-danger text-white fs-8 px-2.5 py-1.5 rounded-pill"><i class="bi bi-x-circle me-1"></i>Đã Hủy Giao</span>
                            @elseif($order->shipping_status === 'delivered')
                                <span class="badge bg-success fs-8 px-2.5 py-1.5 rounded-pill"><i class="bi bi-check-all me-1"></i>Đã Giao Hàng</span>
                            @elseif($order->shipping_status === 'delivering')
                                <span class="badge bg-info text-dark fs-8 px-2.5 py-1.5 rounded-pill"><i class="bi bi-truck me-1"></i>Đang Giao Hàng</span>
                            @elseif($order->shipping_status === 'ready_to_pick')
                                <span class="badge bg-primary fs-8 px-2.5 py-1.5 rounded-pill"><i class="bi bi-box-seam me-1"></i>Sẵn Sàng Lấy Hàng</span>
                            @else
                                <span class="badge bg-secondary fs-8 px-2.5 py-1.5 rounded-pill"><i class="bi bi-clock me-1"></i>Chờ Xử Lý</span>
                            @endif
                        </div>
                    </div>

                    {{-- Order Items Preview --}}
                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach($order->items as $item)
                            <div class="d-flex align-items-center justify-content-between py-1">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 overflow-hidden bg-light flex-shrink-0" style="width: 44px; height: 44px;">
                                        @if($item->product && $item->product->first_image_url)
                                            <img src="{{ $item->product->first_image_url }}" alt="{{ $item->product->name }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold text-dark mb-0 fs-6">{{ $item->product ? $item->product->name : 'Sản phẩm son môi' }}</h6>
                                        <span class="fs-8 text-muted">x{{ $item->quantity }} | {{ number_format($item->price, 0, ',', '.') }}đ</span>
                                    </div>
                                </div>
                                <strong class="fs-7 text-dark">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</strong>
                            </div>
                        @endforeach
                    </div>

                    {{-- Footer Actions --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 border-top border-secondary" style="border-color: rgba(236,72,153,0.15) !important;">
                        <div>
                            <span class="fs-7 me-1 text-dark fw-semibold">Tổng thanh toán:</span>
                            <strong class="fs-5 text-warning" style="color: #f59e0b !important;">{{ number_format($order->total_price, 0, ',', '.') }}đ</strong>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($order->payment_method === 'momo' && $order->status !== 'paid' && $order->status !== 'cancelled')
                                <a href="{{ route('user.orders.momo.pay', $order->id) }}" class="btn btn-momo-pay btn-sm text-decoration-none">
                                    <i class="bi bi-qr-code-scan me-1"></i> Thanh Toán MoMo
                                </a>
                            @endif

                            @if($order->status !== 'cancelled' && !in_array($order->shipping_status, ['delivering', 'delivered']))
                                <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                        <i class="bi bi-x-circle me-1"></i> Hủy Đơn
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('user.orders.show', $order->id) }}" class="btn btn-detail btn-sm text-decoration-none rounded-pill px-3">
                                Chi Tiết Đơn Hàng <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    @else
        <!-- Empty Orders State -->
        <div class="order-card p-5 text-center my-4">
            <i class="bi bi-bag-x" style="font-size: 4.5rem; color: #ec4899; filter: drop-shadow(0 0 15px rgba(236,72,153,0.4));"></i>
            <h4 class="fw-bold text-dark mt-3 mb-2 brand-font">Bạn chưa có đơn hàng nào!</h4>
            <p class="mb-4 text-muted">Khám phá các bộ sưu tập son môi và đặt mua ngay hôm nay.</p>
            <a href="{{ route('home') }}" class="btn nav-header-btn rounded-pill px-4 py-2 text-decoration-none d-inline-block">
                <i class="bi bi-arrow-left me-2"></i> Khám Phá Bộ Sưu Tập
            </a>
        </div>
    @endif
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Chi Tiết Đơn Hàng | LuxeLips Admin')
@section('header_title', 'Chi Tiết Đơn Hàng')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">
        <i class="bi bi-receipt me-2"></i>
        Chi Tiết Đơn Hàng <span class="text-rose" style="color: #ec4899 !important;">{{ $order->order_code }}</span>
    </h1>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-rose">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
</div>


{{-- Success Alert --}}
@if ($message = Session::get('success'))
<div class="alert alert-success rounded-3 mb-4 d-flex align-items-center" role="alert" style="background-color: rgba(34, 197, 94, 0.15); border: none; border-left: 4px solid #22c55e; color: #4ade80;">
    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
    {{ $message }}
</div>
@endif

<div class="row g-4">
    {{-- Left Side: Order Items & Status Update --}}
    <div class="col-lg-8">
        <div class="card-elegant p-4 mb-4">
            <h5 class="fw-bold text-white mb-3 border-bottom border-secondary pb-3">Sản Phẩm Đã Đặt</h5>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-center">Đơn giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 overflow-hidden bg-dark flex-shrink-0" style="width: 48px; height: 48px;">
                                        @if($item->product && $item->product->first_image_url)
                                        <img src="{{ $item->product->first_image_url }}" alt="{{ $item->product->name }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-white mb-0 fs-6">{{ $item->product ? $item->product->name : 'Sản phẩm son môi' }}</h6>
                                        <span class="text-slate-400 fs-8">Sản phẩm LuxeLips</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center text-slate-300">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                            <td class="text-center fw-bold text-white">{{ $item->quantity }}</td>
                            <td class="text-end text-rose fw-bold" style="color: #ec4899;">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-3 border-top border-secondary mt-3">
                <div class="d-flex justify-content-between mb-2 text-slate-300 fs-7">
                    <span>Tạm tính tiền hàng:</span>
                    <strong class="text-white">{{ number_format($order->subtotal, 0, ',', '.') }}đ</strong>
                </div>
                <div class="d-flex justify-content-between mb-2 text-slate-300 fs-7">
                    <span>Phí giao hàng GHN:</span>
                    <strong class="text-pink" style="color: #ec4899;">{{ number_format($order->ghn_total_fee, 0, ',', '.') }}đ</strong>
                </div>
                <hr class="border-secondary my-3">
                <div class="d-flex justify-content-between">
                    <span class="fw-bold text-white fs-6">Tổng tiền thanh toán:</span>
                    <strong class="fs-4 text-warning" style="color: #f59e0b !important;">{{ number_format($order->total_price, 0, ',', '.') }}đ</strong>
                </div>
            </div>
        </div>

        {{-- Status Update Form --}}
        <div class="card-elegant p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-3">
                <h5 class="fw-bold text-white mb-0">Cập Nhật Trạng Thái Đơn Hàng</h5>
                @if($order->ghn_order_code)
                <form action="{{ route('admin.orders.sync-ghn', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-info rounded-pill px-3" title="Tự động gọi API GHN để đồng bộ trạng thái vận đơn thực tế mới nhất">
                        <i class="bi bi-arrow-repeat me-1"></i> ⚡ Đồng Bộ Từ GHN API
                    </button>
                </form>
                @endif
            </div>
            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-slate-300">Trạng Thái Thanh Toán </label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ thanh toán (Pending)</option>
                            <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Đã thanh toán (Paid)</option>
                            <option value="cod_ordered" {{ $order->status === 'cod_ordered' ? 'selected' : '' }}>Đã tạo đơn COD (COD Ordered)</option>
                            <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Thất bại (Failed)</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Đã hủy (Cancelled)</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Hoàn tất (Completed)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-slate-300">Trạng Thái Vận Chuyển GHN</label>
                        <select name="shipping_status" class="form-select">
                            <option value="pending" {{ $order->shipping_status === 'pending' ? 'selected' : '' }}>Chờ xử lý (Pending)</option>
                            <option value="ready_to_pick" {{ $order->shipping_status === 'ready_to_pick' ? 'selected' : '' }}>Sẵn sàng lấy hàng (Ready to Pick)</option>
                            <option value="delivering" {{ $order->shipping_status === 'delivering' ? 'selected' : '' }}>Đang giao hàng (Delivering)</option>
                            <option value="delivered" {{ $order->shipping_status === 'delivered' ? 'selected' : '' }}>Đã giao hàng (Delivered)</option>
                            <option value="cancelled" {{ $order->shipping_status === 'cancelled' ? 'selected' : '' }}>Đã hủy giao (Cancelled)</option>
                        </select>
                    </div>
                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-rose text-white fw-bold px-4 py-2.5" style="background-color: #ec4899 !important; border: none; border-radius: 12px; font-weight: 600; box-shadow: 0 4px 15px rgba(236, 72, 153, 0.4); font-size: 0.95rem;">
                            <i class="bi bi-check-lg me-1"></i> Lưu Cập Nhật Thủ Công
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    {{-- Right Side: Customer Info & GHN Code --}}
    <div class="col-lg-4">
        <div class="card-elegant p-4 mb-4">
            <h5 class="fw-bold text-white mb-3 border-bottom border-secondary pb-3">Thông Tin Giao Hàng</h5>

            <div class="mb-3">
                <span class="text-muted fs-8 d-block">Họ và tên người nhận:</span>
                <strong class="text-white fs-6">{{ $order->name }}</strong>
            </div>

            <div class="mb-3">
                <span class="text-muted fs-8 d-block">Số điện thoại:</span>
                <strong class="text-white fs-6">{{ $order->phone }}</strong>
            </div>

            <div class="mb-3">
                <span class="text-muted fs-8 d-block">Email:</span>
                <span class="text-slate-300 fs-7">{{ $order->email ?: 'Chưa cung cấp' }}</span>
            </div>

            <div class="mb-3">
                <span class="text-muted fs-8 d-block">Địa chỉ nhận hàng:</span>
                <div class="text-slate-200 fs-7">{{ $order->address }}</div>
            </div>

            <hr class="border-secondary my-3">

            <div class="mb-3">
                <span class="text-muted fs-8 d-block mb-1">Phương thức thanh toán:</span>
                @if($order->payment_method === 'momo')
                <span class="badge bg-danger fs-8 px-3 py-2 rounded-pill" style="background-color: #a50064 !important;">
                    <i class="bi bi-qr-code-scan me-1"></i> Ví MoMo
                </span>
                @else
                <span class="badge bg-success fs-8 px-3 py-2 rounded-pill">
                    <i class="bi bi-cash-stack me-1"></i> COD (Tiền mặt)
                </span>
                @endif
            </div>

            <div class="mb-3">
                <span class="text-muted fs-8 d-block mb-1">Mã đơn hàng (Hệ thống):</span>
                <span class="badge rounded-pill px-2.5 py-1.5 fs-8 fw-semibold d-inline-flex align-items-center gap-1.5" style="background-color: #131d38; border: 1px solid rgba(236, 72, 153, 0.5); color: #ffffff !important; font-size: 0.82rem;">
                    <i class="bi bi-bag-check text-rose" style="color: #ec4899;"></i> <span class="text-white fw-bold">{{ $order->order_code }}</span>
                </span>
            </div>

            <div>
                <span class="text-muted fs-8 d-block mb-1">Mã vận đơn GHN (Đối chiếu):</span>
                @if($order->ghn_order_code)
                <span class="badge rounded-pill px-2.5 py-1.5 fs-8 fw-semibold d-inline-flex align-items-center gap-1.5" style="background-color: #131d38; border: 1px solid rgba(59, 130, 246, 0.5); color: #ffffff !important; font-size: 0.82rem;">
                    <i class="bi bi-truck text-info"></i> <span class="text-white fw-bold">{{ $order->ghn_order_code }}</span>
                </span>
                @else
                <span class="text-warning fs-8">Chưa có mã vận đơn GHN</span>
                @endif
            </div>


        </div>

        {{-- Transactions Log --}}
        <div class="card-elegant p-4">
            <h5 class="fw-bold text-white mb-3 border-bottom border-secondary pb-3">Lịch Sử Giao Dịch Tài Chính</h5>
            @if($order->transactions->count() > 0)
            <div class="d-flex flex-column gap-3">
                @foreach($order->transactions as $trans)
                <div class="p-3 fs-8" style="background-color: #131d38; border: 1px solid #233054; border-radius: 12px; word-break: break-all;">
                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">
                        <strong class="text-white fs-7">{{ strtoupper($trans->gateway) }}</strong>
                        <span class="badge rounded-pill px-2.5 py-1 {{ $trans->status === 'paid' ? 'bg-success' : ($trans->status === 'failed' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $trans->status }}</span>
                    </div>
                    <div class="text-slate-300 fs-8 mt-1" style="color: #cbd5e1; word-break: break-all;">Mã: <code class="ms-1" style="color: #ffffff !important; background-color: #0b1329; padding: 2px 6px; border-radius: 6px; border: 1px solid #233054; font-weight: 600; word-break: break-all;">{{ $trans->gateway_order_id ?: '-' }}</code></div>
                    <div class="text-slate-300 fs-8" style="color: #cbd5e1;">Số tiền: <strong class="text-warning" style="color: #f59e0b !important;">{{ number_format($trans->amount, 0, ',', '.') }}đ</strong></div>
                    <div class="text-slate-400 fs-9 mt-1" style="color: #94a3b8;">{{ $trans->created_at->format('H:i:s - d/m/Y') }}</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-muted fs-7">Chưa có nhật ký giao dịch.</div>
            @endif
        </div>




    </div>
</div>

@endsection
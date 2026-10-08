@extends('admin.layouts.app')

@section('title', 'Danh sách Sản Phẩm | LuxeLips Admin')
@section('header_title', 'Quản Lý Sản Phẩm')

@section('content')
@php /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Product[] $products */ @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">
        <i class="bi bi-box-seam"></i>
        Danh sách Sản Phẩm
    </h1>
    <a class="btn-rose" href="{{ route('admin.products.create') }}" id="btn-create-product">
        <i class="bi bi-plus-lg me-1"></i> Thêm Sản Phẩm
    </a>
</div>

{{-- Success Alert --}}
@if ($message = Session::get('success'))
<div class="alert alert-success rounded-3 mb-4 d-flex align-items-center" id="alert-success" role="alert" style="background-color: rgba(34, 197, 94, 0.15); border: none; border-left: 4px solid #22c55e; color: #4ade80;">
    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
    {{ $message }}
</div>
@endif

{{-- Summary Statistics Row (3 Cards) --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
            <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #ec4899;">
                <i class="bi bi-bag-heart-fill fs-6"></i>
            </div>
            <div>
                <div class="text-slate-400 fs-8 fw-medium">Tổng số sản phẩm</div>
                <div class="fw-bold text-light" style="font-size: 1.15rem;">{{ $totalProductsCount }} sản phẩm</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
            <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #10b981;">
                <i class="bi bi-eye-fill fs-6"></i>
            </div>
            <div>
                <div class="text-slate-400 fs-8 fw-medium">Sản phẩm đang hoạt động</div>
                <div class="fw-bold text-success" style="font-size: 1.15rem;">{{ $activeProductsCount }} đang hiển thị</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-elegant h-100 rounded-3 d-flex align-items-center" style="padding: 0.85rem 1rem !important; background-color: #131d38; border: 1px solid #233054;">
            <div class="rounded-circle text-white me-2.5 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background-color: #3b82f6;">
                <i class="bi bi-box-seam-fill fs-6"></i>
            </div>
            <div>
                <div class="text-slate-400 fs-8 fw-medium">Tổng tồn kho</div>
                <div class="fw-bold text-light" style="font-size: 1.15rem;">{{ $totalStockSum }} sản phẩm</div>
            </div>
        </div>
    </div>
</div>

{{-- Search & Filter Form --}}
<div class="mb-4">
    <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2.5 align-items-end">
        <div class="col-md-5">
            <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Tìm kiếm sản phẩm</label>
            <div class="input-group">
                <span class="input-group-text border-secondary text-muted" style="background-color: #131d38; height: 38px; border-radius: 8px 0 0 8px;"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control text-light border-secondary admin-filter-input" style="background-color: #131d38; border-radius: 0 8px 8px 0 !important;" placeholder="Tìm tên sản phẩm, mã SKU, loại biến thể..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label text-slate-300 fs-7 fw-semibold mb-1">Danh mục sản phẩm</label>
            <select name="category_id" class="form-select text-light border-secondary admin-filter-input" style="background-color: #131d38;">
                <option value="">-- Tất cả danh mục --</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-rose admin-filter-btn flex-grow-1" style="background-color: #ec4899; border: none;">
                <i class="bi bi-funnel-fill me-1"></i> Lọc dữ liệu
            </button>
            @if(request('search') || request('category_id'))
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary admin-filter-btn" title="Đặt lại bộ lọc">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Table Card --}}
<div class="card-elegant">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-table me-2"></i>
            Tất cả sản phẩm ({{ $products->total() }})
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-nowrap" style="min-width: 110px;">Hình ảnh</th>
                        <th scope="col" class="text-nowrap">Tên sản phẩm</th>
                        <th scope="col" class="text-nowrap">Giá bán</th>
                        <th scope="col" class="text-nowrap">Danh mục</th>
                        <th scope="col" class="text-nowrap">Tồn kho</th>
                        <th scope="col" class="text-nowrap">Trạng thái</th>
                        <th scope="col" class="text-nowrap text-center" style="width: 180px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->first_image_url)
                            <img src="{{ $product->first_image_url }}" alt="{{ $product->name }}" class="rounded-3 border border-secondary object-fit-cover" style="width: 50px; height: 50px;">
                            @else
                            <div class="rounded-3 border border-secondary bg-dark d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                <i class="bi bi-image fs-4"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-light mb-1" style="font-size: 0.85rem;">{{ $product->name }}</div>
                            <span class="badge bg-dark border border-secondary text-slate-300">
                                <i class="bi bi-layers me-1 text-rose"></i> {{ $product->variants->count() }} biến thể
                            </span>
                        </td>
                        <td>
                            <span class="text-success fw-bold">{{ $product->price_range_formatted }}</span>
                        </td>
                        <td>
                            @forelse($product->categories_list as $category)
                            <span class="badge bg-rose text-white me-1 fw-semibold shadow-sm">{{ $category->name }}</span>
                            @empty
                            <span class="text-muted fs-7">Chưa phân loại</span>
                            @endforelse
                        </td>
                        <td>
                            @php $stock = $product->total_stock; @endphp
                            @if($stock > 10)
                            <span class="text-success fw-semibold">
                                <i class="bi bi-check-circle me-1"></i> {{ $stock }} sản phẩm
                            </span>
                            @elseif($stock > 0)
                            <span class="text-warning fw-semibold">
                                <i class="bi bi-exclamation-triangle me-1"></i> {{ $stock }} (Sắp hết)
                            </span>
                            @else
                            <span class="text-danger fw-semibold">
                                <i class="bi bi-x-circle me-1"></i> Hết hàng
                            </span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.products.toggle-status', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $product->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }} rounded-pill" style="font-size: 0.75rem; padding: 2px 10px;" title="Bật/Tắt hiển thị trên trang bán hàng">
                                    @if($product->is_active)
                                        <i class="bi bi-eye-fill me-1"></i> Hiển thị
                                    @else
                                        <i class="bi bi-eye-slash-fill me-1"></i> Đang ẩn
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-nowrap text-center">
                            <div class="d-inline-flex gap-1 justify-content-center py-1">
                                <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-outline-light border-secondary px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Sửa thông tin">
                                    <i class="bi bi-pencil"></i> Sửa
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Xóa sản phẩm">
                                        <i class="bi bi-trash3"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam mb-3 d-block" style="font-size: 3rem; color: #64748b;"></i>
                            <p class="mb-1 fw-medium text-slate-300">Không tìm thấy sản phẩm nào</p>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-rose mt-2">
                                <i class="bi bi-plus-lg"></i> Thêm mới sản phẩm
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($products->hasPages())
    <div class="card-footer bg-transparent border-top border-secondary p-3 d-flex justify-content-between align-items-center">
        <div class="text-muted fs-7">
            Hiển thị từ {{ $products->firstItem() }} đến {{ $products->lastItem() }} trên tổng số {{ $products->total() }} sản phẩm
        </div>
        <div>
            {{ $products->links() }}
        </div>
    </div>
    @endif
</div>

@endsection

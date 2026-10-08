<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Helper to get paid/valid revenue orders query matching ReportController.
     */
    private function validRevenueOrders()
    {
        $paymentStatus = DB::table('payment_transactions')->select('status')
            ->whereColumn('order_id', 'orders.id')
            ->orderByRaw("CASE WHEN status IN ('paid', 'refund_pending', 'refunded') THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->limit(1);

        return Order::query()
            ->where('orders.created_at', '<=', now())
            ->where('orders.status', '!=', 'cancelled')
            ->whereNotIn('orders.shipping_status', ['cancelled', 'return', 'returned'])
            ->where(function ($q) use ($paymentStatus) {
                $q->where($paymentStatus, 'paid')
                    ->orWhere(function ($legacy) {
                        $legacy->whereDoesntHave('transactions')
                            ->whereIn('orders.status', ['paid', 'cod_paid', 'paid_momo']);
                    });
            });
    }

    /**
     * Display the Admin Summary Dashboard with real database metrics.
     */
    public function index()
    {
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalCategories = Category::count();
        $activeProductsCount = Product::where('is_active', true)->count();
        $totalStockSum = ProductVariant::sum('stock');
        
        $totalOrders = Order::count();
        $totalRevenue = (float) $this->validRevenueOrders()->sum('total_price');

        // Order Status Counters
        $pendingOrdersCount = Order::where(function ($q) {
            $q->whereIn('status', ['pending', 'cod_ordered'])
              ->orWhereIn('shipping_status', ['pending', 'preparing']);
        })->where('status', '!=', 'cancelled')->count();

        $shippingOrdersCount = Order::where(function ($q) {
            $q->whereIn('shipping_status', ['shipping', 'delivering']);
        })->where('status', '!=', 'cancelled')->count();

        $completedOrdersCount = Order::where(function ($q) {
            $q->whereIn('status', ['paid', 'cod_paid', 'completed'])
              ->orWhere('shipping_status', 'delivered');
        })->count();

        $cancelledOrdersCount = Order::where('status', 'cancelled')
            ->orWhereIn('shipping_status', ['cancelled', 'return', 'returned'])
            ->count();

        // Recent Orders
        $recentOrders = Order::latest()->take(5)->get();

        // Recent Products
        $recentProducts = Product::with('variants.category')
            ->latest()
            ->take(5)
            ->get();

        // Recent Users
        $recentUsers = User::latest()->take(5)->get();

        // 7-day mini revenue dataset
        $start7Days = Carbon::now()->startOfDay()->subDays(6);
        $daily7Days = $this->validRevenueOrders()
            ->selectRaw('DATE(orders.created_at) as date, SUM(total_price) as total_revenue')
            ->where('orders.created_at', '>=', $start7Days)
            ->groupByRaw('DATE(orders.created_at)')
            ->pluck('total_revenue', 'date');

        $chart7DayLabels = [];
        $chart7DayData = [];
        for ($i = 0; $i < 7; $i++) {
            $dateStr = $start7Days->copy()->addDays($i)->toDateString();
            $chart7DayLabels[] = Carbon::parse($dateStr)->format('d/m');
            $chart7DayData[] = (float) ($daily7Days->get($dateStr) ?? 0);
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalUsers',
            'totalCategories',
            'activeProductsCount',
            'totalStockSum',
            'totalRevenue',
            'totalOrders',
            'pendingOrdersCount',
            'shippingOrdersCount',
            'completedOrdersCount',
            'cancelledOrdersCount',
            'recentOrders',
            'recentProducts',
            'recentUsers',
            'chart7DayLabels',
            'chart7DayData'
        ));
    }

    /**
     * AJAX Endpoint: Return live real-time JSON statistics for Admin Dashboard.
     */
    public function apiStats(Request $request)
    {
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalCategories = Category::count();
        $activeProductsCount = Product::where('is_active', true)->count();
        $totalStockSum = ProductVariant::sum('stock');
        
        $totalOrders = Order::count();
        $totalRevenue = (float) $this->validRevenueOrders()->sum('total_price');

        $pendingOrdersCount = Order::where(function ($q) {
            $q->whereIn('status', ['pending', 'cod_ordered'])
              ->orWhereIn('shipping_status', ['pending', 'preparing']);
        })->where('status', '!=', 'cancelled')->count();

        $shippingOrdersCount = Order::where(function ($q) {
            $q->whereIn('shipping_status', ['shipping', 'delivering']);
        })->where('status', '!=', 'cancelled')->count();

        $completedOrdersCount = Order::where(function ($q) {
            $q->whereIn('status', ['paid', 'cod_paid', 'completed'])
              ->orWhere('shipping_status', 'delivered');
        })->count();

        $cancelledOrdersCount = Order::where('status', 'cancelled')
            ->orWhereIn('shipping_status', ['cancelled', 'return', 'returned'])
            ->count();

        $recentOrders = Order::latest()->take(5)->get()->map(function ($o) {
            return [
                'id' => $o->id,
                'order_code' => $o->order_code ?? ('#' . $o->id),
                'name' => $o->name,
                'total_price' => number_format($o->total_price, 0, ',', '.') . ' ₫',
                'status' => $o->status,
                'shipping_status' => $o->shipping_status,
                'created_at_formatted' => $o->created_at ? $o->created_at->format('d/m H:i') : '',
            ];
        });

        $recentProducts = Product::with('variants.category')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'image_url' => $p->first_image_url,
                    'category_name' => $p->variants->first()?->category?->name ?? 'Mỹ phẩm',
                    'price_formatted' => $p->price_range_formatted,
                    'is_active' => $p->is_active,
                ];
            });

        $recentUsers = User::latest()->take(5)->get()->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'avatar_url' => $u->avatar_url,
                'is_admin' => $u->isAdmin(),
            ];
        });

        $start7Days = Carbon::now()->startOfDay()->subDays(6);
        $daily7Days = $this->validRevenueOrders()
            ->selectRaw('DATE(orders.created_at) as date, SUM(total_price) as total_revenue')
            ->where('orders.created_at', '>=', $start7Days)
            ->groupByRaw('DATE(orders.created_at)')
            ->pluck('total_revenue', 'date');

        $chart7DayLabels = [];
        $chart7DayData = [];
        for ($i = 0; $i < 7; $i++) {
            $dateStr = $start7Days->copy()->addDays($i)->toDateString();
            $chart7DayLabels[] = Carbon::parse($dateStr)->format('d/m');
            $chart7DayData[] = (float) ($daily7Days->get($dateStr) ?? 0);
        }

        return response()->json([
            'success' => true,
            'timestamp' => now()->format('H:i:s d/m/Y'),
            'total_products' => $totalProducts,
            'total_users' => $totalUsers,
            'total_categories' => $totalCategories,
            'active_products_count' => $activeProductsCount,
            'total_stock_sum' => $totalStockSum,
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'total_revenue_formatted' => number_format($totalRevenue, 0, ',', '.') . ' ₫',
            'pending_orders_count' => $pendingOrdersCount,
            'shipping_orders_count' => $shippingOrdersCount,
            'completed_orders_count' => $completedOrdersCount,
            'cancelled_orders_count' => $cancelledOrdersCount,
            'recent_orders' => $recentOrders,
            'recent_products' => $recentProducts,
            'recent_users' => $recentUsers,
            'chart7_labels' => $chart7DayLabels,
            'chart7_data' => $chart7DayData,
        ]);
    }
}

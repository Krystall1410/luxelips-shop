<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Build base query for paid and valid revenue orders with dynamic filters.
     */
    private function paidOrders(?Request $request = null): Builder
    {
        $query = Order::query()
            ->where('orders.created_at', '<=', now())
            ->where('orders.status', '!=', 'cancelled')
            ->whereNotIn('orders.shipping_status', ['cancelled', 'return', 'returned']);

        if ($request) {
            // Preset filters
            if ($request->filled('preset')) {
                match ($request->preset) {
                    'today' => $query->whereDate('orders.created_at', Carbon::today()),
                    '7days' => $query->where('orders.created_at', '>=', Carbon::now()->subDays(6)->startOfDay()),
                    '30days' => $query->where('orders.created_at', '>=', Carbon::now()->subDays(29)->startOfDay()),
                    'this_month' => $query->where('orders.created_at', '>=', Carbon::now()->startOfMonth()),
                    'this_year' => $query->where('orders.created_at', '>=', Carbon::now()->startOfYear()),
                    default => null,
                };
            } else {
                if ($request->filled('from_date')) {
                    $query->where('orders.created_at', '>=', Carbon::parse($request->from_date)->startOfDay());
                }
                if ($request->filled('to_date')) {
                    $query->where('orders.created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
                }
            }

            // Category filter
            if ($request->filled('category_id')) {
                $catId = $request->category_id;
                $query->whereIn('orders.id', function ($sub) use ($catId) {
                    $sub->select('order_items.order_id')
                        ->from('order_items')
                        ->join('product_variants', 'order_items.product_id', '=', 'product_variants.product_id')
                        ->where('product_variants.category_id', $catId);
                });
            }
        }

        $paymentStatus = DB::table('payment_transactions')->select('status')
            ->whereColumn('order_id', 'orders.id')
            ->orderByRaw("CASE WHEN status IN ('paid', 'refund_pending', 'refunded') THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->limit(1);

        return $query->where(function (Builder $q) use ($paymentStatus) {
            $q->where($paymentStatus, 'paid')
                ->orWhere(function (Builder $legacy) {
                    $legacy->whereDoesntHave('transactions')
                        ->whereIn('orders.status', ['paid', 'cod_paid', 'paid_momo']);
                });
        });
    }

    /**
     * Calculate revenue grouped by product category.
     */
    private function categoryRevenue(?Request $request = null): Collection
    {
        $productCategories = DB::table('product_variants')
            ->select('product_id', 'category_id')
            ->whereNotNull('category_id')
            ->groupBy('product_id', 'category_id');

        $query = DB::table('order_items')
            ->joinSub($productCategories, 'product_cats', 'order_items.product_id', '=', 'product_cats.product_id')
            ->leftJoin('categories', 'product_cats.category_id', '=', 'categories.id')
            ->whereIn('order_items.order_id', $this->paidOrders($request)->select('orders.id'));

        if ($request && $request->filled('category_id')) {
            $query->where('product_cats.category_id', $request->category_id);
        }

        return $query->select('product_cats.category_id', 'categories.name as category_name')
            ->selectRaw('SUM(order_items.price * order_items.quantity) as total_revenue, SUM(order_items.quantity) as total_qty')
            ->groupBy('product_cats.category_id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();
    }

    /**
     * Calculate daily revenue.
     */
    private function dailyRevenue(?Request $request = null): Collection
    {
        return $this->paidOrders($request)
            ->selectRaw('DATE(orders.created_at) as date, SUM(total_price) as total_revenue, COUNT(*) as order_count')
            ->groupByRaw('DATE(orders.created_at)')
            ->orderBy('date')
            ->get();
    }

    /**
     * Aggregate daily data by month or year.
     */
    private function periodRevenue(Collection $days, string $period): Collection
    {
        return $days->groupBy(fn ($day) => substr($day->date, 0, $period === 'month' ? 7 : 4))
            ->map(fn (Collection $rows, $key) => (object) [
                $period => (string) $key,
                'total_revenue' => $rows->sum('total_revenue'),
                'order_count' => $rows->sum('order_count'),
            ])->values();
    }

    /**
     * Render revenue report tables index view.
     */
    public function index(Request $request)
    {
        $allCategories = Category::orderBy('name')->get();
        $categoryRevenue = $this->categoryRevenue($request);
        $totalOrders = $this->paidOrders($request)->count();
        $totalCustomers = DB::table('users')->where('role', 'user')->count();
        $revenueByDate = $this->dailyRevenue($request);
        $revenueByMonth = $this->periodRevenue($revenueByDate, 'month');
        $revenueByYear = $this->periodRevenue($revenueByDate, 'year');
        $totalRevenue = $revenueByDate->sum('total_revenue');

        return view('admin.reports.index', compact(
            'allCategories',
            'categoryRevenue',
            'totalOrders',
            'totalCustomers',
            'totalRevenue',
            'revenueByDate',
            'revenueByMonth',
            'revenueByYear'
        ));
    }

    /**
     * Render revenue report interactive charts view.
     */
    public function charts(Request $request)
    {
        $allCategories = Category::orderBy('name')->get();
        $categories = $this->categoryRevenue($request);
        $catLabels = $categories->map(fn ($row) => $row->category_name ?? 'Danh mục #'.$row->category_id)->all();
        $catRevenue = $categories->pluck('total_revenue')->map(fn ($value) => (float) $value)->all();

        $daily = $this->dailyRevenue($request);
        $byDate = $daily->keyBy('date');
        $byMonth = $this->periodRevenue($daily, 'month')->keyBy('month');
        $byYear = $this->periodRevenue($daily, 'year');

        $startDay = Carbon::now()->startOfDay()->subDays(29);
        $startMonth = Carbon::now()->startOfMonth()->subMonths(11);

        $revDateLabels = $revDateData = $revMonthLabels = $revMonthData = [];

        for ($i = 0; $i < 30; $i++) {
            $date = $startDay->copy()->addDays($i)->toDateString();
            $revDateLabels[] = Carbon::parse($date)->format('d/m');
            $revDateData[] = (float) ($byDate->get($date)?->total_revenue ?? 0);
        }

        for ($i = 0; $i < 12; $i++) {
            $month = $startMonth->copy()->addMonths($i);
            $revMonthLabels[] = $month->format('m/Y');
            $revMonthData[] = (float) ($byMonth->get($month->format('Y-m'))?->total_revenue ?? 0);
        }

        $revYearLabels = $byYear->pluck('year')->all();
        $revYearData = $byYear->pluck('total_revenue')->map(fn ($value) => (float) $value)->all();

        $gateway = DB::table('payment_transactions')->select('gateway')
            ->whereColumn('order_id', 'orders.id')
            ->where('status', 'paid')
            ->orderByDesc('id')
            ->limit(1);

        $paid = $this->paidOrders($request)->select('orders.total_price')->selectSub($gateway, 'gateway')
            ->selectRaw("CASE WHEN orders.status = 'cod_paid' THEN 'cod' ELSE 'momo' END as legacy_gateway");

        $methodRevenue = DB::query()->fromSub($paid, 'paid_orders')
            ->selectRaw('COALESCE(gateway, legacy_gateway) as method, SUM(total_price) as revenue')
            ->groupByRaw('COALESCE(gateway, legacy_gateway)')
            ->pluck('revenue', 'method');

        $paymentMethodLabels = ['Ví MoMo', 'Thanh toán COD'];
        $paymentMethodRevenue = [(float) $methodRevenue->get('momo', 0), (float) $methodRevenue->get('cod', 0)];

        return view('admin.reports.charts', compact(
            'allCategories',
            'catLabels',
            'catRevenue',
            'revDateLabels',
            'revDateData',
            'revMonthLabels',
            'revMonthData',
            'revYearLabels',
            'revYearData',
            'paymentMethodLabels',
            'paymentMethodRevenue'
        ));
    }
}

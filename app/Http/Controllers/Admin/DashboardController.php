<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Category;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSales = Order::sum('total');
        $dailyRevenue = Order::whereDate('created_at', now()->toDateString())->sum('total');
        $ordersCount = Order::count();
        $activeUsers = User::where('role', User::ROLE_CUSTOMER)->count();
        $conversionRate = $activeUsers > 0 ? round(($ordersCount / $activeUsers) * 100, 1) : 0;

        $topProductIds = DB::table('order_items')
            ->select('product_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit(5)
            ->pluck('product_id');

        $topProducts = Product::whereIn('id', $topProductIds)->get();
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)->orderBy('stock_quantity')->take(8)->get();
        $recentOrders = Order::with('user')->latest()->take(8)->get();
        $recentActivity = AnalyticsEvent::latest()->take(8)->get();

        $salesByDay = Order::selectRaw('DATE(created_at) as day, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $ordersByDay = Order::selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $rangeDays = collect(range(13, 0))->map(fn (int $offset) => now()->subDays($offset)->toDateString());
        $salesMap = $salesByDay->keyBy('day');
        $ordersMap = $ordersByDay->keyBy('day');

        $chartLabels = $rangeDays->map(fn (string $day) => \Carbon\Carbon::parse($day)->format('M d'));
        $salesSeries = $rangeDays->map(fn (string $day) => round((float) ($salesMap[$day]->total ?? 0), 2));
        $ordersSeries = $rangeDays->map(fn (string $day) => (int) ($ordersMap[$day]->count ?? 0));

        $previousPeriodSales = Order::whereBetween('created_at', [now()->subDays(27)->startOfDay(), now()->subDays(14)->endOfDay()])->sum('total');
        $salesTrend = $previousPeriodSales > 0
            ? round((($totalSales - $previousPeriodSales) / $previousPeriodSales) * 100, 1)
            : 0;

        $todayOrders = Order::whereDate('created_at', now()->toDateString())->count();
        $yesterdayOrders = Order::whereDate('created_at', now()->subDay()->toDateString())->count();
        $ordersTrend = $yesterdayOrders > 0 ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1) : 0;

        $stockedProducts = Product::where('stock_quantity', '>', 0)->count();
        $totalProducts = Product::count();
        $inventoryHealth = $totalProducts > 0 ? round(($stockedProducts / $totalProducts) * 100, 1) : 0;

        return view('admin.dashboard', [
            'totalSales' => $totalSales,
            'dailyRevenue' => $dailyRevenue,
            'ordersCount' => $ordersCount,
            'activeUsers' => $activeUsers,
            'conversionRate' => $conversionRate,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'recentOrders' => $recentOrders,
            'recentActivity' => $recentActivity,
            'chartLabels' => $chartLabels,
            'salesSeries' => $salesSeries,
            'ordersSeries' => $ordersSeries,
            'salesTrend' => $salesTrend,
            'ordersTrend' => $ordersTrend,
            'inventoryHealth' => $inventoryHealth,
        ]);
    }

    public function customers()
    {
        $customers = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderByDesc('orders_sum_total')
            ->paginate(20);

        return view('admin.customers', compact('customers'));
    }

    public function updateCustomerPassword(Request $request, User $user)
    {
        abort_unless($user->role === User::ROLE_CUSTOMER, 404);

        $validated = $request->validate([
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => null,
        ])->save();

        return back()->with('success', "Password updated for {$user->name}.");
    }

    public function inventory()
    {
        $products = Product::with('category')->orderBy('stock_quantity')->paginate(25);
        $recentLogs = InventoryLog::with(['product', 'user'])->latest()->take(20)->get();

        return view('admin.inventory', compact('products', 'recentLogs'));
    }

    public function analytics()
    {
        $eventTypes = AnalyticsEvent::selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->orderByDesc('count')
            ->get();

        $topViewedProducts = AnalyticsEvent::query()
            ->select('product_id', DB::raw('COUNT(*) as views'))
            ->where('event_type', 'product_view')
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('views')
            ->with('product')
            ->take(10)
            ->get();

        $salesByDay = Order::selectRaw('DATE(created_at) as day, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $rangeDays = collect(range(29, 0))->map(fn (int $offset) => now()->subDays($offset)->toDateString());
        $salesMap = $salesByDay->keyBy('day');
        $salesLabels = $rangeDays->map(fn (string $day) => \Carbon\Carbon::parse($day)->format('M d'));
        $salesValues = $rangeDays->map(fn (string $day) => round((float) ($salesMap[$day]->total ?? 0), 2));

        return view('admin.analytics', compact('eventTypes', 'topViewedProducts', 'salesLabels', 'salesValues'));
    }

    public function settings()
    {
        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'categories' => Category::count(),
            'order_items' => OrderItem::count(),
        ];

        return view('admin.settings', compact('stats'));
    }
}

<?php

namespace App\Http\Controllers\Dashboards;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Quotation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $orders = Order::count();
        $completedOrders = Order::where('order_status', OrderStatus::COMPLETE)
            ->count();

        $products = Product::count();

        $purchases = Purchase::count();
        $todayPurchases = Purchase::query()
            ->where('date', today())
            ->get()
            ->count();

        $categories = Category::count();

        $quotations = Quotation::count();
        $todayQuotations = Quotation::query()
            ->where('date', today()->format('Y-m-d'))
            ->get()
            ->count();

        // Doanh thu 7 ngày gần nhất
        $revenueData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyRevenue = Order::where('order_status', OrderStatus::COMPLETE)
                ->whereDate('order_date', $date)
                ->sum('total');

            $revenueData[] = [
                'date' => $date->format('d/m'),
                'revenue' => $dailyRevenue,
                'order_count' => Order::where('order_status', OrderStatus::COMPLETE)
                    ->whereDate('order_date', $date)
                    ->count()
            ];
        }

        // Top sản phẩm bán chạy (10 sản phẩm) - theo tổng doanh thu
        $topProducts = OrderDetails::with('product')
            ->whereHas('order', function($query) {
                $query->where('order_status', OrderStatus::COMPLETE);
            })
            ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(total) as total_revenue')
            ->groupBy('product_id')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Đơn hàng gần đây (10 đơn)
        $recentOrders = Order::with('customer')
            ->latest()
            ->limit(10)
            ->get();

        // Sản phẩm tồn kho thấp (dưới 10)
        $lowStockProducts = Product::with('category')
            ->where('quantity', '<', 10)
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get();

        // Tổng doanh thu tháng này
        $monthlyRevenue = Order::where('order_status', OrderStatus::COMPLETE)
            ->whereMonth('order_date', Carbon::now()->month)
            ->whereYear('order_date', Carbon::now()->year)
            ->sum('total');

        // Tổng số khách hàng
        $totalCustomers = \App\Models\Customer::count();

        // Data cho biểu đồ 7 ngày từ bảng revenueData
        $weeklyChartData = [];
        $weeklyDates = [];
        foreach ($revenueData as $data) {
            $weeklyChartData[] = $data['revenue'];
            $weeklyDates[] = $data['date'];
        }

        // Data cho biểu đồ top sản phẩm (5 sản phẩm) - theo tổng doanh thu
        $topProductsChart = OrderDetails::with('product')
            ->whereHas('order', function($query) {
                $query->where('order_status', OrderStatus::COMPLETE);
            })
            ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(total) as total_revenue')
            ->groupBy('product_id')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        $productNames = [];
        $productRevenues = [];
        foreach ($topProductsChart as $item) {
            $productNames[] = $item->product->name;
            $productRevenues[] = (float) $item->total_revenue;
        }

        // Data cho biểu đồ tỷ lệ hoàn thành đơn hàng (pie chart)
        $completedOrdersCount = Order::where('order_status', OrderStatus::COMPLETE)->count();
        $pendingOrdersCount = Order::where('order_status', OrderStatus::PENDING)->count();
        $totalOrderCount = $completedOrdersCount + $pendingOrdersCount;

        $completionRate = $totalOrderCount > 0 ? round(($completedOrdersCount / $totalOrderCount) * 100, 1) : 0;

        // Data cho biểu đồ so sánh tháng trước và tháng này
        $lastMonth = Carbon::now()->subMonth();
        $thisMonthRevenue = Order::where('order_status', OrderStatus::COMPLETE)
            ->whereMonth('order_date', Carbon::now()->month)
            ->whereYear('order_date', Carbon::now()->year)
            ->sum('total');

        $lastMonthRevenue = Order::where('order_status', OrderStatus::COMPLETE)
            ->whereMonth('order_date', $lastMonth->month)
            ->whereYear('order_date', $lastMonth->year)
            ->sum('total');

        return view('dashboard', [
            'products' => $products,
            'orders' => $orders,
            'completedOrders' => $completedOrders,
            'purchases' => $purchases,
            'todayPurchases' => $todayPurchases,
            'categories' => $categories,
            'quotations' => $quotations,
            'todayQuotations' => $todayQuotations,
            'revenueData' => $revenueData,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'monthlyRevenue' => $monthlyRevenue,
            'totalCustomers' => $totalCustomers,
            // Chart data
            'weeklyChartData' => $weeklyChartData,
            'weeklyDates' => $weeklyDates,
            'productNames' => $productNames,
            'productRevenues' => $productRevenues,
            'completionRate' => $completionRate,
            'completedOrdersCount' => $completedOrdersCount,
            'pendingOrdersCount' => $pendingOrdersCount,
            'thisMonthRevenue' => $thisMonthRevenue,
            'lastMonthRevenue' => $lastMonthRevenue,
        ]);
    }
}

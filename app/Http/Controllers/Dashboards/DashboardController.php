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
use Illuminate\Support\Facades\DB;

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

        // Thống kê theo giờ trong ngày
        $today = Carbon::today();
        $hourlyStats = Order::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->whereDate('created_at', $today)
            ->where('order_status', OrderStatus::COMPLETE)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $hourlyLabels = [];
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyLabels[] = sprintf('%02d:00', $i);
            $found = $hourlyStats->firstWhere('hour', $i);
            $hourlyData[] = $found ? (float)$found->total_revenue : 0;
        }

        // Thống kê theo thứ trong tuần
        $weeklyStats = Order::select(
                DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('order_status', OrderStatus::COMPLETE)
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        $weekDays = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        $weeklyDayLabels = [];
        $weeklyDayData = [];
        
        for ($i = 1; $i <= 7; $i++) {
            $weeklyDayLabels[] = $weekDays[$i % 7];
            $found = $weeklyStats->firstWhere('day_of_week', $i);
            $weeklyDayData[] = $found ? (float)$found->total_revenue : 0;
        }

        // Thống kê theo tháng trong năm
        $monthlyStats = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->where('order_status', OrderStatus::COMPLETE)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyLabels = [];
        $monthlyData = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $currentMonth = Carbon::now()->subMonths(12 - $i);
            $monthlyLabels[] = $currentMonth->format('m/Y');
            
            $found = $monthlyStats->first(function($item) use ($currentMonth) {
                return $item->month == $currentMonth->month && 
                       $item->year == $currentMonth->year;
            });
            
            $monthlyData[] = $found ? (float)$found->total_revenue : 0;
        }

        // Thống kê theo ngày (30 ngày gần nhất)
        $dailyRangeStart = Carbon::now()->startOfDay()->subDays(29);
        $dailyStats = Order::select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->whereDate('created_at', '>=', $dailyRangeStart)
            ->where('order_status', OrderStatus::COMPLETE)
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $dailyLabels = [];
        $dailyData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $dailyLabels[] = $date->format('d/m');
            $found = $dailyStats->firstWhere('day', $date->toDateString());
            $dailyData[] = $found ? (float)$found->total_revenue : 0;
        }

        // Thống kê theo năm (5 năm gần nhất)
        $yearlyStats = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subYears(5)->startOfYear())
            ->where('order_status', OrderStatus::COMPLETE)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $yearlyLabels[] = (string)$year;
            $found = $yearlyStats->firstWhere('year', $year);
            $yearlyData[] = $found ? (float)$found->total_revenue : 0;
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
            
            // Time-based statistics
            'hourlyLabels' => $hourlyLabels,
            'hourlyData' => $hourlyData,
            'weeklyDayLabels' => $weeklyDayLabels,
            'weeklyDayData' => $weeklyDayData,
            'monthlyLabels' => $monthlyLabels,
            'monthlyData' => $monthlyData,
            'dailyLabels' => $dailyLabels,
            'dailyData' => $dailyData,
            'yearlyLabels' => $yearlyLabels,
            'yearlyData' => $yearlyData,
        ]);
    }
}

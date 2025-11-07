@extends('layouts.tabler')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Tổng quan
                    </div>
                    <h2 class="page-title">
                        Bảng điều khiển
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="d-flex gap-2">
                        <a href="{{ route('products.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <x-icon.plus/>
                            Thêm sản phẩm mới
                        </a>
                        <a href="{{ route('products.create') }}" class="btn btn-primary d-sm-none btn-icon" aria-label="Create new report">
                            <x-icon.plus/>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="col-12">
                    <div class="row row-cards">
                        <div class="col-sm-6 col-lg-3">
                            <a href="{{ route('products.store') }}" class="text-decoration-none">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                    <span class="bg-primary text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" /><path d="M2 13.5v5.5l5 3" /><path d="M7 16.545l5 -3.03" /><path d="M17 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" /><path d="M12 19l5 3" /><path d="M17 16.5l5 -3" /><path d="M12 13.5v-5.5l-5 -3l5 -3l5 3v5.5" /><path d="M7 5.03v5.455" /><path d="M12 8l5 -3" /></svg>
                                    </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                {{ $products }} Sản phẩm
                                            </div>
                                            <div class="text-muted">
                                                {{ $categories }} danh mục
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <a href="{{ route('orders.index') }}" class="text-decoration-none">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                    <span class="bg-green text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/shopping-cart -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                                    </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                {{ $orders }} Đơn hàng
                                            </div>
                                            <div class="text-muted">
                                                {{ $completedOrders }} hoàn thành
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <a href="{{ route('purchases.store') }}" class="text-decoration-none">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                    <span class="bg-twitter text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/brand-twitter -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck-delivery" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /><path d="M3 9l4 0" /></svg>
                                    </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                {{ $quotations }} Báo giá
                                            </div>
                                            <div class="text-muted">
                                                {{ $todayQuotations }} hôm nay
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                    <span class="bg-success text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-coin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 6v2m0 8v2" /></svg>
                                    </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                Doanh thu tháng
                                            </div>
                                            <div class="text-muted">
                                                {{ format_currency($monthlyRevenue) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    </div>
                </div>

                <!-- Biểu đồ 7 ngày từ bảng dữ liệu -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">📈 Thống kê doanh thu theo thời gian</h3>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs" data-bs-toggle="tabs">
                                <li class="nav-item">
                                    <a href="#tab-hourly" class="nav-link active" data-bs-toggle="tab">Theo giờ</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-daily" class="nav-link" data-bs-toggle="tab">Theo ngày</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-weekday" class="nav-link" data-bs-toggle="tab">Theo thứ</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-monthly" class="nav-link" data-bs-toggle="tab">Theo tháng</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-yearly" class="nav-link" data-bs-toggle="tab">Theo năm</a>
                                </li>
                            </ul>
                            <div class="tab-content pt-3">
                                <div class="tab-pane active show" id="tab-hourly">
                                    <div id="hourly-chart" style="height: 350px; width:100%"></div>
                                </div>
                                <div class="tab-pane" id="tab-daily">
                                    <div id="daily-chart" style="height: 350px; width:100%"></div>
                                </div>
                                <div class="tab-pane" id="tab-weekday">
                                    <div id="weekday-chart" style="height: 350px; width:100%"></div>
                                </div>
                                <div class="tab-pane" id="tab-monthly">
                                    <div id="monthly-chart" style="height: 350px; width:100%"></div>
                                </div>
                                <div class="tab-pane" id="tab-yearly">
                                    <div id="yearly-chart" style="height: 350px; width:100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bảng thống kê doanh thu 7 ngày -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Doanh thu 7 ngày gần nhất</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Ngày</th>
                                            <th>Số đơn hàng</th>
                                            <th>Doanh thu</th>
                                            <th>Trung bình/đơn</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($revenueData as $data)
                                            <tr>
                                                <td><strong>{{ $data['date'] ?? 'N/A' }}</strong></td>
                                                <td>{{ $data['order_count'] ?? 0 }}</td>
                                                <td><strong>{{ format_currency($data['revenue'] ?? 0) }}</strong></td>
                                                <td>
                                                    @if(($data['order_count'] ?? 0) > 0)
                                                        {{ format_currency(($data['revenue'] ?? 0) / ($data['order_count'] ?? 1)) }}
                                                    @else
                                                        0 ₫
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thống kê theo thời gian giống KiotViet: Giờ / Ngày / Thứ / Tháng / Năm -->
                

                <!-- Bảng top sản phẩm và đơn hàng gần đây -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top sản phẩm bán chạy</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Số lượng</th>
                                            <th>Doanh thu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topProducts as $product)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <div class="font-weight-medium">{{ $product->product->name ?? 'N/A' }}</div>
                                                            <div class="text-muted">{{ $product->product->code ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><strong>{{ $product->total_sold ?? 0 }}</strong></td>
                                                <td><strong>{{ format_currency($product->total_revenue ?? 0) }}</strong></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Đơn hàng gần đây</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Hóa đơn</th>
                                            <th>Khách hàng</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentOrders as $order)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <div class="font-weight-medium">{{ $order->invoice_no ?? 'N/A' }}</div>
                                                        <div class="text-muted">{{ $order->order_date ? $order->order_date->format('d/m/Y') : 'N/A' }}</div>
                                                    </div>
                                                </td>
                                                <td>{{ $order->customer->name ?? 'Khách lẻ' }}</td>
                                                <td><strong>{{ format_currency($order->total ?? 0) }}</strong></td>
                                                <td>
                                                    @if($order->order_status && $order->order_status->value == 'complete')
                                                        <span class="badge bg-success">Hoàn thành</span>
                                                    @elseif($order->order_status && $order->order_status->value == 'pending')
                                                        <span class="badge bg-warning">Chờ xử lý</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $order->order_status->value ?? 'N/A' }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Chưa có đơn hàng</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bảng sản phẩm tồn kho thấp -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Cảnh báo tồn kho thấp</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Mã sản phẩm</th>
                                            <th>Tồn kho</th>
                                            <th>Đơn giá</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lowStockProducts as $product)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <div class="font-weight-medium">{{ $product->name ?? 'N/A' }}</div>
                                                            <div class="text-muted">{{ $product->category->name ?? 'Chưa phân loại' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $product->code ?? 'N/A' }}</td>
                                                <td>
                                                    @if($product->quantity <= 0)
                                                        <span class="badge bg-danger">{{ $product->quantity ?? 0 }}</span>
                                                    @else
                                                        <span class="badge bg-warning">{{ $product->quantity ?? 0 }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ format_currency($product->selling_price ?? 0) }}</td>
                                                <td>
                                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">
                                                        Cập nhật
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Không có sản phẩm tồn kho thấp</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
@endsection

<script src="https://unpkg.com/apexcharts@3.45.1/dist/apexcharts.min.js"></script>

@pushonce('page-scripts')
    <script>
        // @formatter:off
        console.log('Dashboard script loaded');

        // Kiểm tra ApexCharts
        console.log('ApexCharts available:', typeof ApexCharts !== 'undefined');

        // Kiểm tra data
        console.log('Weekly chart data:', @json($weeklyChartData));
        console.log('Weekly dates:', @json($weeklyDates));

        document.addEventListener("DOMContentLoaded", function () {
            console.log('DOM Content Loaded');
            console.log('ApexCharts available:', typeof ApexCharts !== 'undefined');

            if (typeof ApexCharts !== 'undefined') {
                console.log('ApexCharts is available, creating weekly chart...');

                // Render weekly chart từ dữ liệu thực
                renderWeeklyChart();
                // Render time-based charts
                renderHourlyTimeChart();
                renderDailyTimeChart();
                renderWeekdayTimeChart();
                renderMonthlyTimeChart();
                renderYearlyTimeChart();

                // Re-render on tab shown (fix initial hidden size)
                const tabLinks = document.querySelectorAll('a[data-bs-toggle="tab"]');
                tabLinks.forEach(link => {
                    link.addEventListener('shown.bs.tab', function(e) {
                        const target = e.target.getAttribute('href');
                        switch(target) {
                            case '#tab-hourly':
                                renderHourlyTimeChart();
                                break;
                            case '#tab-daily':
                                renderDailyTimeChart();
                                break;
                            case '#tab-weekday':
                                renderWeekdayTimeChart();
                                break;
                            case '#tab-monthly':
                                renderMonthlyTimeChart();
                                break;
                            case '#tab-yearly':
                                renderYearlyTimeChart();
                                break;
                        }
                    });
                });
            }
        });

        // Test functions
        function showChartStatus() {
            const statusDiv = document.getElementById('chart-status');
            if (statusDiv) {
                statusDiv.innerHTML = `
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 15px;">
                        <strong>Chart Status:</strong><br>
                        ApexCharts defined: ${typeof ApexCharts !== 'undefined'}<br>
                        Weekly chart element: ${document.getElementById('weekly-revenue-chart') !== null}<br>
                        Script loaded: ${document.querySelector('script[src*="apexcharts"]') !== null}
                    </div>
                `;
            }
        }

        function renderWeeklyChart() {
            const weeklyElement = document.getElementById('weekly-revenue-chart');
            if (!weeklyElement) {
                console.error('Weekly chart element not found');
                return;
            }

            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts not available for weekly chart');
                weeklyElement.innerHTML = '<div style="text-align: center; padding: 50px; color: #dc3545;"><h5>ApexCharts chưa sẵn sàng</h5></div>';
                return;
            }

            console.log('Creating weekly chart with real data...');
            console.log('Weekly data:', @json($weeklyChartData));
            console.log('Weekly dates:', @json($weeklyDates));

            // Weekly chart options với data thực từ controller
            const weeklyOptions = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '70%',
                        distributed: false,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                series: [{
                    name: 'Doanh thu',
                    data: @json($weeklyChartData)
                }],
                xaxis: {
                    categories: @json($weeklyDates),
                    labels: {
                        style: {
                            colors: '#6c757d',
                            fontSize: '12px'
                        },
                        rotate: 0
                    },
                    axisBorder: {
                        show: true,
                        color: '#e9ecef'
                    },
                    axisTicks: {
                        show: true,
                        color: '#e9ecef'
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6c757d'
                        },
                        formatter: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND',
                                notation: 'compact'
                            }).format(value);
                        }
                    },
                    title: {
                        text: 'Doanh thu (VND)',
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    }
                },
                colors: ['#ff6b6b'],
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(value);
                        }
                    },
                    marker: {
                        show: true
                    }
                },
                grid: {
                    strokeDashArray: 4,
                    borderColor: '#e9ecef'
                },
                dataLabels: {
                    enabled: true,
                    offsetY: -20,
                    style: {
                        fontSize: '10px',
                        colors: ['#6c757d']
                    },
                    formatter: function(value) {
                        return new Intl.NumberFormat('vi-VN', {
                            style: 'currency',
                            currency: 'VND',
                            notation: 'compact'
                        }).format(value);
                    }
                },
                title: {
                    text: 'Doanh thu 7 ngày gần nhất từ bảng dữ liệu',
                    align: 'center',
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold',
                        color: '#495057'
                    }
                },
                subtitle: {
                    text: 'Dữ liệu được cập nhật từ bảng "Doanh thu 7 ngày gần nhất"',
                    align: 'center',
                    style: {
                        fontSize: '11px',
                        color: '#6c757d'
                    }
                }
            };

            // Clear existing chart
            weeklyElement.innerHTML = '';

            try {
                const weeklyChart = new ApexCharts(weeklyElement, weeklyOptions);
                weeklyChart.render();
                console.log('Weekly chart rendered successfully with real data');
            } catch (error) {
                console.error('Error creating weekly chart:', error);
                weeklyElement.innerHTML = `<div style="text-align: center; padding: 50px; color: #dc3545;"><h5>Lỗi tạo biểu đồ</h5><p>${error.message}</p></div>`;
            }
        }

        // ===== Time-based charts =====
        function currencyVND(value) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
        }

        function renderHourlyTimeChart() {
            const el = document.getElementById('hourly-chart');
            if (!el || typeof ApexCharts === 'undefined') return;
            el.innerHTML = '';
            const options = {
                chart: { type: 'bar', height: 350, toolbar: { show: false } },
                series: [{ name: 'Doanh thu', data: @json($hourlyData) }],
                xaxis: { categories: @json($hourlyLabels), title: { text: 'Giờ' } },
                yaxis: { labels: { formatter: v => currencyVND(v) }, title: { text: 'VND' } },
                colors: ['#5d87ff'], dataLabels: { enabled: false }, grid: { borderColor: '#e9ecef' },
                tooltip: { y: { formatter: v => currencyVND(v) } }
            };
            new ApexCharts(el, options).render();
        }

        function renderDailyTimeChart() {
            const el = document.getElementById('daily-chart');
            if (!el || typeof ApexCharts === 'undefined') return;
            el.innerHTML = '';
            const options = {
                chart: { type: 'line', height: 350, toolbar: { show: false } },
                stroke: { curve: 'smooth', width: 3 },
                series: [{ name: 'Doanh thu', data: @json($dailyData) }],
                xaxis: { categories: @json($dailyLabels), title: { text: 'Ngày (30 ngày gần nhất)' } },
                yaxis: { labels: { formatter: v => currencyVND(v) }, title: { text: 'VND' } },
                colors: ['#ff6b6b'], markers: { size: 0 },
                tooltip: { y: { formatter: v => currencyVND(v) } }
            };
            new ApexCharts(el, options).render();
        }

        function renderWeekdayTimeChart() {
            const el = document.getElementById('weekday-chart');
            if (!el || typeof ApexCharts === 'undefined') return;
            el.innerHTML = '';
            const options = {
                chart: { type: 'bar', height: 350, toolbar: { show: false } },
                plotOptions: { bar: { columnWidth: '55%', borderRadius: 6 } },
                series: [{ name: 'Doanh thu', data: @json($weeklyDayData) }],
                xaxis: { categories: @json($weeklyDayLabels), title: { text: 'Thứ' } },
                yaxis: { labels: { formatter: v => currencyVND(v) }, title: { text: 'VND' } },
                colors: ['#49beff'], dataLabels: { enabled: false },
                tooltip: { y: { formatter: v => currencyVND(v) } }
            };
            new ApexCharts(el, options).render();
        }

        function renderMonthlyTimeChart() {
            const el = document.getElementById('monthly-chart');
            if (!el || typeof ApexCharts === 'undefined') return;
            el.innerHTML = '';
            const options = {
                chart: { type: 'area', height: 350, toolbar: { show: false } },
                dataLabels: { enabled: false }, stroke: { curve: 'smooth', width: 2 },
                series: [{ name: 'Doanh thu', data: @json($monthlyData) }],
                xaxis: { categories: @json($monthlyLabels), title: { text: 'Tháng (12 tháng)' } },
                yaxis: { labels: { formatter: v => currencyVND(v) }, title: { text: 'VND' } },
                colors: ['#39b69a'], fill: { type: 'gradient', gradient: { opacityFrom: 0.6, opacityTo: 0.2 } },
                tooltip: { y: { formatter: v => currencyVND(v) } }
            };
            new ApexCharts(el, options).render();
        }

        function renderYearlyTimeChart() {
            const el = document.getElementById('yearly-chart');
            if (!el || typeof ApexCharts === 'undefined') return;
            el.innerHTML = '';
            const options = {
                chart: { type: 'bar', height: 350, toolbar: { show: false } },
                series: [{ name: 'Doanh thu', data: @json($yearlyData) }],
                xaxis: { categories: @json($yearlyLabels), title: { text: 'Năm' } },
                yaxis: { labels: { formatter: v => currencyVND(v) }, title: { text: 'VND' } },
                colors: ['#845ef7'], dataLabels: { enabled: false },
                tooltip: { y: { formatter: v => currencyVND(v) } }
            };
            new ApexCharts(el, options).render();
        }

        // @formatter:on
    </script>
@endpushonce

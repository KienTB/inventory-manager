@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h1 class="display-4 text-primary mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-point" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/>
                            </svg>
                        </h1>
                        <h2 class="mb-3">Chào mừng bạn đến với hệ thống bán hàng!</h2>
                        <p class="text-muted mb-4">Bạn có thể bắt đầu bán hàng ngay bây giờ hoặc xem các đơn hàng đã tạo.</p>

                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('pos.index') }}" class="btn btn-primary btn-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 5l0 14"/>
                                    <path d="M5 12l14 0"/>
                                </svg>
                                Bắt đầu bán hàng
                            </a>

                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 6l11 0"/>
                                    <path d="M9 12l11 0"/>
                                    <path d="M9 18l11 0"/>
                                </svg>
                                Xem đơn hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="h1 mb-2 text-primary">{{ \App\Models\Order::whereDate('created_at', today())->count() }}</div>
                        <div class="text-muted">Đơn hàng hôm nay</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="h1 mb-2 text-success">{{ \App\Models\Order::where('order_status', \App\Enums\OrderStatus::COMPLETE)->whereDate('created_at', today())->count() }}</div>
                        <div class="text-muted">Đơn hàng hoàn thành</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="h1 mb-2 text-info">{{ number_format(\App\Models\Order::whereDate('created_at', today())->sum('total'), 0, ',', '.') }}₫</div>
                        <div class="text-muted">Doanh thu hôm nay</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Đơn hàng gần đây</h3>
                        <div class="card-actions">
                            <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">Xem tất cả</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $recentOrders = \App\Models\Order::with(['customer'])
                                ->latest()
                                ->limit(5)
                                ->get();
                        @endphp

                        @if($recentOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Mã đơn hàng</th>
                                            <th>Khách hàng</th>
                                            <th>Ngày tạo</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentOrders as $order)
                                            <tr>
                                                <td>{{ $order->invoice_no }}</td>
                                                <td>{{ $order->customer?->name ?? 'Khách lẻ' }}</td>
                                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                <td><strong>{{ number_format($order->total, 0, ',', '.') }}₫</strong></td>
                                                <td>
                                                    @if($order->order_status->value === 'complete')
                                                        <span class="badge bg-success">Hoàn thành</span>
                                                    @elseif($order->order_status->value === 'pending')
                                                        <span class="badge bg-warning">Đang xử lý</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $order->order_status->label() }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                                        </svg>
                                                        Xem
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/>
                                        <path d="M12 12l8 -4.5"/>
                                        <path d="M12 12l0 9"/>
                                        <path d="M12 12l-8 -4.5"/>
                                    </svg>
                                </div>
                                <p class="text-muted">Chưa có đơn hàng nào. Hãy bắt đầu bán hàng!</p>
                                <a href="{{ route('pos.index') }}" class="btn btn-primary">Tạo đơn hàng đầu tiên</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

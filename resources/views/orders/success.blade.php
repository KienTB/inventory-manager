@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <x-alert/>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card border-success">
                    <div class="card-header bg-success text-white text-center">
                        <h3 class="card-title mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check-circle" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                                <path d="M9 12l2 2l4 -4"/>
                            </svg>
                            Thanh toán thành công!
                        </h3>
                    </div>

                    <div class="card-body text-center">
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-muted">Hóa đơn số</div>
                                    <div class="h4 text-primary">{{ $order->invoice_no }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted">Tổng tiền</div>
                                    <div class="h3 text-success">{{ format_currency($order->total) }}</div>
                                </div>
                            </div>
                        </div>

                        @if($order->customer)
                        <div class="mb-4">
                            <div class="text-muted">Khách hàng</div>
                            <div class="h5">{{ $order->customer->name }}</div>
                        </div>
                        @endif

                        <div class="mb-4">
                            <div class="text-muted">Thời gian thanh toán</div>
                            <div class="h6">{{ $order->order_date->format('d/m/Y H:i:s') }}</div>
                        </div>

                        <div class="mb-4">
                            <div class="text-muted">Sản phẩm đã mua</div>
                            <div class="h6">{{ $order->details->sum('quantity') }} sản phẩm</div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                    <a href="{{ route('pos.index') }}" class="btn btn-primary btn-lg me-md-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                            <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                            <path d="M17 17h-11v-14h-2"/>
                                            <path d="M6 2l11 0l1 14h2"/>
                                        </svg>
                                        Tiếp tục bán hàng
                                    </a>

                                    <a href="{{ route('order.downloadInvoice', $order->id) }}" class="btn btn-outline-success btn-lg" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/>
                                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/>
                                            <path d="M7 13h8v4h-8z"/>
                                        </svg>
                                        In hóa đơn
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <div class="text-muted small">
                                <p>✅ Đơn hàng đã được lưu vào hệ thống</p>
                                <p>✅ Tồn kho đã được cập nhật tự động</p>
                                <p>✅ Khách hàng có thể xem hóa đơn bất kỳ lúc nào</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Thao tác nhanh</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary w-100 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                        <path d="M21 12c-1 0 -3 -3 -9 -3s-8 3 -9 3"/>
                                    </svg>
                                    Xem chi tiết
                                </a>
                            </div>
                            <div class="col-md-4 text-center">
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 6l11 0"/>
                                        <path d="M9 12l11 0"/>
                                        <path d="M9 18l11 0"/>
                                        <path d="M5 6l0 0.01"/>
                                        <path d="M5 12l0 0.01"/>
                                        <path d="M5 18l0 0.01"/>
                                    </svg>
                                    Danh sách đơn hàng
                                </a>
                            </div>
                            <div class="col-md-4 text-center">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-info w-100 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z"/>
                                        <path d="M9 8m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z"/>
                                        <path d="M15 4m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z"/>
                                        <path d="M21 12m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z"/>
                                    </svg>
                                    Về dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card.border-success {
    border-width: 2px !important;
}

.card-header.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.btn-lg {
    padding: 0.75rem 2rem;
    font-size: 1.1rem;
}

.pos-quick-amounts .btn {
    min-width: 80px;
}

@media (max-width: 768px) {
    .btn-lg {
        width: 100%;
        margin-bottom: 0.5rem;
    }

    .d-md-flex {
        flex-direction: column !important;
    }

    .me-md-2 {
        margin-right: 0 !important;
    }
}
</style>
@endsection

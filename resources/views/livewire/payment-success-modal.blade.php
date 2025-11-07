<div wire:key="payment-success-modal-main">
    <!-- Modal - luôn render để tránh snapshot missing -->
    <div class="modal fade {{ $show ? 'show' : '' }}" 
         style="display: {{ $show ? 'block' : 'none' }}; {{ $show ? '' : 'pointer-events: none;' }}" 
         tabindex="-1" 
         role="dialog">
        <div class="modal-backdrop fade {{ $show ? 'show' : '' }}" 
             style="{{ $show ? 'opacity: 0.5;' : 'opacity: 0; pointer-events: none;' }}"
             wire:click="close"></div>
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-success">
                <!-- Success Header -->
                <div class="modal-header bg-success text-white">
                    <h4 class="modal-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check-circle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                            <path d="M9 12l2 2l4 -4"/>
                        </svg>
                        Thanh toán thành công!
                    </h4>
                    <button type="button" class="btn-close btn-close-white" wire:click="close" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if($order)
                    <!-- Order Summary -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6 text-center mb-3 mb-md-0">
                                <div class="text-muted mb-2">Hóa đơn số</div>
                                <div class="h4 text-primary fw-bold">{{ $order->invoice_no }}</div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="text-muted mb-2">Tổng tiền</div>
                                <div class="h3 text-success fw-bold">{{ format_currency($order->total) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    @if($order->customer)
                    <div class="mb-3 text-center">
                        <div class="text-muted mb-1">Khách hàng</div>
                        <div class="h5">{{ $order->customer->name }}</div>
                    </div>
                    @endif

                    <!-- Order Date -->
                    <div class="mb-3 text-center">
                        <div class="text-muted mb-1">Thời gian thanh toán</div>
                        <div class="h6">{{ $order->order_date->format('d/m/Y H:i:s') }}</div>
                    </div>

                    <!-- Order Items Summary -->
                    <div class="mb-4 text-center">
                        <div class="text-muted mb-1">Sản phẩm đã mua</div>
                        <div class="h6">{{ $order->details->sum('quantity') }} sản phẩm</div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <!-- Tiếp tục bán hàng button -->
                        <button type="button" class="btn btn-primary btn-lg me-md-2" wire:click="continueSelling">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                <path d="M17 17h-11v-14h-2"/>
                                <path d="M6 2l11 0l1 14h2"/>
                            </svg>
                            Tiếp tục bán hàng
                        </button>

                        <!-- In hóa đơn button -->
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

                    <!-- Additional Info -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="text-muted small text-center">
                            <p class="mb-1">✅ Đơn hàng đã được lưu vào hệ thống</p>
                            <p class="mb-1">✅ Tồn kho đã được cập nhật tự động</p>
                            <p class="mb-0">✅ Khách hàng có thể xem hóa đơn bất kỳ lúc nào</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modal.show {
    display: block !important;
}
.modal-backdrop.show {
    opacity: 0.5;
    pointer-events: auto;
}
</style>

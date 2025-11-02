<div>
    <div class="card pos-panel" wire:poll.750ms>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <div class="text-muted">Tổng tiền hàng</div>
                    <div class="h4 mb-0">{{ format_currency($this->subtotal) }}</div>
                </div>
                <div class="text-end">
                    <div class="text-muted">Khách cần trả</div>
                    <div class="h3 text-primary mb-0">{{ format_currency($this->total) }}</div>
                </div>
            </div>

            <div class="mt-2">
                <label class="form-label">Giảm giá</label>
                <div class="input-group">
                    <input type="number" min="0" step="1000" class="form-control" wire:model.live.debounce.300ms="discount" placeholder="Nhập số tiền giảm">
                    <span class="input-group-text">₫</span>
                </div>
            </div>

            <hr/>
            <div class="mb-2">Phương thức thanh toán</div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="pm-cash" value="cash" wire:model="payment_method">
                <label class="form-check-label" for="pm-cash">Tiền mặt</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="pm-bank" value="bank" wire:model="payment_method">
                <label class="form-check-label" for="pm-bank">Chuyển khoản</label>
            </div>

            <div class="mt-3">
                <div class="mb-2">Khách thanh toán</div>
                <input type="number" step="1000" min="0" class="form-control" wire:model.live="received" placeholder="Nhập số tiền khách đưa">
                <div class="mt-2 d-flex flex-wrap gap-2 pos-quick-amounts">
                    <button type="button" class="btn btn-outline-secondary" wire:click="quickFill(50000)">50,000</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="quickFill(100000)">100,000</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="quickFill(200000)">200,000</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="quickFill(500000)">500,000</button>
                </div>
            </div>

            @if($this->payment_method === 'bank')
                <div class="mt-3 text-center">
                    <div class="mb-2">Quét mã để thanh toán</div>
                    @if ($this->vietQrUrl)
                        <img src="{{ $this->vietQrUrl }}" alt="VietQR" style="max-width: 220px;"/>
                    @else
                        <div class="text-muted small">Vui lòng cập nhật ngân hàng và số tài khoản trong trang hồ sơ.</div>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('pos.checkout') }}" class="mt-3" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Đang xử lý...';">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $customer_id }}" />
                <input type="hidden" name="cart_instance" value="{{ $cartInstance }}" />
                <input type="hidden" name="payment_type" :value="$wire.payment_method === 'bank' ? 'BankTransfer' : 'HandCash'" />
                <input type="hidden" name="pay" value="{{ $this->received > 0 ? $this->received : $this->total }}" />
                <button class="btn btn-primary w-100" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-credit-card" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z"/>
                        <path d="M8 9l4 4l4 -4"/>
                    </svg>
                    THANH TOÁN
                </button>
            </form>
        </div>
    </div>
</div>

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

            <div class="mt-3" x-data="{ amount: @entangle('received') }">
                <div class="mb-2">Khách thanh toán</div>
                <input type="number" step="1000" min="0" class="form-control" x-model.number="amount" placeholder="Nhập số tiền khách đưa">
                <div class="mt-2 d-flex flex-wrap gap-2 pos-quick-amounts">
                    <button type="button" class="btn btn-outline-secondary" wire:click="quickFill(50000)">50,000</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="quickFill(100000)">100,000</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="quickFill(200000)">200,000</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="quickFill(500000)">500,000</button>
                </div>
            </div>

            <template x-if="$wire.payment_method === 'bank'">
                <div class="mt-3 text-center">
                    <div class="mb-2">Quét mã để thanh toán</div>
                    @if ($this->vietQrUrl)
                        <img src="{{ $this->vietQrUrl }}" alt="VietQR" style="max-width: 220px;"/>
                    @else
                        <div class="text-muted small">Vui lòng cập nhật ngân hàng và số tài khoản trong trang hồ sơ.</div>
                    @endif
                </div>
            </template>

            <form method="POST" action="{{ route('pos.checkout') }}" class="mt-3">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $customer_id }}" />
                <input type="hidden" name="cart_instance" value="{{ $cartInstance }}" />
                <input type="hidden" name="payment_type" :value="$wire.payment_method === 'bank' ? 'BankTransfer' : 'HandCash'" />
                <input type="hidden" name="pay" value="{{ $received ?? $this->total }}" />
                <button class="btn btn-primary w-100" type="submit">THANH TOÁN</button>
            </form>
        </div>
    </div>
</div>

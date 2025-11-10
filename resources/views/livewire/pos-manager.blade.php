<div>
    
    <!-- Tab Navigation -->
    <div class="card mb-3" wire:poll.10s="refreshTabTotals">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-success me-3" wire:click="createNewTab">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14"/>
                        <path d="M5 12l14 0"/>
                    </svg>
                    Tạo hóa đơn mới
                </button>
                <h4 class="card-title mb-0">Hóa đơn đang mở</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="nav nav-tabs nav-fill">
                @foreach($tabs as $index => $tab)
                    <a class="nav-link {{ $activeTab === $tab ? 'active' : '' }}"
                       href="#"
                       wire:click.prevent="switchToTab('{{ $tab }}')">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Hóa đơn {{ $index + 1 }}</span>
                            @if(isset($tabTotals[$tab]) && $tabTotals[$tab]['itemCount'] > 0)
                                <span class="badge bg-primary ms-2">
                                    {{ $tabTotals[$tab]['itemCount'] }}sp - {{ format_currency($tabTotals[$tab]['total']) }}
                                </span>
                            @endif
                        </div>
                        @if(count($tabs) > 1)
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger ms-2"
                                    wire:click.prevent="closeTab('{{ $tab }}')"
                                    title="Đóng tab">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M18 6l-12 12"/>
                                    <path d="M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Active Invoice Content -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <livewire:search-product :active-cart-instance="$this->activeCartInstance" wire:key="search-product-component" />
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <livewire:product-cart :cart-instance="$this->activeCartInstance" wire:key="product-cart-component" />
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <livewire:checkout-panel :cart-instance="$this->activeCartInstance" wire:key="checkout-panel-component" />
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Success Modal - render trực tiếp trong PosManager -->
    @include('livewire.includes.payment-success-modal', [
        'show' => $showPaymentSuccessModal ?? false,
        'order' => $successOrder ?? null
    ])
</div>

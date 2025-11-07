<div>
    <!-- Danh sách hóa đơn chưa thanh toán -->
    @if(isset($allPendingInvoices) && !empty($allPendingInvoices))
    <div class="card mb-3">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-invoice" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                    <path d="M9 7l1 0"/>
                    <path d="M9 13l6 0"/>
                    <path d="M13 17l2 0"/>
                </svg>
                Danh sách hóa đơn chưa thanh toán 
                <span class="badge bg-primary">{{ count($allPendingInvoices) }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach($allPendingInvoices as $invoice)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card border {{ $invoice['isActive'] ? 'border-primary shadow-sm' : ($invoice['isInTabs'] ? 'border-info' : '') }} h-100" 
                             style="cursor: pointer; transition: all 0.2s;"
                             wire:click="switchToTab('{{ $invoice['tab'] }}')"
                             onmouseover="this.style.transform='translateY(-2px)'" 
                             onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0 fw-bold {{ $invoice['isActive'] ? 'text-primary' : '' }}">
                                            Hóa đơn {{ $invoice['number'] }}
                                        </h6>
                                        <small class="text-muted">{{ $invoice['tab'] }}</small>
                                    </div>
                                    <div>
                                        @if($invoice['isActive'])
                                            <span class="badge bg-primary">Đang mở</span>
                                        @elseif($invoice['isInTabs'])
                                            <span class="badge bg-info">Đã mở</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Sản phẩm:</small>
                                        <strong>{{ $invoice['itemCount'] }} sp</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Tổng tiền:</small>
                                        <strong class="text-success">{{ format_currency($invoice['total']) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

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

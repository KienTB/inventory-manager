<div>
    <div>
        @if (session()->has('message'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="alert-body">
                    <span>{{ session('message') }}</span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            </div>
        @endif

        <div class="table-responsive position-relative">
            <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center" style="top:0;right:0;left:0;bottom:0;background-color: rgba(255,255,255,0.5);z-index: 99;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Đang tải...</span>
                </div>
            </div>
            <table class="table table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th class="align-middle">Sản phẩm</th>
                    <th class="align-middle text-center"style="width: 250px; white-space: nowrap;">Đơn giá</th>
                    <th class="align-middle text-center">Tồn kho</th>
                    <th class="align-middle text-center" style="width: 150px; white-space: nowrap;">Số lượng</th>
                    <th class="align-middle text-center">Thuế</th>
                    <th class="align-middle text-center">Tạm tính</th>
                    <th class="align-middle text-center" style="width: 80px; white-space: nowrap;">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                    @if($cart_items->isNotEmpty())
                        @foreach($cart_items as $cart_item)
                            <tr>
                                <td class="align-middle">
                                    {{ $cart_item->name }} <br>
                                    <span class="badge badge-success">
                                        {{ $cart_item->options->code }}
                                    </span>
                                    @include('livewire.includes.product-cart-modal')
                                </td>

                                <td class="align-middle text-center" x-data="{
                                    open: false,
                                    toggle() {
                                        this.open = !this.open;
                                    }
                                }">
                                    <span x-show="!open" @click="toggle()" style="cursor: pointer;">
                                        {{ format_currency($cart_item->price) }}
                                    </span>

                                    <div x-show="open" @click.away="open = false">
                                        @include('livewire.includes.product-cart-price')
                                    </div>
                                </td>

                                <td class="align-middle text-center">
                                    <span class="badge badge-info">
                                        {{ $cart_item->options->stock . ' ' . $cart_item->options->unit }}
                                    </span>
                                </td>

                                <td class="align-middle text-center" style="white-space: nowrap; width: 120px;">
                                    @include('livewire.includes.product-cart-quantity')
                                </td>

                                <td class="align-middle text-center">
                                    {{ format_currency($cart_item->options->product_tax) }}
                                </td>

                                <td class="align-middle text-center">
                                    {{ format_currency($cart_item->options->sub_total) }}
                                </td>

                                <td class="align-middle text-center">
{{--                                    <a href="#" wire:click.prevent="removeItem('{{ $cart_item->rowId }}')">--}}
{{--                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>--}}
{{--                                        Del--}}
{{--                                    </a>--}}

                                    <a href="#" wire:click.prevent="removeItem('{{ $cart_item->rowId }}')" class="btn btn-icon btn-outline-danger btn-sm" title="Xóa">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">
                        <span class="text-danger">
                            Vui lòng tìm và chọn sản phẩm!
                        </span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

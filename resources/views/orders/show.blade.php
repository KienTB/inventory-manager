@extends('layouts.tabler')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">
                            {{ __('Chi tiết đơn hàng') }}
                        </h3>
                    </div>

                    <div class="card-actions btn-actions">
                        <div class="dropdown">
                            <a href="#" class="btn-action dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                    <path d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                    <path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                </svg>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end">
                                @if ($order->order_status === \App\Enums\OrderStatus::PENDING)
                                    <form action="{{ route('orders.update', $order) }}" method="POST">
                                        @csrf
                                        @method('put')

                                        <button type="submit" class="dropdown-item text-success"
                                                onclick="return confirm('Bạn có chắc chắn muốn duyệt đơn hàng này không?')"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>

                                            {{ __('Duyệt đơn hàng') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <x-action.close route="{{ route('orders.index') }}"/>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row row-cards mb-3">
                        <div class="col">
                            <label for="order_date" class="form-label required">
                                {{ __('Ngày đặt hàng') }}
                            </label>
                            <input type="text"
                                   id="order_date"
                                   class="form-control"
                                   value="{{ $order->order_date->format('d-m-Y') }}"
                                   disabled
                            >
                        </div>

                        <div class="col">
                            <label for="invoice_no" class="form-label required">
                                {{ __('Mã hóa đơn') }}
                            </label>
                            <input type="text"
                                   id="invoice_no"
                                   class="form-control"
                                   value="{{ $order->invoice_no }}"
                                   disabled
                            >
                        </div>

                        <div class="col">
                            <label for="customer" class="form-label required">
                                {{ __('Khách hàng') }}
                            </label>
                            <input type="text"
                                   id="customer"
                                   class="form-control"
                                   value="{{ $order->customer?->name ?? 'Không có dữ liệu' }}"
                                   disabled
                            >
                        </div>

                        <div class="col">
                            <label for="payment_type" class="form-label required">
                                {{ __('Hình thức thanh toán') }}
                            </label>

                            <input type="text" id="payment_type" class="form-control" value="{{ $order->payment_type }}" disabled>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col" class="align-middle text-center">STT</th>
                                <th scope="col" class="align-middle text-center">Hình ảnh</th>
                                <th scope="col" class="align-middle text-center">Tên sản phẩm</th>
                                <th scope="col" class="align-middle text-center">Mã sản phẩm</th>
                                <th scope="col" class="align-middle text-center">Số lượng</th>
                                <th scope="col" class="align-middle text-center">Đơn giá</th>
                                <th scope="col" class="align-middle text-center">Thành tiền</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($order->details as $item)
                                <tr>
                                    <td class="align-middle text-center">
                                        {{ $loop->iteration  }}
                                    </td>
                                    <td class="align-middle text-center">
                                        <div style="max-height: 80px; max-width: 80px;">
                                            <img class="img-fluid"  src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/img/products/default.webp') }}">
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ $item->product->name }}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ $item->product->code }}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ number_format($item->unitcost, 2) }}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ number_format($item->total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="6" class="text-end">
                                    Số tiền đã thanh toán
                                </td>
                                <td class="text-center">{{ number_format($order->pay, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end">Còn nợ</td>
                                <td class="text-center">{{ number_format($order->due, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end">Thuế VAT</td>
                                <td class="text-center">{{ number_format($order->vat, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end">Tổng cộng</td>
                                <td class="text-center">{{ number_format($order->total, 2) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    @if ($order->order_status === \App\Enums\OrderStatus::PENDING)
                        <form action="{{ route('orders.update', $order) }}" method="POST">
                            @method('put')
                            @csrf

                            <button type="submit"
                                    class="btn btn-success"
                                    onclick="return confirm('Bạn có chắc chắn muốn hoàn tất đơn hàng này không?')"
                            >
                                {{ __('Hoàn tất đơn hàng') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

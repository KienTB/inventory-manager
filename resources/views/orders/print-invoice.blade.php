<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            {{ config('app.name') }}
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <!-- External CSS libraries -->
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/bootstrap.min.css') }}">
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/fonts/font-awesome/css/font-awesome.min.css') }}">
        <!-- Google fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <!-- Custom Stylesheet -->
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/style.css') }}">
        <script>
            // Tự động in khi trang tải xong
            window.onload = function() {
                // Chờ một chút để đảm bảo tất cả nội dung đã tải xong
                setTimeout(function() {
                    window.print();
                    // Đóng tab sau khi in (tùy chọn)
                    // window.onafterprint = function() {
                    //     window.close();
                    // };
                }, 500);
            };
        </script>
    </head>
    <body>
        <div class="invoice-16 invoice-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="invoice-inner-9" id="invoice_wrapper">
                            <div class="invoice-top">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="logo">
                                            <h1>Cửa hàng tạp hóa ABC</h1>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="invoice">
                                            <h1>
                                                Hóa đơn # <span>{{ $order->invoice_no }}</span>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-info">
                                <div class="row">
                                    <div class="col-sm-6 mb-50">
                                        <div class="invoice-number">
                                            <h4 class="inv-title-1">
                                                Ngày hóa đơn:
                                            </h4>
                                            <p class="invo-addr-1">
                                                {{ $order->order_date }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 text-end mb-50">
                                        <h4 class="inv-title-1">Cửa hàng</h4>
                                        <p class="inv-from-1">{{ $settings->store_name ?? 'Cửa hàng tạp hóa' }}</p>
                                        <p class="inv-from-1">{{ $settings->store_phone ?? 'Chưa cập nhật' }}</p>
                                        <p class="inv-from-1">{{ $settings->store_email ?? 'Chưa cập nhật' }}</p>
                                        <p class="inv-from-2">{{ $settings->store_address ?? 'Chưa cập nhật' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="order-summary">
                                <div class="table-outer">
                                    <table class="default-table invoice-table">
                                        <thead>
                                            <tr>
                                                <th class="align-middle">Sản phẩm</th>
                                                <th class="align-middle text-center">Đơn vị</th>
                                                <th class="align-middle text-center">Đơn giá</th>
                                                <th class="align-middle text-center">Số lượng</th>
                                                <th class="align-middle text-center">Thành tiền</th>
                                            </tr>
                                        </thead>

                                        <tbody>
{{--                                            @foreach ($orderDetails as $item)--}}
                                            @foreach ($order->details as $item)
                                            <tr>
                                                <td class="align-middle">
                                                    {{ $item->product->invoice_name ?? $item->product->name }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{ $item->product->unit->short_code ?? 'cái' }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{ format_currency($item->unitcost) }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{ format_currency($item->total) }}
                                                </td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td colspan="4" class="text-end">
                                                    <strong>
                                                        Tạm tính
                                                    </strong>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <strong>
                                                        {{ format_currency($order->sub_total) }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">
                                                    <strong>Thuế VAT</strong>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <strong>
                                                        {{ format_currency($order->vat) }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">
                                                    <strong>Tổng cộng</strong>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <strong>
                                                        {{ format_currency($order->total) }}
                                                    </strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Thông tin thanh toán -->
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="payment-info">
                                        <h5 class="text-muted mb-2">Thông tin thanh toán:</h5>
                                        <p class="mb-1"><strong>Phương thức:</strong>
                                            @if($order->payment_type == 'HandCash')
                                                Tiền mặt
                                            @elseif($order->payment_type == 'BankTransfer')
                                                Chuyển khoản
                                            @else
                                                {{ $order->payment_type }}
                                            @endif
                                        </p>
                                        @if($order->pay)
                                            <p class="mb-1"><strong>Khách trả:</strong> {{ format_currency($order->pay) }}</p>
                                        @endif
                                        @if($order->due > 0)
                                            <p class="mb-1 text-danger"><strong>Còn nợ:</strong> {{ format_currency($order->due) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6 text-end">
                                    <div class="thank-you">
                                        <p class="mb-2"><em>Cảm ơn quý khách đã mua hàng!</em></p>
                                        <p class="mb-1"><small>Hàng hóa đã mua không được đổi trả</small></p>
                                        <p class="mb-0"><small>Xuất hóa đơn: {{ date('d/m/Y H:i') }}</small></p>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-informeshon-footer mt-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Website:</strong> <a href="#">{{ $settings->store_website ?? 'Chưa cập nhật' }}</a></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Email:</strong> <a href="mailto:{{ $settings->store_email ?? '#' }}">{{ $settings->store_email ?? 'Chưa cập nhật' }}</a></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Hotline:</strong> <a href="tel:{{ $settings->store_phone ?? '#' }}">{{ $settings->store_phone ?? 'Chưa cập nhật' }}</a></p>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12 text-center">
                                        <small class="text-muted">{{ $settings->store_name ?? 'Cửa hàng tạp hóa' }} - {{ $settings->store_slogan ?? 'Phục vụ 24/7' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-btn-section clearfix d-print-none">
                            <a href="javascript:window.print()" class="btn btn-lg btn-print">
                                <i class="fa fa-print"></i>
                                In hóa đơn
                            </a>
                            <a id="invoice_download_btn" class="btn btn-lg btn-download">
                                <i class="fa fa-download"></i>
                                Tải PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('assets/invoice/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/jspdf.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/html2canvas.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/app.js') }}"></script>

        <!-- Auto print when page loads -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Wait a moment for the page to fully load
                setTimeout(function() {
                    window.print();
                }, 500);
            });
        </script>
    </body>
</html>

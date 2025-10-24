@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl mb-3">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    Chi tiết sản phẩm
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $product])
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            {{-- Hình ảnh và Thông tin sản phẩm --}}
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">
                                Hình ảnh sản phẩm
                            </h3>

                            <img class="img-account-profile mb-2" src="{{ asset('assets/img/products/default.webp') }}" alt="" id="image-preview" />
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                Thông tin sản phẩm
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                                <tbody>
                                    <tr>
                                        <td class="w-25 fw-bold">Tên sản phẩm</td>
                                        <td>{{ $product->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Slug</td>
                                        <td>{{ $product->slug }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Mã sản phẩm</td>
                                        <td>{{ $product->code }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Mã hàng</td>
                                        <td>{{ $product->product_code ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Mã vạch</td>
                                        <td>{!! $barcode !!}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Danh mục</td>
                                        <td>
                                            <a href="{{ route('categories.show', $product->category) }}" class="badge bg-blue-lt">
                                                {{ $product->category->name }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Thương hiệu</td>
                                        <td>{{ $product->brand ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Vị trí</td>
                                        <td>{{ $product->location ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Đơn vị</td>
                                        <td>
                                            <a href="{{ route('units.show', $product->unit) }}" class="badge bg-blue-lt">
                                                {{ $product->unit->short_code }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Số lượng</td>
                                        <td>{{ $product->quantity }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Cảnh báo tồn kho</td>
                                        <td>
                                            <span class="badge bg-red-lt">
                                                {{ $product->quantity_alert }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Trọng lượng</td>
                                        <td>{{ $product->weight ? $product->weight . ' kg' : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Giá mua</td>
                                        <td>{{ number_format($product->buying_price, 0, ',', '.') }} đ</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Giá bán</td>
                                        <td>{{ number_format($product->selling_price, 0, ',', '.') }} đ</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Hoa hồng nhân viên</td>
                                        <td>
                                            <span class="badge bg-green-lt">
                                                {{ $product->commission ?? 0 }}%
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Thuế</td>
                                        <td>
                                            <span class="badge bg-red-lt">
                                                {{ $product->tax }} %
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Loại thuế</td>
                                        <td>{{ $product->tax_type?->label() ?? 'Chưa xác định' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Ghi chú</td>
                                        <td>{{ $product->notes ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer text-end">
                            <x-button.edit route="{{ route('products.edit', $product) }}">
                            </x-button.edit>

                            <x-button.back route="{{ route('products.index') }}">
                            </x-button.back>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Đơn vị tính (Cơ bản & Phụ) - Cải tiến giao diện --}}
            @if($product->productUnits && count($product->productUnits) > 0)
                <div class="col-12 mt-4">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">
                                Đơn vị tính
                            </h3>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="w-15">Loại</th>
                                        <th class="w-25">Đơn vị</th>
                                        <th class="w-20 text-center">Tỷ lệ quy đổi</th>
                                        <th class="w-20">Mã vạch</th>
                                        <th class="w-20 text-end">Giá bán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->productUnits->sortByDesc('is_base_unit') as $unit)
                                        <tr>
                                            <td>
                                                @if($unit->is_base_unit)
                                                    <span class="badge bg-green-lt">Cơ bản</span>
                                                @else
                                                    <span class="badge bg-blue-lt">Phụ</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-azure-lt">
                                                    {{ $unit->unit->name }} ({{ $unit->unit->short_code }})
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-semibold">{{ $unit->conversion_rate }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $unit->barcode ?? '-' }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-dark">{{ number_format($unit->selling_price / 100, 0, ',', '.') }} đ</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
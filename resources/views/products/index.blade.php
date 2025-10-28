@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        @if($products->isEmpty() && !request()->has('search') && !request()->has('category') && !request()->has('brand') && !request()->has('price_min'))
            <x-empty
                title="No products found"
                message="Try adjusting your search or filter to find what you're looking for."
                button_label="{{ __('Add your first Product') }}"
                button_route="{{ route('products.create') }}"
            />
        @else
            <x-alert/>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex align-items-center gap-3">
                        <div style="min-width: 220px;">
                            <h2 class="page-title mb-0"></h2>
                        </div>
                        <div class="flex-fill">
                            <div class="card">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="flex-fill" style="max-width: 400px;">
                                            <form action="{{ route('products.index') }}" method="GET" class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                        <path d="M21 21l-6 -6" />
                                                    </svg>
                                                </span>
                                                <input type="text" 
                                                    name="search" 
                                                    class="form-control border-start-0 ps-0" 
                                                    placeholder="Theo mã, tên hàng..."
                                                    value="{{ request('search') }}">
                                                @if(request()->has('category'))
                                                    <input type="hidden" name="category" value="{{ request('category') }}">
                                                @endif
                                                @if(request()->has('brand'))
                                                    <input type="hidden" name="brand" value="{{ request('brand') }}">
                                                @endif
                                            </form>
                                        </div>

                                        <div class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M12 5l0 14" />
                                                    <path d="M5 12l14 0" />
                                                </svg>
                                                Tạo mới
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('products.create') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M12 5l0 14" />
                                                        <path d="M5 12l14 0" />
                                                    </svg>
                                                    Thêm sản phẩm
                                                </a>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                <path d="M7 9l5 5l5 -5" />
                                                <path d="M12 4l0 10" />
                                            </svg>
                                            Import file
                                        </button>

                                        <form action="{{ route('products.export') }}" method="GET" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="search" value="{{ request('search') }}">
                                            <input type="hidden" name="category" value="{{ request('category') }}">
                                            <input type="hidden" name="brand" value="{{ request('brand') }}">
                                            <input type="hidden" name="price_min" value="{{ request('price_min') }}">
                                            <input type="hidden" name="price_max" value="{{ request('price_max') }}">
                                            
                                            <button type="submit" class="btn btn-outline-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                    <path d="M7 11l5 -5l5 5" />
                                                    <path d="M12 4l0 12" />
                                                </svg>
                                                Xuất file
                                            </button>
                                        </form>

                                        <div class="ms-auto d-flex gap-2">
                                            <div class="dropdown">
                                                <button type="button" 
                                                        class="btn btn-icon btn-outline-secondary" 
                                                        data-bs-toggle="dropdown" 
                                                        data-bs-auto-close="outside"
                                                        title="Ẩn/hiện cột">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                                                        <path d="M9 5v14" />
                                                        <path d="M15 5v14" />
                                                    </svg>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 320px;">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-image" data-column="image" checked>
                                                                <label class="form-check-label" for="col-image">Hình ảnh</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-code" data-column="code" checked>
                                                                <label class="form-check-label" for="col-code">Mã hàng</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-name" data-column="name" checked disabled>
                                                                <label class="form-check-label" for="col-name">Tên hàng</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-selling-price" data-column="selling-price" checked>
                                                                <label class="form-check-label" for="col-selling-price">Giá bán</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-buying-price" data-column="buying-price" checked>
                                                                <label class="form-check-label" for="col-buying-price">Giá vốn</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-quantity" data-column="quantity" checked>
                                                                <label class="form-check-label" for="col-quantity">Tồn kho</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-category" data-column="category">
                                                                <label class="form-check-label" for="col-category">Danh mục</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-brand" data-column="brand">
                                                                <label class="form-check-label" for="col-brand">Thương hiệu</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-location" data-column="location">
                                                                <label class="form-check-label" for="col-location">Vị trí</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-weight" data-column="weight">
                                                                <label class="form-check-label" for="col-weight">Trọng lượng</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-orders" data-column="orders" checked>
                                                                <label class="form-check-label" for="col-orders">Khách đặt</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input column-toggle" type="checkbox" id="col-created" data-column="created" checked>
                                                                <label class="form-check-label" for="col-created">Thời gian tạo</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="button" class="btn btn-icon btn-outline-secondary" title="Thiết lập">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                </svg>
                                            </button>

                                            <button type="button" class="btn btn-icon btn-outline-secondary" title="Trợ giúp">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                    <path d="M12 17l0 .01" />
                                                    <path d="M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 col-md-4 mb-3">
                    <div class="card sticky-top" style="top: 1rem;">
                        <div class="card-body">
                            <form action="{{ route('products.index') }}" method="GET" id="filter-sidebar-form">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <h3 class="card-title mb-3">Bộ lọc</h3>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Danh mục</label>
                                    <div class="input-group">
                                        <select name="category" class="form-select" onchange="this.form.submit()">
                                            <option value="">Chọn danh mục</option>
                                            @foreach($categories ?? [] as $cat)
                                                <option value="{{ $cat->id }}" @selected($cat->id == request('category'))>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-icon" title="Tạo mới">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 5l0 14" />
                                                <path d="M5 12l14 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tồn kho</label>
                                    <select name="stock_status" class="form-select" onchange="this.form.submit()">
                                        <option value="">Tất cả</option>
                                        <option value="in_stock" @selected(request('stock_status') == 'in_stock')>Còn hàng</option>
                                        <option value="out_of_stock" @selected(request('stock_status') == 'out_of_stock')>Hết hàng</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Dự kiến hết hàng</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="stock_forecast" id="stock_all_time" value="all" checked>
                                        <label class="form-check-label" for="stock_all_time">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                <path d="M12 7l0 5l3 3" />
                                            </svg>
                                            Toàn thời gian
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="stock_forecast" id="stock_custom" value="custom">
                                        <label class="form-check-label" for="stock_custom">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                <path d="M16 3v4" />
                                                <path d="M8 3v4" />
                                                <path d="M4 11h16" />
                                            </svg>
                                            Tùy chỉnh
                                        </label>
                                    </div>
                                    <div id="stock_custom_date" class="mt-2" style="display: none;">
                                        <input type="date" class="form-control form-control-sm" name="stock_forecast_date">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Thời gian tạo</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="created_time" id="created_all_time" value="all" checked>
                                        <label class="form-check-label" for="created_all_time">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                <path d="M12 7l0 5l3 3" />
                                            </svg>
                                            Toàn thời gian
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="created_time" id="created_custom" value="custom">
                                        <label class="form-check-label" for="created_custom">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                <path d="M16 3v4" />
                                                <path d="M8 3v4" />
                                                <path d="M4 11h16" />
                                            </svg>
                                            Tùy chỉnh
                                        </label>
                                    </div>
                                    <div id="created_custom_date" class="mt-2" style="display: none;">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="date" class="form-control form-control-sm" name="created_from" placeholder="Từ ngày">
                                            </div>
                                            <div class="col-6">
                                                <input type="date" class="form-control form-control-sm" name="created_to" placeholder="Đến ngày">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Thương hiệu</label>
                                    <input type="text" 
                                           name="brand" 
                                           class="form-control" 
                                           placeholder="Nhập thương hiệu..."
                                           value="{{ request('brand') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Vị trí</label>
                                    <select name="location" class="form-select" onchange="this.form.submit()">
                                        <option value="">Chọn vị trí</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Khoảng giá</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="number" 
                                                   name="price_min" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Từ"
                                                   value="{{ request('price_min') }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" 
                                                   name="price_max" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Đến"
                                                   value="{{ request('price_max') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5.5 5h13a1 1 0 0 1 .5 1.5l-5 5.5l0 7l-4 -3l0 -4l-5 -5.5a1 1 0 0 1 .5 -1.5" />
                                        </svg>
                                        Lọc
                                    </button>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                        </svg>
                                        Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-10 col-md-8">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div class="text-muted">
                                    <small>Tổng: <strong>{{ $products->total() }}</strong> sản phẩm</small>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-hover" id="products-table">
                                <thead>
                                    <tr>
                                        <th class="w-1">
                                            <input class="form-check-input m-0" type="checkbox" id="select-all">
                                        </th>
                                        <th class="w-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                            </svg>
                                        </th>
                                        <th style="width: 80px;" data-column="image">Hình</th>
                                        <th data-column="code">Mã vạch</th>
                                        <th data-column="name">Tên sản phẩm</th>
                                        <th data-column="selling-price">Giá bán</th>
                                        <th data-column="buying-price">Giá vốn</th>
                                        <th data-column="quantity">Tồn kho</th>
                                        <th data-column="category" style="display: none;">Danh mục</th>
                                        <th data-column="brand" style="display: none;">Thương hiệu</th>
                                        <th data-column="location" style="display: none;">Vị trí</th>
                                        <th data-column="weight" style="display: none;">Trọng lượng</th>
                                        <th data-column="orders">Khách đặt</th>
                                        <th data-column="created">Thời gian tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr class="cursor-pointer product-row" data-product-id="{{ $product->id }}">
                                            <td>
                                                <input class="form-check-input m-0" type="checkbox" name="product_ids[]" value="{{ $product->id }}" onclick="event.stopPropagation()">
                                            </td>
                                            <td>
                                                <a href="#" class="text-muted" onclick="event.stopPropagation()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                                    </svg>
                                                </a>
                                            </td>
                                            <td data-column="image">
                                                <div class="avatar avatar-sm" style="background-image: url('{{ $product->product_image ? asset('storage/' . $product->product_image) : asset('assets/img/products/default.webp') }}')"></div>
                                            </td>
                                            <td data-column="code">
                                                <span class="text-muted">{{ $product->code }}</span>
                                            </td>
                                            <td data-column="name">
                                                <div>
                                                    <span class="text-reset">{{ $product->name }}</span>
                                                </div>
                                                @if($product->category)
                                                    <div class="text-muted text-xs">{{ $product->category->name }}</div>
                                                @endif
                                            </td>
                                            <td data-column="selling-price">
                                                <span class="fw-semibold">{{ number_format($product->selling_price, 0, '.', ',') }}</span>
                                            </td>
                                            <td data-column="buying-price">
                                                <span class="text-muted">{{ number_format($product->buying_price ?? 0, 0, '.', ',') }}</span>
                                            </td>
                                            <td data-column="quantity">
                                                @if($product->quantity <= ($product->quantity_alert ?? 0))
                                                    <span class="badge bg-red-lt">{{ $product->quantity }}</span>
                                                @else
                                                    <span class="text-muted">{{ $product->quantity }}</span>
                                                @endif
                                            </td>
                                            <td data-column="category" style="display: none;">
                                                <span class="badge bg-blue-lt">{{ $product->category->name ?? '-' }}</span>
                                            </td>
                                            <td data-column="brand" style="display: none;">
                                                <span class="text-muted">{{ $product->brand ?? '-' }}</span>
                                            </td>
                                            <td data-column="location" style="display: none;">
                                                <span class="text-muted">{{ $product->location ?? '-' }}</span>
                                            </td>
                                            <td data-column="weight" style="display: none;">
                                                <span class="text-muted">{{ $product->weight ? $product->weight . ' kg' : '-' }}</span>
                                            </td>
                                            <td data-column="orders">
                                                <span class="text-muted">0</span>
                                            </td>
                                            <td data-column="created">
                                                <span class="text-muted">{{ $product->created_at->format('d/m/Y H:i') }}</span>
                                            </td>
                                        </tr>

                                        <tr class="product-detail-row" id="detail-{{ $product->id }}" style="display: none;">
                                            <td colspan="14" class="p-0">
                                                <div class="card mb-0 border-0 shadow-none">
                                                    <div class="card-body bg-light">
                                                        <div class="row g-4">
                                                            <div class="col-md-2 col-12 text-center">
                                                                <div class="mb-3">
                                                                    <img src="{{ $product->product_image ? asset('storage/' . $product->product_image) : asset('assets/img/products/default.webp') }}" 
                                                                        alt="{{ $product->name }}" 
                                                                        class="img-fluid rounded shadow-sm" 
                                                                        style="max-height: 120px; width: auto; object-fit: cover;">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-10">
                                                                <div class="row g-3">
                                                                    <div class="col-md-4">
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block mb-1">Tên sản phẩm</small>
                                                                            <strong>{{ $product->name }}</strong>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block mb-1">Mã vạch</small>
                                                                            <strong>{{ $product->code }}</strong>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block mb-1">Mã hàng</small>
                                                                            <strong>{{ $product->product_code ?? '-' }}</strong>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="col-md-4">
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block mb-1">Danh mục</small>
                                                                            <span class="badge bg-blue-lt">{{ $product->category->name ?? '-' }}</span>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block mb-1">Thương hiệu</small>
                                                                            <strong>{{ $product->brand ?? 'Chưa có' }}</strong>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block mb-1">Vị trí</small>
                                                                            <strong>{{ $product->location ?? 'Chưa có' }}</strong>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block mb-1">Cảnh báo tồn kho</small>
                                                                            <span class="badge bg-red-lt">{{ $product->quantity_alert ?? 10 }}</span>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block mb-1">Trọng lượng</small>
                                                                            <strong>{{ $product->weight ? $product->weight . ' kg' : '0.05 kg' }}</strong>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="row g-3">
                                                                            <div class="col-md-3">
                                                                                <small class="text-muted d-block mb-1">Giá nhập</small>
                                                                                <strong class="text-primary">{{ number_format($product->buying_price, 0, '.', ',') }} đ</strong>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <small class="text-muted d-block mb-1">Giá bán</small>
                                                                                <strong class="text-success">{{ number_format($product->selling_price, 0, '.', ',') }} đ</strong>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <small class="text-muted d-block mb-1">Hoa hồng</small>
                                                                                <span class="badge bg-green-lt">{{ $product->commission ?? 0.10 }}%</span>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <small class="text-muted d-block mb-1">Thuế</small>
                                                                                <span class="badge bg-red-lt">{{ $product->tax ?? 10 }}%</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    @if($product->notes)
                                                                    <div class="col-12">
                                                                        <small class="text-muted d-block mb-1">Ghi chú</small>
                                                                        <p class="text-muted mb-0">{{ $product->notes }}</p>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                            <div class="col-12 mt-3 pt-3 border-top">
                                                                <div class="d-flex gap-2 justify-content-end">
                                                                    <a href="{{ route('products.edit', $product) }}" 
                                                                        data-product-id="{{ $product->id }}"
                                                                        class="btn btn-outline-primary">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                            <path d="M16 5l3 3" />
                                                                        </svg>
                                                                        Chỉnh sửa
                                                                    </a>
                                                                    
                                                                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xoá sản phẩm này?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-outline-danger">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                <path d="M4 7l16 0" />
                                                                                <path d="M10 11l0 6" />
                                                                                <path d="M14 11l0 6" />
                                                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                                <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                                                            </svg>
                                                                            Xóa
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal modal-blur fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-header">
                <h5 class="modal-title">Import sản phẩm từ Excel</h5>
            </div>
            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" id="import-form">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Chọn file Excel</label>
                        <input type="file"
                               name="file"
                               class="form-control @error('file') is-invalid @enderror"
                               accept=".xlsx,.xls,.csv"
                               required>
                        @error('file')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                        <small class="form-hint">Hỗ trợ các định dạng: .xlsx, .xls, .csv</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-link" data-bs-dismiss="modal">Hủy</a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1" />
                        </svg>
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function numberFormat(num) {
    if (!num && num !== 0) return '0';
    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

// Hàm parse chuỗi có dấu phẩy thành số (VD: '1,000,000' -> 1000000)
function parseNumber(str) {
    return parseFloat(str.replace(/,/g, '')) || 0;
}
    document.addEventListener('DOMContentLoaded', function() {
        const stockCustomRadio = document.getElementById('stock_custom');
        const stockCustomDate = document.getElementById('stock_custom_date');
        const createdCustomRadio = document.getElementById('created_custom');
        const createdCustomDate = document.getElementById('created_custom_date');
        if (stockCustomRadio) {
            stockCustomRadio.addEventListener('change', function() {
                stockCustomDate.style.display = this.checked ? 'block' : 'none';
            });
        }
        if (createdCustomRadio) {
            createdCustomRadio.addEventListener('change', function() {
                createdCustomDate.style.display = this.checked ? 'block' : 'none';
            });
        }
        const selectAllCheckbox = document.getElementById('select-all');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="product_ids[]"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
        let currentOpenDetail = null;
        document.querySelectorAll('.product-row').forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.closest('input[type="checkbox"]') || e.target.closest('a')) {
                    return;
                }
                const productId = this.dataset.productId;
                const detailRow = document.getElementById('detail-' + productId);
                if (currentOpenDetail === productId) {
                    detailRow.style.display = 'none';
                    this.classList.remove('table-active');
                    currentOpenDetail = null;
                } else {
                    if (currentOpenDetail) {
                        const prevDetailRow = document.getElementById('detail-' + currentOpenDetail);
                        const prevRow = document.querySelector(`.product-row[data-product-id="${currentOpenDetail}"]`);
                        if (prevDetailRow) prevDetailRow.style.display = 'none';
                        if (prevRow) prevRow.classList.remove('table-active');
                    }
                    detailRow.style.display = 'table-row';
                    this.classList.add('table-active');
                    currentOpenDetail = productId;
                }
            });
        });

        document.querySelectorAll('.column-toggle').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const columnName = this.dataset.column;
                const isChecked = this.checked;
                
                const headerCells = document.querySelectorAll(`th[data-column="${columnName}"]`);
                headerCells.forEach(cell => {
                    cell.style.display = isChecked ? '' : 'none';
                });
                
                const bodyCells = document.querySelectorAll(`td[data-column="${columnName}"]`);
                bodyCells.forEach(cell => {
                    cell.style.display = isChecked ? '' : 'none';
                });
            });
        });

        const editPriceForm = document.getElementById('edit-price-form');
        if (editPriceForm) {
            editPriceForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const productId = document.getElementById('edit-product-id').value;
                const formData = new FormData(this);
                const submitBtn = document.getElementById('btn-save-price');
                const errorAlert = document.getElementById('price-error-alert');

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

                fetch(`/products/${productId}/update-price`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editPriceModal'));
                        modal.hide();

                        const alertHTML = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <div class="d-flex">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Thành công!</h4>
                                        <div class="text-secondary">${data.message}</div>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        
                        const container = document.querySelector('.container-fluid');
                        const alertDiv = document.createElement('div');
                        alertDiv.innerHTML = alertHTML;
                        container.insertBefore(alertDiv.firstElementChild, container.firstChild);

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    errorAlert.classList.remove('d-none');
                    document.getElementById('price-error-message').textContent = error.message;

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                        Lưu thay đổi
                    `;
                });
            });
        }
    });
</script>

<style>
    .sticky-top {
        position: sticky;
        z-index: 1020;
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
    
    .table-hover tbody tr.product-row:hover {
        background-color: rgba(var(--tblr-primary-rgb), 0.02);
    }
    
    .table-active {
        background-color: rgba(var(--tblr-primary-rgb), 0.05) !important;
    }
    
    .form-check-input:checked {
        background-color: var(--tblr-primary);
        border-color: var(--tblr-primary);
    }
    
    .product-detail-row td {
        transition: all 0.3s ease;
    }
    
    .dropdown-menu {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .dropdown-menu .form-check {
        padding: 0.25rem 0;
    }
    
    .dropdown-menu .form-check-label {
        cursor: pointer;
        user-select: none;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .sticky-top {
            position: relative;
        }
    }

    .dropdown-menu .row.g-2 {
        padding-left: 3rem;  
    }

</style>


<div class="modal modal-blur fade" id="editProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                        <path d="M16 5l3 3" />
                    </svg>
                    Chỉnh sửa thông tin sản phẩm
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="edit-product-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit-product-id" name="product_id">
                
                <div class="modal-body">
                    <!-- Alert Error -->
                    <div class="alert alert-danger d-none" id="edit-error-alert" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M12 8l0 4" />
                                    <path d="M12 16l.01 0" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Có lỗi xảy ra!</h4>
                                <div class="text-secondary" id="edit-error-message"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-12 mb-3 mb-md-0">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-header bg-transparent border-0 pb-2">
                                    <h6 class="card-title mb-0 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M15 8h.01" />
                                            <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                            <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                            <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                        </svg>
                                        Ảnh sản phẩm
                                    </h6>
                                </div>
                                <div class="card-body text-center p-3">
                                    <div class="mb-3">
                                        <img
                                            class="img-fluid rounded shadow-sm"
                                            src="{{ asset('assets/img/products/default.webp') }}"
                                            id="edit-image-preview"
                                            alt="Preview"
                                            style="max-width: 100%; max-height: 200px; width: auto; height: auto; object-fit: cover;"
                                        >
                                    </div>
                                    
                                    <div class="small text-muted mb-2">
                                        JPG, PNG, WEBP<br>(max 2MB)
                                    </div>
                                    
                                    <input
                                        type="file"
                                        accept="image/*"
                                        id="edit-product-image"
                                        name="product_image"
                                        class="form-control form-control-sm @error('product_image') is-invalid @enderror"
                                        onchange="previewEditImage(event);"
                                    >
                                    @error('product_image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-9 col-12">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Tên sản phẩm</label>
                                    <input type="text" class="form-control" id="edit-name" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Mã vạch</label>
                                    <input type="text" class="form-control" id="edit-code" name="code">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Mã hàng</label>
                                    <input type="text" class="form-control" id="edit-product-code" name="product_code">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Thương hiệu</label>
                                    <input type="text" class="form-control" id="edit-brand" name="brand">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Vị trí</label>
                                    <input type="text" class="form-control" id="edit-location" name="location">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Trọng lượng (kg)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-weight" name="weight">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Danh mục</label>
                                    <select class="form-select" id="edit-category" name="category_id" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        @foreach($categories ?? [] as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Đơn vị chính</label>
                                    <select class="form-select" id="edit-unit" name="unit_id" required>
                                        <option value="">-- Chọn đơn vị --</option>
                                        @foreach($units ?? [] as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->short_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Giá nhập</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control format-price" id="edit-buying-price" name="buying_price" required>
                                        <span class="input-group-text">đ</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Giá bán</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control format-price" id="edit-selling-price" name="selling_price" required>
                                        <span class="input-group-text">đ</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Hoa hồng (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-commission" name="commission" min="0" max="100">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Thuế (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-tax" name="tax" min="0" max="100">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Tồn kho</label>
                                    <input type="number" class="form-control" id="edit-quantity" name="quantity" min="0" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Cảnh báo tồn kho</label>
                                <input type="number" class="form-control" id="edit-quantity-alert" name="quantity_alert" min="0">
                                <small class="form-hint">Hệ thống sẽ cảnh báo khi tồn kho thấp hơn giá trị này</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea class="form-control" id="edit-notes" name="notes" rows="2"></textarea>
                            </div>

                            <div>
                                <label class="form-label">Đơn vị phụ (tùy chọn)</label>
                                <div id="edit-product-units-container" class="mb-2"></div>
                                <button type="button" id="edit-add-unit-btn" class="btn btn-outline-primary btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                    Thêm đơn vị phụ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-product">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let editUnitIndex = 0;
    const units = @json($units ?? []);
    document.addEventListener('click', function(e) {
        const editButton = e.target.closest('[data-product-id]');
        if (editButton && editButton.href && editButton.href.includes('/products/') && editButton.href.includes('/edit')) {
            e.preventDefault();
            const productId = editButton.getAttribute('data-product-id');
            loadProductData(productId);
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('format-price')) {
            let value = e.target.value.replace(/,/g, '');  // Loại bỏ tất cả dấu phẩy cũ
            // Chỉ cho phép số (prevent non-numeric input)
            if (!/^\d*$/.test(value)) {
                e.target.value = value.replace(/[^\d]/g, '');  // Xóa ký tự không phải số
                value = e.target.value.replace(/,/g, '');
            }
            if (value !== '' && !isNaN(value)) {
                e.target.value = numberFormat(parseInt(value, 10));  // Format integer, dùng parseInt để tránh decimal
            } else if (value === '') {
                e.target.value = '';  // Cho phép xóa hết
            }
        }
    });

    function loadProductData(productId) {
        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
        const submitBtn = document.getElementById('btn-save-product');
        const errorAlert = document.getElementById('edit-error-alert');
        errorAlert.classList.add('d-none');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tải...';
        modal.show();
        fetch(`/products/${productId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: Không thể tải dữ liệu sản phẩm`);  
            }
            return response.json();
        })
        .then(data => {
            fillFormData(data);
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l5 5l10 -10" />
                </svg>
                Lưu thay đổi
            `;
        })
        .catch(error => {
            errorAlert.classList.remove('d-none');
            document.getElementById('edit-error-message').textContent = error.message;
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Lưu thay đổi';
        });
    }
    function fillFormData(product) {
        document.getElementById('edit-product-id').value = product.id;
        document.getElementById('edit-name').value = product.name || '';
        const imagePreview = document.getElementById('edit-image-preview');
        console.log('Product image:', product.product_image);
        if (product.product_image) {
            if (product.product_image.startsWith('http') || product.product_image.startsWith('/storage')) {
                imagePreview.src = product.product_image;
            } else {
                imagePreview.src = `/storage/${product.product_image}`;
            }
        } else {
            imagePreview.src = '/assets/img/products/default.webp';
        }

        document.getElementById('edit-product-image').value = '';
    
        document.getElementById('edit-product-code').value = product.product_code || '';
        document.getElementById('edit-brand').value = product.brand || '';
        document.getElementById('edit-location').value = product.location || '';
        document.getElementById('edit-weight').value = product.weight || '';
        document.getElementById('edit-category').value = product.category_id || '';
        document.getElementById('edit-unit').value = product.unit_id || '';
        document.getElementById('edit-buying-price').value = numberFormat(product.buying_price || 0);
        document.getElementById('edit-selling-price').value = numberFormat(product.selling_price || 0);
        document.getElementById('edit-commission').value = product.commission || 0;
        document.getElementById('edit-tax').value = product.tax || 0;
        document.getElementById('edit-quantity').value = product.quantity || 0;
        document.getElementById('edit-quantity-alert').value = product.quantity_alert || 0;
        document.getElementById('edit-notes').value = product.notes || '';
        loadProductUnits(product);
    }
    function loadProductUnits(product) {
        const container = document.getElementById('edit-product-units-container');
        container.innerHTML = ''; 
        editUnitIndex = 0;
        fetch(`/products/${product.id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
        if (data.product_units && data.product_units.length > 0) {
            data.product_units.forEach((pu, index) => {
                if (!pu.is_base_unit) {
                    if (!pu.custom_unit_name && pu.unit) {
                        pu.custom_unit_name = pu.unit.name;
                    }
                    addEditUnitRow(pu, index);
                    setTimeout(() => {
                        const newPriceInput = document.querySelectorAll('.edit-unit-selling-price')[index];
                        if (newPriceInput) {
                            newPriceInput.dispatchEvent(new Event('input'));
                        }
                    }, 100);
                }
            });
        }
    })
        .catch(error => {
            console.error('❌ Error loading product units:', error);
        });
    }

    function addEditUnitRow(unitData = null, index = null) {
        const container = document.getElementById('edit-product-units-container');
        const rowIndex = index !== null ? index : editUnitIndex++;
        
        const row = document.createElement('div');
        row.className = 'edit-unit-row mb-3';
        row.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tên đơn vị</label>
                                <input type="text" 
                                    name="product_units[${rowIndex}][custom_unit_name]" 
                                    class="form-control edit-unit-name" 
                                    value="${unitData ? (unitData.custom_unit_name || (unitData.unit ? unitData.unit.name : '')) : ''}" 
                                    placeholder="Nhập tên đơn vị mới (VD: Hộp, Túi...)" 
                                    required>
                                <!-- Hidden input unit_id giữ nguyên -->
                                <input type="hidden" 
                                    name="product_units[${rowIndex}][unit_id]" 
                                    value="${unitData ? unitData.unit_id || '' : ''}">
                            </div>
                        </div>

                        <!-- Cột mã vạch -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Mã vạch</label>
                                <input type="text" 
                                    name="product_units[${rowIndex}][barcode]" 
                                    class="form-control" 
                                    value="${unitData && unitData.barcode ? unitData.barcode : ''}"
                                    placeholder="Tự động nếu để trống">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Giá bán</label>
                                <input type="text" 
                                    name="product_units[${rowIndex}][selling_price]" 
                                    class="form-control format-price edit-unit-selling-price" 
                                    value="${unitData ? numberFormat(unitData.selling_price || 0) : ''}"
                                    placeholder="Nhập giá bán riêng"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-icon btn-outline-danger w-100 edit-remove-unit-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" 
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" 
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(row);
        
        const priceInput = row.querySelector('.format-price.edit-unit-selling-price');
        if (priceInput) {
            priceInput.addEventListener('input', function() {
                let value = this.value.replace(/,/g, '');
                if (!/^\d*$/.test(value)) {
                    this.value = value.replace(/[^\d]/g, '');
                    value = this.value.replace(/,/g, '');
                }
                if (value !== '' && !isNaN(value)) {
                    this.value = numberFormat(parseInt(value, 10));
                } else if (value === '') {
                    this.value = '';
                }
            });
        }
        
        row.querySelector('.edit-remove-unit-btn').addEventListener('click', () => row.remove());
    }

    document.getElementById('edit-add-unit-btn').addEventListener('click', function() {
        addEditUnitRow();
    });

    const editProductForm = document.getElementById('edit-product-form');
    
    if (editProductForm) {
        editProductForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const productId = document.getElementById('edit-product-id').value;
            const submitBtn = document.getElementById('btn-save-product');
            const errorAlert = document.getElementById('edit-error-alert');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
            errorAlert.classList.add('d-none');

            document.querySelectorAll('.format-price').forEach(input => {
                const parsed = parseNumber(input.value);
                input.value = parseInt(parsed, 10).toString();  
            });
            
            const formData = new FormData(this);
            formData.delete('product_id'); 
            
            const ajaxUrl = `/products/${productId}/ajax-update`;
            
            fetch(ajaxUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw err;
                    });
                }
                return response.json();
            })
            .then(data => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                modal.hide();
                
                showSuccessAlert('Sản phẩm đã được cập nhật thành công!');
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            })
            .catch(error => {
                console.error('❌ Error updating product:', error);
                errorAlert.classList.remove('d-none');
                
                let errorMessage = 'Có lỗi xảy ra khi cập nhật sản phẩm!';
                
                if (error.errors) {
                    errorMessage = Object.values(error.errors).flat().join('<br>');
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                document.getElementById('edit-error-message').innerHTML = errorMessage;
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l5 5l10 -10" />
                    </svg>
                    Lưu thay đổi
                `;
            });
        });
    }

    function showSuccessAlert(message) {
        const alertHTML = `
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="alert-title">Thành công!</h4>
                        <div class="text-secondary">${message}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', alertHTML);
        setTimeout(() => {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    const stockCustomRadio = document.getElementById('stock_custom');
    const stockCustomDate = document.getElementById('stock_custom_date');
    const createdCustomRadio = document.getElementById('created_custom');
    const createdCustomDate = document.getElementById('created_custom_date');
    if (stockCustomRadio) {
        stockCustomRadio.addEventListener('change', function() {
            stockCustomDate.style.display = this.checked ? 'block' : 'none';
        });
    }
    if (createdCustomRadio) {
        createdCustomRadio.addEventListener('change', function() {
            createdCustomDate.style.display = this.checked ? 'block' : 'none';
        });
    }
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="product_ids[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    let currentOpenDetail = null;
    document.querySelectorAll('.product-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('input[type="checkbox"]') || e.target.closest('a')) {
                return;
            }
            const productId = this.dataset.productId;
            const detailRow = document.getElementById('detail-' + productId);
            if (currentOpenDetail === productId) {
                detailRow.style.display = 'none';
                this.classList.remove('table-active');
                currentOpenDetail = null;
            } else {
                if (currentOpenDetail) {
                    const prevDetailRow = document.getElementById('detail-' + currentOpenDetail);
                    const prevRow = document.querySelector(`.product-row[data-product-id="${currentOpenDetail}"]`);
                    if (prevDetailRow) prevDetailRow.style.display = 'none';
                    if (prevRow) prevRow.classList.remove('table-active');
                }
                detailRow.style.display = 'table-row';
                this.classList.add('table-active');
                currentOpenDetail = productId;
            }
        });
    });

    document.querySelectorAll('.column-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const columnName = this.dataset.column;
            const isChecked = this.checked;
            
            const headerCells = document.querySelectorAll(`th[data-column="${columnName}"]`);
            headerCells.forEach(cell => {
                cell.style.display = isChecked ? '' : 'none';
            });
            
            const bodyCells = document.querySelectorAll(`td[data-column="${columnName}"]`);
            bodyCells.forEach(cell => {
                cell.style.display = isChecked ? '' : 'none';
            });
        });
    });

    const editPriceForm = document.getElementById('edit-price-form');
    if (editPriceForm) {
        editPriceForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const productId = document.getElementById('edit-product-id').value;
            const formData = new FormData(this);
            const submitBtn = document.getElementById('btn-save-price');
            const errorAlert = document.getElementById('price-error-alert');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

            fetch(`/products/${productId}/update-price`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editPriceModal'));
                    modal.hide();

                    const alertHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">Thành công!</h4>
                                    <div class="text-secondary">${data.message}</div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    
                    const container = document.querySelector('.container-fluid');
                    const alertDiv = document.createElement('div');
                    alertDiv.innerHTML = alertHTML;
                    container.insertBefore(alertDiv.firstElementChild, container.firstChild);

                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Có lỗi xảy ra!');
                }
            })
            .catch(error => {
                errorAlert.classList.remove('d-none');
                document.getElementById('price-error-message').textContent = error.message;

                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l5 5l10 -10" />
                    </svg>
                    Lưu thay đổi
                `;
            });
        });
    }
});

    function previewEditImage(event) {
        const reader = new FileReader();
        const imagePreview = document.getElementById('edit-image-preview');
        
        reader.onload = function() {
            if (reader.readyState === 2) {
                imagePreview.src = reader.result;
            }
        };
        
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

</script>

<style>
    .modal-backdrop.show {
        opacity: 0.7;
    }

    .modal-content {
        border-radius: 0.5rem;
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    }

    .modal-header {
        border-bottom: 1px solid rgba(98, 105, 118, 0.16);
        padding: 1.25rem 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }

    .modal-footer {
        border-top: 1px solid rgba(98, 105, 118, 0.16);
        padding: 1rem 1.5rem;
    }

    #editProductModal .form-control:focus,
    #editProductModal .form-select:focus {
        border-color: var(--tblr-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--tblr-primary-rgb), 0.25);
    }

    #btn-save-product .spinner-border {
        width: 1rem;
        height: 1rem;
    }

    #edit-error-alert {
        margin-bottom: 1rem;
    }

    .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
        }
        
        .modal-body {
            max-height: calc(100vh - 150px);
        }
    }

    .btn-outline-primary:hover {
        background-color: #206bc4 !important;
        border-color: #206bc4 !important;
        color: #fff !important;
    }

    .btn-outline-primary:hover svg {
        color: #fff !important;
    }

    .btn-outline-danger:hover {
        background-color: #d63939 !important;
        border-color: #d63939 !important;
        color: #fff !important;
    }

    .btn-outline-danger:hover svg {
        color: #fff !important;
    }

    .btn-outline-primary, 
    .btn-outline-danger {
        transition: all 0.2s ease-in-out;
    }

    #edit-image-preview {
        border: 2px dashed #e9ecef;
        padding: 0.5rem;
        transition: all 0.3s ease;
        background: white;
    }

    #edit-image-preview:hover {
        border-color: var(--tblr-primary);
    }

    @media (max-width: 768px) {
        #edit-image-preview {
            max-height: 150px;
        }
    }

</style>

@endsection
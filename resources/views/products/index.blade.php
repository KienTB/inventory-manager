@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        @if($products->isEmpty() && !request()->has('search') && !request()->has('category') && !request()->has('brand') && !request()->has('price_min'))
            <x-empty
                title="No products found"
                message="Try adjusting your search or filter to find what you're looking for."
                button_label="{{ __('Add your first Product') }}"
                button_route="{{ route('products.create') }}"
            />
        @else
            <x-alert/>

            {{-- ============================================
                 TOOLBAR: Filter + Import/Export
                 ============================================ --}}
            <div class="row row-cards mb-3">
                {{-- Thanh tìm kiếm và bộ lọc --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('products.index') }}" method="GET" id="filter-form" class="row g-3 align-items-end">
                                {{-- Tìm kiếm theo tên sản phẩm --}}
                                <div class="col-md-3">
                                    <label class="form-label">Tìm kiếm sản phẩm</label>
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Nhập tên hoặc mã sản phẩm..."
                                           value="{{ request('search') }}">
                                </div>

                                {{-- Lọc theo danh mục --}}
                                <div class="col-md-2">
                                    <label class="form-label">Danh mục</label>
                                    <select name="category" class="form-select" onchange="document.getElementById('filter-form').submit()">
                                        <option value="">-- Tất cả --</option>
                                        @foreach($categories ?? [] as $cat)
                                            <option value="{{ $cat->id }}" @selected($cat->id == request('category'))>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Thương hiệu</label>
                                    <input type="text" 
                                           name="brand" 
                                           class="form-control" 
                                           placeholder="Nhập thương hiệu..."
                                           value="{{ request('brand') }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Giá tối thiểu</label>
                                    <input type="number" 
                                           name="price_min" 
                                           class="form-control" 
                                           placeholder="0"
                                           value="{{ request('price_min') }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Giá tối đa</label>
                                    <input type="number" 
                                           name="price_max" 
                                           class="form-control" 
                                           placeholder="0"
                                           value="{{ request('price_max') }}">
                                </div>

                                <div class="col-md-1 text-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 3r7 0" />
                                            <path d="M5 21h14" />
                                            <path d="M4 6h16" />
                                            <path d="M9 12h2" />
                                            <path d="M13 12h2" />
                                        </svg>
                                        Lọc
                                    </button>
                                </div>

                                <div class="col-md-1 text-end">
                                    <a href="{{ route('products.index') }}" class="btn btn-secondary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                            <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                            <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                        </svg>
                                        Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-between">
                        <div class="btn-list">
                            <a href="{{ route('products.create') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                                Thêm sản phẩm
                            </a>

                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M9 9h6" />
                                    <path d="M9 15h6" />
                                </svg>
                                Import Excel
                            </button>

                            <form action="{{ route('products.export') }}" method="GET" style="display: inline;">
                                @csrf
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="category" value="{{ request('category') }}">
                                <input type="hidden" name="brand" value="{{ request('brand') }}">
                                <input type="hidden" name="price_min" value="{{ request('price_min') }}">
                                <input type="hidden" name="price_max" value="{{ request('price_max') }}">
                                
                                <button type="submit" class="btn btn-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M12 17v-6" />
                                        <path d="M9.5 14.5l2.5 2.5l2.5 -2.5" />
                                    </svg>
                                    Export Excel
                                </button>
                            </form>
                        </div>

                        <div class="text-muted">
                            <small>Tổng: <strong>{{ $products->total() }}</strong> sản phẩm</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Tên sản phẩm</th>
                                <th>Mã sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Giá bán</th>
                                <th>Số lượng</th>
                                <th class="w-1">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr class="product-row" data-product-id="{{ $product->id }}">
                                    <td>
                                        <button type="button" 
                                                class="btn btn-ghost-primary btn-icon btn-sm toggle-details"
                                                data-product-id="{{ $product->id }}"
                                                title="Xem chi tiết">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-expand" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M6 9l6 6l6 -6" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md" style="background-image: url('{{ asset('assets/img/products/default.webp') }}')"></div>
                                            <div class="ms-3">
                                                <div class="text-heading">{{ $product->name }}</div>
                                                <div class="text-muted text-xs">{{ $product->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->code }}</td>
                                    <td>
                                        <span class="badge bg-blue-lt">{{ $product->category->name }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($product->selling_price, 0, ',', '.') }} đ</strong>
                                    </td>
                                    <td>
                                        @if($product->quantity <= $product->quantity_alert)
                                            <span class="badge bg-red-lt">{{ $product->quantity }}</span>
                                        @else
                                            <span class="badge bg-green-lt">{{ $product->quantity }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-ghost-primary btn-icon btn-sm" title="Xem chi tiết">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                    <path d="M12 19c-4 0 -7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7c-2.667 4.667 -6 7 -10 7" />
                                                </svg>
                                            </a>

                                            <button type="button" 
                                                    class="btn btn-ghost-warning btn-icon btn-sm btn-edit-price" 
                                                    data-product-id="{{ $product->id }}"
                                                    data-product-name="{{ $product->name }}"
                                                    data-buying-price="{{ $product->buying_price }}"
                                                    data-selling-price="{{ $product->selling_price }}"
                                                    data-commission="{{ $product->commission ?? 0 }}"
                                                    data-tax="{{ $product->tax ?? 0 }}"
                                                    title="Sửa giá nhanh"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editPriceModal">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                                    <path d="M12 7v10" />
                                                    <path d="M8 11h8" />
                                                </svg>
                                            </button>

                                            <a href="{{ route('products.edit', $product) }}" class="btn btn-ghost-primary btn-icon btn-sm" title="Chỉnh sửa">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                    <path d="M16 5l3 3" />
                                                </svg>
                                            </a>

                                            <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-ghost-danger btn-icon btn-sm"
                                                        title="Xoá"
                                                        onclick="return confirm('Bạn chắc chắn muốn xoá sản phẩm này?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="product-details-row" id="details-{{ $product->id }}" style="display: none;">
                                    <td colspan="7">
                                        <div class="details-container p-2 bg-light border-top">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <div class="details-info">
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Mã hàng</small>
                                                                <span class="fw-semibold">{{ $product->product_code ?? '-' }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Thương hiệu</small>
                                                                <span class="fw-semibold">{{ $product->brand ?? '-' }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Vị trí</small>
                                                                <span class="fw-semibold">{{ $product->location ?? '-' }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Trọng lượng</small>
                                                                <span class="fw-semibold">{{ $product->weight ? $product->weight . ' kg' : '-' }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Đơn vị</small>
                                                                <span class="badge bg-azure-lt">{{ $product->unit->short_code }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Cảnh báo tồn</small>
                                                                <span class="badge bg-red-lt">{{ $product->quantity_alert }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="details-pricing">
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Giá mua</small>
                                                                <span class="fw-semibold">{{ number_format($product->buying_price, 0, ',', '.') }} đ</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Giá bán</small>
                                                                <span class="fw-semibold text-success">{{ number_format($product->selling_price, 0, ',', '.') }} đ</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Hoa hồng</small>
                                                                <span class="badge bg-green-lt">{{ $product->commission ?? 0 }}%</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Thuế</small>
                                                                <span class="badge bg-red-lt">{{ $product->tax }}%</span>
                                                            </div>
                                                            <div class="col-12">
                                                                <small class="text-muted d-block">Loại thuế</small>
                                                                <span class="fw-semibold">{{ $product->tax_type?->label() ?? '-' }}</span>
                                                            </div>
                                                            @if($product->notes)
                                                            <div class="col-12">
                                                                <small class="text-muted d-block">Ghi chú</small>
                                                                <span class="fw-semibold">{{ $product->notes }}</span>
                                                            </div>
                                                            @endif
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

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Import Excel --}}
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
                            <path d="M7 18c-1.1 0 -2 .9 -2 2s.9 2 2 2s2 -.9 2 -2s-.9 -2 -2 -2z" />
                            <path d="M17 18c-1.1 0 -2 .9 -2 2s.9 2 2 2s2 -.9 2 -2s-.9 -2 -2 -2z" />
                            <path d="M12 18v.01" />
                            <path d="M2 6h20v11a2 2 0 0 1 -2 2h-16a2 2 0 0 1 -2 -2v-11z" />
                            <path d="M2 6l1 -3h18l1 3" />
                        </svg>
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Sửa giá nhanh --}}
<div class="modal modal-blur fade" id="editPriceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                        <path d="M12 7v10" />
                        <path d="M8 11h8" />
                    </svg>
                    Sửa giá nhanh
                </h5>
            </div>
            <form id="edit-price-form" method="POST">
                @csrf
                <input type="hidden" id="edit-product-id" name="product_id">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Sản phẩm</label>
                        <div class="text-primary fw-semibold" id="edit-product-name"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Giá nhập</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="edit-buying-price" 
                                       name="buying_price" 
                                       min="0" 
                                       step="1000"
                                       placeholder="0">
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Giá bán</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="edit-selling-price" 
                                       name="selling_price" 
                                       min="0" 
                                       step="1000"
                                       placeholder="0"
                                       required>
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Hoa hồng</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="edit-commission" 
                                       name="commission" 
                                       min="0" 
                                       max="100"
                                       step="0.1"
                                       placeholder="0">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Thuế</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="edit-tax" 
                                       name="tax" 
                                       min="0" 
                                       max="100"
                                       step="0.1"
                                       placeholder="0">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    {{-- Alert khi có lỗi --}}
                    <div class="alert alert-danger mt-3 d-none" id="price-error-alert" role="alert">
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
                                <div class="text-secondary" id="price-error-message"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn btn-link" data-bs-dismiss="modal">Hủy</a>
                    <button type="submit" class="btn btn-primary" id="btn-save-price">
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
        // ============================================
        // Toggle Details (Code cũ)
        // ============================================
        const toggleButtons = document.querySelectorAll('.toggle-details');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const productId = this.getAttribute('data-product-id');
                const detailsRow = document.getElementById(`details-${productId}`);
                const icon = this.querySelector('.icon-expand');

                if (detailsRow.style.display === 'none') {
                    detailsRow.style.display = 'table-row';
                    icon.style.transform = 'rotate(180deg)';
                    icon.style.transition = 'transform 0.3s ease';
                } else {
                    detailsRow.style.display = 'none';
                    icon.style.transform = 'rotate(0deg)';
                    icon.style.transition = 'transform 0.3s ease';
                }
            });
        });

        // ============================================
        // Edit Price Modal (Code mới)
        // ============================================
        
        // Xử lý khi click button "Sửa giá"
        document.querySelectorAll('.btn-edit-price').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                const buyingPrice = this.dataset.buyingPrice;
                const sellingPrice = this.dataset.sellingPrice;
                const commission = this.dataset.commission || 0;
                const tax = this.dataset.tax || 0;

                // Điền dữ liệu vào form
                document.getElementById('edit-product-id').value = productId;
                document.getElementById('edit-product-name').textContent = productName;
                document.getElementById('edit-buying-price').value = buyingPrice;
                document.getElementById('edit-selling-price').value = sellingPrice;
                document.getElementById('edit-commission').value = commission;
                document.getElementById('edit-tax').value = tax;

                // Ẩn alert lỗi cũ
                document.getElementById('price-error-alert').classList.add('d-none');
            });
        });

        // Xử lý submit form AJAX
        const editPriceForm = document.getElementById('edit-price-form');
        editPriceForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const productId = document.getElementById('edit-product-id').value;
            const formData = new FormData(this);
            const submitBtn = document.getElementById('btn-save-price');
            const errorAlert = document.getElementById('price-error-alert');

            // Disable button khi đang xử lý
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

            // Gửi AJAX request
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
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editPriceModal'));
                    modal.hide();

                    // Hiển thị thông báo thành công
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
                    
                    // Thêm alert vào đầu container
                    const container = document.querySelector('.container-xl');
                    const alertDiv = document.createElement('div');
                    alertDiv.innerHTML = alertHTML;
                    container.insertBefore(alertDiv.firstElementChild, container.firstChild);

                    // Reload trang sau 1.5s để cập nhật dữ liệu
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Có lỗi xảy ra!');
                }
            })
            .catch(error => {
                // Hiển thị lỗi trong modal
                errorAlert.classList.remove('d-none');
                document.getElementById('price-error-message').textContent = error.message;

                // Enable lại button
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
    });
</script>

@endsection
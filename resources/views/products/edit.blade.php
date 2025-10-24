@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Chỉnh Sửa Sản Phẩm') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $product])
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="card-title mb-3">{{ __('Ảnh Sản Phẩm') }}</h3>

                                <img
                                    class="img-account-profile mb-2"
                                    src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/img/products/default.webp') }}"
                                    id="image-preview"
                                    style="max-width: 100%; height: auto;"
                                >

                                <div class="small text-muted mb-2">
                                    JPG hoặc PNG, dung lượng tối đa 2 MB
                                </div>

                                <input
                                    type="file"
                                    accept="image/*"
                                    id="image"
                                    name="product_image"
                                    class="form-control @error('product_image') is-invalid @enderror"
                                    onchange="previewImage();"
                                >

                                @error('product_image')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Cột phải: Thông tin sản phẩm --}}
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title mb-4">{{ __('Thông Tin Sản Phẩm') }}</h3>

                                {{-- Nhóm thông tin cơ bản --}}
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="name" class="form-label">
                                            Tên sản phẩm <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $product->name) }}" required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- ✅ Thêm nhóm 5 trường mới --}}
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Mã hàng</label>
                                            <input type="text" name="product_code" class="form-control"
                                                   value="{{ old('product_code', $product->product_code ?? '') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Thương hiệu</label>
                                            <input type="text" name="brand" class="form-control"
                                                   value="{{ old('brand', $product->brand ?? '') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Vị trí</label>
                                            <input type="text" name="location" class="form-control"
                                                   value="{{ old('location', $product->location ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Hoa hồng nhân viên (%)</label>
                                            <input type="number" step="0.01" name="commission" class="form-control"
                                                   value="{{ old('commission', $product->commission ?? 0) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Trọng lượng (kg)</label>
                                            <input type="number" step="0.01" name="weight" class="form-control"
                                                   value="{{ old('weight', $product->weight ?? '') }}">
                                        </div>
                                    </div>
                                    {{-- ✅ Kết thúc phần thêm mới --}}

                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id"
                                                class="form-select @error('category_id') is-invalid @enderror" required>
                                            <option value="">-- Chọn danh mục --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label">Đơn vị cơ bản <span class="text-danger">*</span></label>
                                        <select name="unit_id" id="unit_id"
                                                class="form-select @error('unit_id') is-invalid @enderror" required>
                                            <option value="">-- Chọn đơn vị --</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->name }} ({{ $unit->short_code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label">Giá mua <span class="text-danger">*</span></label>
                                        <input type="number" name="buying_price" id="buying_price" step="0.01"
                                            class="form-control @error('buying_price') is-invalid @enderror"
                                            value="{{ old('buying_price', $product->buying_price) }}" required>
                                        @error('buying_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label">Giá bán (đơn vị cơ bản) <span class="text-danger">*</span></label>
                                        <input type="number" name="selling_price" id="selling_price" step="0.01"
                                            class="form-control @error('selling_price') is-invalid @enderror"
                                            value="{{ old('selling_price', $product->selling_price) }}" required>
                                        @error('selling_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                        <input type="number" name="quantity" id="quantity" min="0"
                                            class="form-control @error('quantity') is-invalid @enderror"
                                            value="{{ old('quantity', $product->quantity) }}" required>
                                        @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label">Cảnh báo tồn kho <span class="text-danger">*</span></label>
                                        <input type="number" name="quantity_alert" id="quantity_alert" min="0"
                                            class="form-control @error('quantity_alert') is-invalid @enderror"
                                            value="{{ old('quantity_alert', $product->quantity_alert) }}" required>
                                        @error('quantity_alert')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="notes" class="form-label">Ghi chú</label>
                                        <textarea name="notes" id="notes" rows="3"
                                                  class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $product->notes) }}</textarea>
                                        @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Đơn vị phụ --}}
                                {{-- Đơn vị phụ --}}
<div class="col-md-12">
    <div class="mb-3">
        <label class="form-label">
            Đơn vị tính phụ
            <span class="form-label-description">Thêm các đơn vị đóng gói khác (hộp, lốc, thùng...)</span>
        </label>
        <div id="product-units-container">
            @if($productUnits && count($productUnits) > 0)
                @foreach($productUnits as $index => $productUnit)
                    <div class="unit-row mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Đơn vị</label>
                                            <select name="product_units[{{ $index }}][unit_id]" class="form-select" required>
                                                <option value="">-- Chọn đơn vị --</option>
                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}" {{ $productUnit->unit_id == $unit->id ? 'selected' : '' }}>
                                                        {{ $unit->name }} ({{ $unit->short_code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label required">Tỷ lệ quy đổi</label>
                                            <input type="number" 
                                                name="product_units[{{ $index }}][conversion_rate]" 
                                                class="form-control conversion-rate" 
                                                min="1" 
                                                value="{{ $productUnit->conversion_rate }}" 
                                                placeholder="1"
                                                required>
                                            <small class="form-hint">VD: 1 hộp = 6 cái</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">Mã vạch</label>
                                            <input type="text" 
                                                name="product_units[{{ $index }}][barcode]" 
                                                class="form-control" 
                                                value="{{ $productUnit->barcode ?? '' }}"
                                                placeholder="Tự động nếu để trống">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Giá bán</label>
                                            <input type="number" 
                                                name="product_units[{{ $index }}][selling_price]" 
                                                class="form-control unit-selling-price" 
                                                step="0.01" 
                                                value="{{ $productUnit->selling_price }}"
                                                placeholder="0"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-icon btn-outline-danger w-100 remove-unit-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M4 7l16 0"></path>
                                                    <path d="M10 11l0 6"></path>
                                                    <path d="M14 11l0 6"></path>
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" class="btn btn-outline-primary mt-2" id="add-unit-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 5l0 14"></path>
                <path d="M5 12l14 0"></path>
            </svg>
            Thêm đơn vị
        </button>
    </div>
</div>
                            </div>

                            <div class="card-footer text-end">
                                <x-button.save type="submit">
                                    {{ __('Cập nhật') }}
                                </x-button.save>

                                <x-button.back route="{{ route('products.index') }}">
                                    {{ __('Hủy') }}
                                </x-button.back>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@pushonce('page-scripts')
<script src="{{ asset('assets/js/img-preview.js') }}"></script>
<script>
let unitIndex = {{ count($productUnits) }};
const units = @json($units);

document.getElementById('add-unit-btn').addEventListener('click', addUnitRow);

// Xử lý xóa các unit có sẵn
document.querySelectorAll('.remove-unit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('.unit-row').remove();
    });
});

// Xử lý tính giá cho các unit có sẵn
document.querySelectorAll('.conversion-rate').forEach(input => {
    const row = input.closest('.unit-row');
    const priceInput = row.querySelector('.unit-selling-price');
    const basePrice = document.getElementById('selling_price');
    
    input.addEventListener('input', function() {
        if (basePrice.value) {
            priceInput.value = (parseFloat(basePrice.value) * parseInt(this.value) * 0.95).toFixed(2);
        }
    });
});

function addUnitRow() {
    const container = document.getElementById('product-units-container');
    const row = document.createElement('div');
    row.className = 'unit-row mb-3';
    row.innerHTML = `
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label required">Đơn vị</label>
                            <select name="product_units[${unitIndex}][unit_id]" class="form-select" required>
                                <option value="">-- Chọn đơn vị --</option>
                                ${units.map(unit => `<option value="${unit.id}">${unit.name} (${unit.short_code})</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label required">Tỷ lệ quy đổi</label>
                            <input type="number" 
                                   name="product_units[${unitIndex}][conversion_rate]" 
                                   class="form-control conversion-rate" 
                                   min="1" 
                                   value="1" 
                                   placeholder="1"
                                   required>
                            <small class="form-hint">VD: 1 hộp = 6 cái</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Mã vạch</label>
                            <input type="text" 
                                   name="product_units[${unitIndex}][barcode]" 
                                   class="form-control" 
                                   placeholder="Tự động nếu để trống">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label required">Giá bán</label>
                            <input type="number" 
                                   name="product_units[${unitIndex}][selling_price]" 
                                   class="form-control unit-selling-price" 
                                   step="0.01"
                                   placeholder="0"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-icon btn-outline-danger w-100 remove-unit-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 7l16 0"></path>
                                    <path d="M10 11l0 6"></path>
                                    <path d="M14 11l0 6"></path>
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.appendChild(row);
    unitIndex++;

    const conversionInput = row.querySelector('.conversion-rate');
    const priceInput = row.querySelector('.unit-selling-price');
    const basePrice = document.getElementById('selling_price');

    conversionInput.addEventListener('input', function() {
        if (basePrice.value) {
            priceInput.value = (parseFloat(basePrice.value) * parseInt(this.value) * 0.95).toFixed(2);
        }
    });

    row.querySelector('.remove-unit-btn').addEventListener('click', () => row.remove());
}

document.getElementById('selling_price').addEventListener('input', function() {
    document.querySelectorAll('.conversion-rate').forEach(input => {
        input.dispatchEvent(new Event('input'));
    });
});
</script>
@endpushonce

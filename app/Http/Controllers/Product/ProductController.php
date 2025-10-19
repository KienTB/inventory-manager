<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit']);
        
        // Lọc theo tên hoặc mã sản phẩm
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('code', 'like', '%' . $request->search . '%');
        }
        
        // Lọc theo danh mục
        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Lọc theo thương hiệu
        if ($request->brand) {
            $query->where('brand', 'like', '%' . $request->brand . '%');
        }
        
        // Lọc theo khoảng giá
        if ($request->price_min) {
            $query->where('selling_price', '>=', $request->price_min);
        }
        if ($request->price_max) {
            $query->where('selling_price', '<=', $request->price_max);
        }
        
        $products = $query->paginate(15);
        $categories = Category::all(); // Để hiển thị trong dropdown lọc
        
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Hiển thị form tạo sản phẩm
     */
    public function create(Request $request)
    {
        $categories = Category::all(['id', 'name']);
        $units = Unit::all(['id', 'name']);

        if ($request->has('category')) {
            $categories = Category::whereSlug($request->get('category'))->get();
        }

        if ($request->has('unit')) {
            $units = Unit::whereSlug($request->get('unit'))->get();
        }

        return view('products.create', compact('categories', 'units'));
    }

    /**
     * Lưu sản phẩm mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255|unique:products,code',
            'product_code' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'commission' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'buying_price' => 'required|integer|min:0',
            'selling_price' => 'required|integer|min:0',
            'quantity_alert' => 'nullable|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'unit_id' => 'required|integer|exists:units,id',
        ]);

        try {
            DB::beginTransaction();

            // 🔹 Sinh mã sản phẩm tự động
            $productCode = $this->generateProductCode();

            // 🔹 Chuẩn bị dữ liệu lưu
            $productData = $request->only([
                'name', 'slug', 'code', 'product_code', 'brand', 'location',
                'commission', 'weight', 'quantity', 'buying_price', 'selling_price',
                'quantity_alert', 'tax', 'tax_type', 'notes', 'category_id', 'unit_id',
            ]);

            // 🔹 Tạo slug và code nếu chưa có
            $productData['slug'] = $productData['slug'] ?? Str::slug($productData['name']);
            $productData['code'] = $productData['code'] ?? $productCode;

            $product = Product::create($productData);

            // 🔹 Upload ảnh nếu có
            if ($request->hasFile('product_image')) {
                $image = $request->file('product_image');
                $imageName = time() . '.' . $image->extension();
                $image->storeAs('products', $imageName, 'public');
                $product->update(['product_image' => $imageName]);
            }

            // 🔑 Thêm đơn vị cơ bản (base unit)
            $product->productUnits()->create([
                'unit_id' => $product->unit_id,
                'conversion_rate' => 1,
                'barcode' => $product->code,
                'selling_price' => $product->selling_price * 100,
                'is_base_unit' => 1,
            ]);

            // 🔑 Thêm các đơn vị phụ nếu có
            if ($request->has('product_units')) {
                foreach ($request->product_units as $unitData) {
                    if (!empty($unitData['unit_id'])) {
                        $product->productUnits()->create([
                            'unit_id' => $unitData['unit_id'],
                            'conversion_rate' => $unitData['conversion_rate'] ?? 1,
                            'barcode' => $unitData['barcode'] ?? null,
                            'selling_price' => isset($unitData['selling_price'])
                                ? (int)($unitData['selling_price'] * 100)
                                : 0,
                            'is_base_unit' => 0,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('products.index')
                ->with('success', 'Sản phẩm đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Lỗi khi tạo sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'productUnits.unit']);
        $generator = new BarcodeGeneratorHTML();
        $barcode = $generator->getBarcode($product->code, $generator::TYPE_CODE_128);
        return view('products.show', compact('product', 'barcode'));
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $units = Unit::all();
        $productUnits = $product->productUnits()->where('is_base_unit', false)->get();
        return view('products.edit', compact('product', 'categories', 'units', 'productUnits'));
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255|unique:products,code,' . $product->id,
            'product_code' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'commission' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'buying_price' => 'required|integer|min:0',
            'selling_price' => 'required|integer|min:0',
            'quantity_alert' => 'nullable|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'unit_id' => 'required|integer|exists:units,id',
        ]);

        try {
            DB::beginTransaction();

            $productData = $request->only([
                'name', 'slug', 'code', 'product_code', 'brand', 'location',
                'commission', 'weight', 'quantity', 'buying_price', 'selling_price',
                'quantity_alert', 'tax', 'tax_type', 'notes', 'category_id', 'unit_id',
            ]);

            $productData['slug'] = $productData['slug'] ?? Str::slug($productData['name']);

            $product->update($productData);

            if ($request->hasFile('product_image')) {
                if ($product->product_image) {
                    Storage::disk('public')->delete('products/' . $product->product_image);
                }
                $image = $request->file('product_image');
                $imageName = time() . '.' . $image->extension();
                $image->storeAs('products', $imageName, 'public');
                $product->update(['product_image' => $imageName]);
            }

            $product->productUnits()->where('is_base_unit', 0)->delete();

            if ($request->has('product_units')) {
                foreach ($request->product_units as $unitData) {
                    if (!empty($unitData['unit_id'])) {
                        $product->productUnits()->create([
                            'unit_id' => $unitData['unit_id'],
                            'conversion_rate' => $unitData['conversion_rate'] ?? 1,
                            'barcode' => $unitData['barcode'] ?? null,
                            'selling_price' => isset($unitData['selling_price'])
                                ? (int)($unitData['selling_price'] * 100)
                                : 0,
                            'is_base_unit' => 0,
                        ]);
                    }
                }
            }

            $baseUnit = $product->productUnits()->where('is_base_unit', 1)->first();
            if ($baseUnit) {
                $baseUnit->update([
                    'unit_id' => $product->unit_id,
                    'selling_price' => $product->selling_price * 100,
                ]);
            } else {
                $product->productUnits()->create([
                    'unit_id' => $product->unit_id,
                    'conversion_rate' => 1,
                    'barcode' => $product->code,
                    'selling_price' => $product->selling_price * 100,
                    'is_base_unit' => 1,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('products.index')
                ->with('success', 'Sản phẩm đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        if ($product->product_image) {
            Storage::disk('public')->delete('products/' . $product->product_image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Sản phẩm đã được xóa thành công!');
    }

       private function generateProductCode()
    {
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $number = $lastProduct ? intval(substr($lastProduct->code, 3)) + 1 : 1;
        return 'PRD' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function updatePrice(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $validated = $request->validate([
                'buying_price' => 'nullable|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'commission' => 'nullable|numeric|min:0|max:100',
                'tax' => 'nullable|numeric|min:0|max:100',
            ]);
            $updateData = array_filter($validated, fn($v) => $v !== null && $v !== '');
            $product->update($updateData);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Cập nhật giá thành công!']);
            }
            return redirect()->route('products.index')->with('success', 'Cập nhật giá thành công!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
            }
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
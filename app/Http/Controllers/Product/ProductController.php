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
        $products = Product::with(['category', 'unit', 'productUnits.unit'])
            ->when($request->get('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
            })
            ->when($request->get('category'), function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->latest()
            ->paginate(10);

        $categories = Category::all();

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

            // 🔑 Cập nhật ảnh nếu có
            if ($request->hasFile('product_image')) {
                if ($product->product_image) {
                    Storage::disk('public')->delete('products/' . $product->product_image);
                }
                $image = $request->file('product_image');
                $imageName = time() . '.' . $image->extension();
                $image->storeAs('products', $imageName, 'public');
                $product->update(['product_image' => $imageName]);
            }

            // 🔑 Xóa đơn vị phụ cũ
            $product->productUnits()->where('is_base_unit', 0)->delete();

            // 🔑 Lưu lại các đơn vị phụ mới
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

            // 🔑 Cập nhật đơn vị cơ bản
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

    /**
     * Xóa sản phẩm
     */
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

    /**
     * Sinh mã sản phẩm tự động (PRD00001, PRD00002, ...)
     */
    private function generateProductCode()
    {
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $number = $lastProduct ? intval(substr($lastProduct->code, 3)) + 1 : 1;
        return 'PRD' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
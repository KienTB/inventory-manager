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
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
            'category', 
            'unit',
            'productUnits.unit'  
        ]);
        
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('code', 'like', '%' . $request->search . '%');
        }
        
        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->brand) {
            $query->where('brand', 'like', '%' . $request->brand . '%');
        }
        
        if ($request->price_min) {
            $query->where('selling_price', '>=', $request->price_min);
        }
        if ($request->price_max) {
            $query->where('selling_price', '<=', $request->price_max);
        }

        if ($request->stock_status === 'out_of_stock') {
            $query->where('quantity', '<=', 0);
        }
        
        $products = $query->paginate(15);
        $categories = Category::all(); 
        $units = Unit::all();
        
        return view('products.index', compact('products', 'categories', 'units'));
    }

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
            $productCode = $this->generateProductCode();
            $productData = $request->only([
                'name', 'slug', 'code', 'product_code', 'brand', 'location',
                'commission', 'weight', 'quantity', 'buying_price', 'selling_price',
                'quantity_alert', 'tax', 'tax_type', 'notes', 'category_id', 'unit_id',
            ]);
            $productData['slug'] = $productData['slug'] ?? Str::slug($productData['name']);
            $productData['code'] = $productData['code'] ?? $productCode;
            $product = Product::create($productData);
            if ($request->hasFile('product_image')) {
                $image = $request->file('product_image');
                $imageName = time() . '.' . $image->extension();
                $image->storeAs('products', $imageName, 'public');
                $product->update(['product_image' => $imageName]);
            }
            $product->productUnits()->create([
                'unit_id' => $product->unit_id,
                'conversion_rate' => 1,
                'barcode' => $product->code,
                'selling_price' => $product->selling_price * 100,
                'is_base_unit' => 1,
            ]);
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

    public function show($id)
    {
        try {
            $product = Product::with(['category', 'unit', 'productUnits.unit'])->findOrFail($id);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'product_code' => $product->product_code,
                    'brand' => $product->brand,
                    'location' => $product->location,
                    'commission' => $product->commission,
                    'weight' => $product->weight,
                    'quantity' => $product->quantity,
                    'buying_price' => (float) $product->buying_price,
                    'selling_price' => (float) $product->selling_price,
                    'quantity_alert' => $product->quantity_alert,
                    'tax' => $product->tax,
                    'notes' => $product->notes,
                    'category_id' => $product->category_id,
                    'unit_id' => $product->unit_id,
                    'category' => $product->category,
                    'unit' => $product->unit,
                    'product_units' => $product->productUnits->map(function($pu) {
                        return [
                            'id' => $pu->id,
                            'unit_id' => $pu->unit_id,
                            'unit' => $pu->unit,
                            'barcode' => $pu->barcode,
                            'conversion_rate' => $pu->conversion_rate,
                            'selling_price' => $pu->selling_price,
                            'is_base_unit' => $pu->is_base_unit,
                        ];
                    }),
                ]);
            }

            $generator = new BarcodeGeneratorHTML();
            $barcode = $generator->getBarcode($product->code, $generator::TYPE_CODE_128);
            return view('products.show', compact('product', 'barcode'));
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm: ' . $e->getMessage()
                ], 404);
            }
            return redirect()->route('products.index')->with('error', 'Không tìm thấy sản phẩm');
        }
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'unit', 'productUnits.unit']);
        
        $categories = Category::all();
        $units = Unit::all();
        $productUnits = $product->productUnits()->where('is_base_unit', false)->get();
        
        return view('products.edit', compact('product', 'categories', 'units', 'productUnits'));
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
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

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được cập nhật thành công!',
                    'product' => $product->load(['category', 'unit', 'productUnits.unit'])
                ]);
            }

            return redirect()
                ->route('products.index')
                ->with('success', 'Sản phẩm đã được cập nhật thành công!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi cập nhật sản phẩm: ' . $e->getMessage()
                ], 500);
            }
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

    public function ajaxUpdate(Request $request, $id)
    {
        $product = Product::with(['category', 'unit', 'productUnits.unit'])->findOrFail($id);
        
        Log::info('AJAX Update called', [
            'product_id' => $product->id,
            'request_data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
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

            DB::beginTransaction();

            $productData = [
                'name' => $request->name,
                'slug' => $request->slug ?? Str::slug($request->name),
                'code' => $request->code ?? $product->code,
                'product_code' => $request->product_code,
                'brand' => $request->brand,
                'location' => $request->location,
                'commission' => $request->commission,
                'weight' => $request->weight,
                'quantity' => $request->quantity,
                'buying_price' => $request->buying_price,
                'selling_price' => $request->selling_price,
                'quantity_alert' => $request->quantity_alert,
                'tax' => $request->tax,
                'tax_type' => $request->tax_type,
                'notes' => $request->notes,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
            ];

            $product->update($productData);
            Log::info('Product updated', ['product' => $product->toArray()]);

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

            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được cập nhật thành công!',
                'product' => $product->fresh(['category', 'unit', 'productUnits.unit'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật sản phẩm: ' . $e->getMessage()
            ], 500);
        }
    }
}
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
    private const CUSTOM_UNIT_SHORT_CODE = 'CUSTOM';
    
    private function validationRules($productId = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255|unique:products,code' . ($productId ? ",$productId" : ''),
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
            'product_units' => 'nullable|array',
            'product_units.*.custom_unit_name' => 'nullable|string|max:255',
            'product_units.*.unit_id' => 'nullable|integer|exists:units,id',
            'product_units.*.barcode' => 'nullable|string|max:255',
            'product_units.*.selling_price' => 'nullable|integer|min:0',
        ];
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit', 'productUnits.unit']);
        
        $this->applyFilters($query, $request);
        
        $products = $query->paginate(15);
        $categories = Category::all(['id', 'name']); 
        $units = Unit::all(['id', 'name']);
        
        return view('products.index', compact('products', 'categories', 'units'));
    }

    public function create(Request $request)
    {
        $categories = $request->has('category') 
            ? Category::whereSlug($request->get('category'))->get(['id', 'name'])
            : Category::all(['id', 'name']);
            
        $units = $request->has('unit')
            ? Unit::whereSlug($request->get('unit'))->get(['id', 'name'])
            : Unit::all(['id', 'name']);
        
        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate($this->validationRules());

        try {
            DB::beginTransaction();

            $product = $this->saveProduct($request);
            $this->handleImageUpload($request, $product);
            $this->createBaseUnit($product);
            
            if ($request->has('product_units')) {
                $this->createProductUnits($product, $request->product_units);
            }

            DB::commit();

            return redirect()
                ->route('products.index')
                ->with('success', 'Sản phẩm đã được tạo thành công!');

        } catch (\Exception $e) {
            return $this->handleError($e, 'Store', $request);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with(['category', 'unit', 'productUnits.unit'])->findOrFail($id);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json($this->formatProductResponse($product));
            }

            $generator = new BarcodeGeneratorHTML();
            $barcode = $generator->getBarcode($product->code, $generator::TYPE_CODE_128);
            
            return view('products.show', compact('product', 'barcode'));

        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
            }
            return redirect()->route('products.index')->with('error', 'Không tìm thấy sản phẩm');
        }
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'unit', 'productUnits.unit']);
        
        $categories = Category::all(['id', 'name']);
        $units = Unit::all(['id', 'name']);
        $productUnits = $product->productUnits()->where('is_base_unit', false)->get();
        
        return view('products.edit', compact('product', 'categories', 'units', 'productUnits'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate($this->validationRules($product->id));

        try {
            DB::beginTransaction();

            $this->updateProduct($request, $product);
            $this->handleImageUpload($request, $product);
            $this->syncProductUnits($product, $request);

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
            return $this->handleValidationError($e, $request);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Update', $request, $product->id);
        }
    }

    public function ajaxUpdate(Request $request, $id)
    {
        $product = Product::with(['category', 'unit', 'productUnits.unit'])->findOrFail($id);
        
        return $this->update($request, $product);
    }

    public function destroy(Product $product)
    {
        $this->deleteProductImage($product);
        $product->delete();
        
        return redirect()
            ->route('products.index')
            ->with('success', 'Sản phẩm đã được xóa thành công!');
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
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
    }

    private function saveProduct(Request $request, Product $product = null): Product
    {
        $data = $request->only([
            'name', 'slug', 'code', 'product_code', 'brand', 'location',
            'commission', 'weight', 'quantity', 'buying_price', 'selling_price',
            'quantity_alert', 'tax', 'tax_type', 'notes', 'category_id', 'unit_id',
        ]);
        
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['code'] = $data['code'] ?? $this->generateProductCode();
        
        return $product ? tap($product)->update($data) : Product::create($data);
    }

    private function updateProduct(Request $request, Product $product)
    {
        $this->saveProduct($request, $product);
    }

    private function handleImageUpload(Request $request, Product $product)
    {
        if (!$request->hasFile('product_image')) {
            return;
        }

        if ($product->product_image) {
            $this->deleteProductImage($product);
        }

        $image = $request->file('product_image');
        $imageName = time() . '.' . $image->extension();
        $path = $image->storeAs('products', $imageName, 'public');
        
        $product->update(['product_image' => $path]);
    }

    private function deleteProductImage(Product $product)
    {
        if (!$product->product_image) {
            return;
        }

        $imagePath = str_starts_with($product->product_image, 'products/')
            ? $product->product_image
            : 'products/' . $product->product_image;
            
        Storage::disk('public')->delete($imagePath);
    }

    private function formatProductResponse(Product $product): array
    {
        $productImage = null;
        if ($product->product_image) {
            $productImage = str_starts_with($product->product_image, 'products/')
                ? asset('storage/' . $product->product_image)
                : asset('storage/products/' . $product->product_image);
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'code' => $product->code,
            'product_code' => $product->product_code,
            'product_image' => $productImage,
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
            'product_units' => $product->productUnits->map(function ($pu) {
                return [
                    'id' => $pu->id,
                    'unit_id' => $pu->unit_id,
                    'unit' => $pu->unit,
                    'custom_unit_name' => $pu->custom_unit_name ?? $pu->unit->name ?? null,
                    'barcode' => $pu->barcode,
                    'selling_price' => $pu->selling_price,
                    'is_base_unit' => $pu->is_base_unit,
                ];
            }),
        ];
    }

    private function createBaseUnit(Product $product)
    {
        $product->productUnits()->create([
            'unit_id' => $product->unit_id,
            'custom_unit_name' => null,
            'barcode' => $product->code,
            'selling_price' => $product->selling_price,
            'is_base_unit' => 1,
        ]);
    }

    private function syncProductUnits(Product $product, Request $request)
    {
        $product->productUnits()->where('is_base_unit', 0)->delete();

        if ($request->has('product_units')) {
            $this->createProductUnits($product, $request->product_units);
        }

        $this->updateOrCreateBaseUnit($product);
    }

    private function createProductUnits(Product $product, array $unitDataArray)
    {
        $customUnit = Unit::where('short_code', self::CUSTOM_UNIT_SHORT_CODE)->first();
        $customUnitId = $customUnit?->id;

        foreach ($unitDataArray as $unitData) {
            $customName = trim($unitData['custom_unit_name'] ?? '');
            
            $unitId = !empty($unitData['unit_id']) ? $unitData['unit_id'] : $customUnitId;
            
            if (empty($unitId)) {
                Log::warning('Skipped product unit: no valid unit_id', ['unit_data' => $unitData]);
                continue;
            }
            
            if (empty($customName)) {
                $unit = Unit::find($unitId);
                $customName = $unit?->name ?? 'Đơn vị phụ';
            }

            $sellingPrice = !empty($unitData['selling_price']) 
                ? (int) str_replace(',', '', $unitData['selling_price']) 
                : $product->selling_price;

            $barcode = !empty($unitData['barcode']) 
                ? $unitData['barcode'] 
                : $this->generateUniqueBarcode($product->code);

            $product->productUnits()->create([
                'unit_id' => $unitId,
                'custom_unit_name' => $customName,
                'barcode' => $barcode,
                'selling_price' => $sellingPrice,
                'is_base_unit' => 0,
            ]);
        }
    }

    private function updateOrCreateBaseUnit(Product $product)
    {
        $baseUnit = $product->productUnits()->where('is_base_unit', 1)->first();

        if ($baseUnit) {
            $baseUnit->update([
                'unit_id' => $product->unit_id,
                'custom_unit_name' => null,
                'selling_price' => $product->selling_price,
            ]);
        } else {
            $this->createBaseUnit($product);
        }
    }

    private function generateProductCode(): string
    {
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $number = $lastProduct ? intval(substr($lastProduct->code, 3)) + 1 : 1;
        return 'PRD' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    private function generateUniqueBarcode(string $baseCode): string
    {
        do {
            $barcode = $baseCode . '-' . strtoupper(Str::random(6));
        } while (ProductUnit::where('barcode', $barcode)->exists());
        
        return $barcode;
    }

    private function handleError(\Exception $e, string $action, Request $request, $productId = null)
    {
        DB::rollBack();
        
        $logData = [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
        
        if ($productId) {
            $logData['product_id'] = $productId;
        }
        
        Log::error("Product {$action} Error", $logData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => "Lỗi khi {$action} sản phẩm: " . $e->getMessage()
            ], 500);
        }
        
        return back()->withInput()->with('error', "Lỗi khi {$action} sản phẩm: " . $e->getMessage());
    }

    private function handleValidationError(\Illuminate\Validation\ValidationException $e, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        }
        
        return back()->withErrors($e->errors())->withInput();
    }
}
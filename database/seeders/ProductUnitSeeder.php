<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductUnit;
use Illuminate\Database\Seeder;

class ProductUnitSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy các đơn vị
        $unitPiece = Unit::where('short_code', 'pc')->first();
        $unitBox = Unit::where('short_code', 'hop')->first();
        $unitCarton = Unit::where('short_code', 'thung')->first();

        // Seed cho từng sản phẩm
        $products = Product::all();

        foreach ($products as $product) {
            // Đơn vị cơ bản: Piece
            ProductUnit::create([
                'product_id' => $product->id,
                'unit_id' => $unitPiece->id,
                'barcode' => $product->code,
                'conversion_rate' => 1,
                'selling_price' => $product->selling_price * 100, // Convert to cents
                'is_base_unit' => true,
            ]);

            // Đơn vị phụ: Hộp (6 pieces)
            ProductUnit::create([
                'product_id' => $product->id,
                'unit_id' => $unitBox->id,
                'barcode' => $product->code . '-HOP',
                'conversion_rate' => 6,
                'selling_price' => (int)($product->selling_price * 6 * 100 * 0.95), // Giảm 5%
                'is_base_unit' => false,
            ]);

            // Đơn vị phụ: Thùng (24 pieces = 4 hộp)
            ProductUnit::create([
                'product_id' => $product->id,
                'unit_id' => $unitCarton->id,
                'barcode' => $product->code . '-THUNG',
                'conversion_rate' => 24,
                'selling_price' => (int)($product->selling_price * 24 * 100 * 0.90), // Giảm 10%
                'is_base_unit' => false,
            ]);
        }

        $this->command->info('✅ Đã seed ' . ($products->count() * 3) . ' product units!');
    }
}
<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = collect([
            [
                'name' => 'Meters',
                'slug' => 'meters',
                'short_code' => 'm'
            ],
            [
                'name' => 'Centimeters',
                'slug' => 'centimeters',
                'short_code' => 'cm'
            ],
            [
                'name' => 'Piece',
                'slug' => 'piece',
                'short_code' => 'pc'
            ],
            [
                'name' => 'Hộp',
                'slug' => 'hop',
                'short_code' => 'hop'
            ],
            [
                'name' => 'Chai',
                'slug' => 'chai',
                'short_code' => 'chai'
            ],
            [
                'name' => 'Cái',
                'slug' => 'cai',
                'short_code' => 'cai'
            ],
            [
                'name' => 'Lốc',
                'slug' => 'loc',
                'short_code' => 'loc'
            ],
            [
                'name' => 'Thùng',
                'slug' => 'thung',
                'short_code' => 'thung'
            ],
            [
                'name' => 'Kg',
                'slug' => 'kilogram',
                'short_code' => 'kg'
            ],
            [
                'name' => 'Gram',
                'slug' => 'gram',
                'short_code' => 'g'
            ],
            [
                'name' => 'Lít',
                'slug' => 'lit',
                'short_code' => 'l'
            ],
            [
                'name' => 'Ml',
                'slug' => 'milliliter',
                'short_code' => 'ml'
            ],
            [
                'name' => 'Gói',
                'slug' => 'goi',
                'short_code' => 'goi'
            ],
            [
                'name' => 'Bộ',
                'slug' => 'bo',
                'short_code' => 'bo'
            ],
            [
                'name' => 'Túi',
                'slug' => 'tui',
                'short_code' => 'tui'
            ],
            [
                'name' => 'Hũ',
                'slug' => 'hu',
                'short_code' => 'hu'
            ],
        ]);

        $units->each(function ($unit){
            // Thay đổi ở đây: Dùng updateOrCreate thay vì insert
            Unit::updateOrCreate(
                ['short_code' => $unit['short_code']], // Tìm theo short_code
                $unit // Dữ liệu để tạo/cập nhật
            );
        });
    }
}
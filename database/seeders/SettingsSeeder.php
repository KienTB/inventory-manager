<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'store_name' => 'Cửa hàng tạp hóa ABC',
            'store_email' => 'info@cuahangabc.vn',
            'store_phone' => '0901 234 567',
            'store_address' => '123 Đường ABC, Quận 1, TP.HCM',
            'store_website' => 'www.cuahangabc.vn',
            'store_slogan' => 'Phục vụ 24/7',
            'store_logo' => null,
        ]);
    }
}

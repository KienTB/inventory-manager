<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'store_email',
        'store_phone',
        'store_address',
        'store_website',
        'store_slogan',
        'store_logo',
    ];

    /**
     * Get the first setting record or create default if none exists
     */
    public static function getSettings()
    {
        $settings = self::first();

        if (!$settings) {
            $settings = self::create([
                'store_name' => 'Cửa hàng tạp hóa',
                'store_email' => null,
                'store_phone' => null,
                'store_address' => null,
                'store_website' => null,
                'store_slogan' => 'Phục vụ 24/7',
                'store_logo' => null,
            ]);
        }

        return $settings;
    }
}

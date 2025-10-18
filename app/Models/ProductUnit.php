<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'unit_id',
        'barcode',
        'conversion_rate',
        'selling_price',
        'is_base_unit',
    ];

    protected $casts = [
        'is_base_unit' => 'boolean',
        'conversion_rate' => 'integer',
        'selling_price' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public static function generateBarcode($productCode, $index = null)
    {
        if ($index === null) {
            return $productCode;
        }
        return $productCode . '-' . $index;
    }

    public function calculateTotalPrice($quantity)
    {
        return $this->selling_price * $quantity;
    }
}
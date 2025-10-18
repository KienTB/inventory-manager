<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('barcode')->unique()->nullable();
            $table->integer('conversion_rate')->default(1)->comment('Tỷ lệ quy đổi so với đơn vị cơ bản');
            $table->integer('selling_price')->comment('Giá bán theo đơn vị này');
            $table->boolean('is_base_unit')->default(false)->comment('Đánh dấu đơn vị cơ bản');
            $table->timestamps();
            
            $table->index(['product_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
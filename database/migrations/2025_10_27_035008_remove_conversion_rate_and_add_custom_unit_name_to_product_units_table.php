<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn('conversion_rate');  
            $table->string('custom_unit_name', 255)->nullable()->after('unit_id');  // Thêm cột custom_unit_name sau unit_id
        });
    }

    public function down()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn('custom_unit_name');  
            $table->integer('conversion_rate')->default(1)->after('barcode')->comment('Tỷ lệ quy đổi so với đơn vị cơ bản');  // Khôi phục cột cũ
        });
    }
};
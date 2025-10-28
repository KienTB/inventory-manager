<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('units')
            ->where('short_code', 'CUSTOM')
            ->exists();

        if (!$exists) {
            DB::table('units')->insert([
                'name' => 'Đơn vị tùy chỉnh',
                'short_code' => 'CUSTOM',
                'slug' => Str::slug('Đơn vị tùy chỉnh'), 
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('units')
            ->where('short_code', 'CUSTOM')
            ->delete();
    }
};
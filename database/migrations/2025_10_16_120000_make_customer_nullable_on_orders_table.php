<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop FK if it exists (default Laravel name)
            try {
                $table->dropForeign(['customer_id']);
            } catch (Throwable $e) {
                // ignore if FK not present
            }
        });

        // Make customer_id nullable without requiring doctrine/dbal
        DB::statement('ALTER TABLE `orders` MODIFY `customer_id` BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        // Revert to NOT NULL (will fail if there are NULL values)
        DB::statement('ALTER TABLE `orders` MODIFY `customer_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('orders', function (Blueprint $table) {
            // Optionally, re-add FK if needed and customers table exists
            try {
                $table
                    ->foreign('customer_id')
                    ->references('id')
                    ->on('customers')
                    ->restrictOnDelete();
            } catch (Throwable $e) {
                // ignore
            }
        });
    }
};

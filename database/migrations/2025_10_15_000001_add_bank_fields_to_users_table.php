<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bank_name', 100)->nullable()->after('email');
            $table->string('bank_bin', 10)->nullable()->after('bank_name');
            $table->string('bank_account_number', 50)->nullable()->after('bank_bin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_bin', 'bank_account_number']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_operasional', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('jam_tutup');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_operasional', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};

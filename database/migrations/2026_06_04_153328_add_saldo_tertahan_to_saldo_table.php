<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('saldo', function (Blueprint $table) {
            $table->decimal('saldo_tertahan', 12, 2)->default(0)->after('jumlah_saldo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saldo', function (Blueprint $table) {
            $table->dropColumn('saldo_tertahan');
        });
    }
};

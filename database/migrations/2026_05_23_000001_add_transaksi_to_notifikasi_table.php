<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->foreignId('id_transaksi')
                ->nullable()
                ->after('id_pengguna')
                ->constrained('transaksi_penyetoran')
                ->nullOnDelete();

            $table->boolean('memerlukan_konfirmasi')
                ->default(false)
                ->after('status_notifikasi');
        });
    }

    public function down(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->dropForeign(['id_transaksi']);
            $table->dropColumn(['id_transaksi', 'memerlukan_konfirmasi']);
        });
    }
};

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
        Schema::create('transaksi_penyetoran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_nasabah')->constrained('nasabah')->cascadeOnDelete();
            $table->foreignId('id_bank_sampah')->constrained('bank_sampah')->cascadeOnDelete();
            $table->foreignId('id_petugas')->constrained('petugas')->cascadeOnDelete();
            $table->datetime('tgl_setor')->useCurrent();
            $table->decimal('total_berat_kg', 8, 2)->default(0);
            $table->integer('total_koin')->default(0);
            $table->enum('status', ['pending', 'diproses', 'selesai', 'dibatalkan'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_penyetoran');
    }
};

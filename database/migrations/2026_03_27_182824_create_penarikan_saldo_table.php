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
        Schema::create('penarikan_saldo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengguna')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('id_saldo')->constrained('saldo')->cascadeOnDelete();
            $table->decimal('jumlah_tarik', 12, 2);
            $table->string('metode_bayar');
            $table->string('no_rekening');
            $table->datetime('tgl_penarikan')->useCurrent();
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penarikan_saldo');
    }
};

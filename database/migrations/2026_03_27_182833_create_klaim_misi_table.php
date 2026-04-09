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
        Schema::create('klaim_misi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengguna')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('id_misi')->constrained('misi')->cascadeOnDelete();
            $table->datetime('tgl_klaim')->useCurrent();
            $table->enum('status_klaim', ['pending', 'valid', 'invalid'])->default('pending');
            $table->integer('koin_diterima')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klaim_misi');
    }
};

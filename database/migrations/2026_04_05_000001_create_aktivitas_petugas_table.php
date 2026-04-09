<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aktivitas_petugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_petugas')->constrained('petugas')->onDelete('cascade');
            $table->string('jenis_aktivitas');
            $table->text('deskripsi')->nullable();
            $table->json('data_lampiran')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aktivitas_petugas');
    }
};
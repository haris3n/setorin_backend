okee<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('pengguna')->onDelete('cascade');
            $table->string('exporter');
            $table->integer('total_rows')->default(0);
            $table->integer('processed_rows')->default(0);
            $table->string('file_disk')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('file_size')->nullable();
            $table->text('exception')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exports');
    }
};
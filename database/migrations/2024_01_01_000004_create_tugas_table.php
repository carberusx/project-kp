<?php
// FILE: database/migrations/2024_01_01_000004_create_tugas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('deadline');
            $table->enum('tipe', ['laporan', 'proyek', 'evaluasi', 'lainnya'])->default('laporan');
            $table->string('file_tugas')->nullable(); // template dari admin
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->text('catatan')->nullable();
            $table->enum('status', ['dikumpulkan', 'dinilai', 'revisi'])->default('dikumpulkan');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->dateTime('dikumpulkan_at');
            $table->timestamps();

            $table->unique(['tugas_id', 'user_id']); // sekali kumpul per tugas per user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
        Schema::dropIfExists('tugas');
    }
};

<?php
// FILE: database/migrations/2024_01_01_000002_create_pendaftarans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('universitas');
            $table->string('jurusan');
            $table->text('motivasi');
            $table->string('file_cv')->nullable();
            $table->string('file_transkrip')->nullable();
            $table->enum('status', ['menunggu', 'wawancara', 'diterima', 'ditolak'])->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};

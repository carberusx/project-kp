<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'super_admin' ke enum role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin','admin','mahasiswa') NOT NULL DEFAULT 'mahasiswa'");
    }

    public function down(): void
    {
        // Rollback: hapus 'super_admin' (pastikan tidak ada user super_admin dulu)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','mahasiswa') NOT NULL DEFAULT 'mahasiswa'");
    }
};

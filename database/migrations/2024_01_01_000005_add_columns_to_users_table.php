<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'mahasiswa'])->default('mahasiswa')->after('password');
            $table->string('nim')->nullable()->after('role');
            $table->string('universitas')->nullable()->after('nim');
            $table->string('jurusan')->nullable()->after('universitas');
            $table->string('telepon')->nullable()->after('jurusan');
            $table->string('foto')->nullable()->after('telepon');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'nim', 'universitas', 'jurusan', 'telepon', 'foto']);
        });
    }
};

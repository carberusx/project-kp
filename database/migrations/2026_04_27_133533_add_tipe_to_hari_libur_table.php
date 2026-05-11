<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hari_liburs', function (Blueprint $table) {
            $table->enum('tipe', ['libur', 'kerja_khusus'])->default('libur')->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('hari_liburs', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};

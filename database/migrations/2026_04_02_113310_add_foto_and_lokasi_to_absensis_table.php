<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->string('foto_masuk')->nullable()->after('jam_masuk');
            $table->string('foto_keluar')->nullable()->after('jam_keluar');
            $table->string('latitude_masuk')->nullable()->after('foto_masuk');
            $table->string('longitude_masuk')->nullable()->after('latitude_masuk');
            $table->string('latitude_keluar')->nullable()->after('foto_keluar');
            $table->string('longitude_keluar')->nullable()->after('latitude_keluar');
            $table->string('alamat_masuk')->nullable()->after('longitude_masuk');
            $table->string('alamat_keluar')->nullable()->after('longitude_keluar');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn([
                'foto_masuk', 'foto_keluar',
                'latitude_masuk', 'longitude_masuk',
                'latitude_keluar', 'longitude_keluar',
                'alamat_masuk', 'alamat_keluar',
            ]);
        });
    }
};

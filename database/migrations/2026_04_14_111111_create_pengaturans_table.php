<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('kunci')->unique();
            $table->text('nilai')->nullable();
            $table->timestamps();
        });
        
        // Insert default values
        DB::table('pengaturans')->insert([
            ['kunci' => 'batas_max_pendaftar', 'nilai' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['kunci' => 'status_pendaftaran', 'nilai' => 'buka', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->string('alat_gambar')->nullable()->after('alat_stok');
        });
    }

    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->dropColumn('alat_gambar');
        });
    }
};
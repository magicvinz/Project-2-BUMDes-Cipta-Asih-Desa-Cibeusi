<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('Produk_Khas', function (Blueprint $table) {
            $table->foreignId('wisata_id')->nullable()->after('id')->constrained('Wisata')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Produk_Khas', function (Blueprint $table) {
            $table->dropForeign(['wisata_id']);
            $table->dropColumn('wisata_id');
        });
    }
};

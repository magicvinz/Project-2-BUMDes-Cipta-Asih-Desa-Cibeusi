<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks to rename safely (MySQL Specific)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::rename('users', 'User');
        
        Schema::rename('wisata', '_wisata_tmp');
        Schema::rename('_wisata_tmp', 'Wisata');
        
        Schema::rename('tiket', '_tiket_tmp');
        Schema::rename('_tiket_tmp', 'Tiket');
        
        Schema::rename('reviews', 'Ulasan');
        
        Schema::rename('produk_khas', '_produk_khas_tmp');
        Schema::rename('_produk_khas_tmp', 'Produk_Khas');
        
        Schema::rename('penjualan_offlines', 'Penjualan_Offline');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::rename('User', 'users');
        Schema::rename('Wisata', 'wisata');
        Schema::rename('Tiket', 'tiket');
        Schema::rename('Ulasan', 'reviews');
        Schema::rename('Produk_Khas', 'produk_khas');
        Schema::rename('Penjualan_Offline', 'penjualan_offlines');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};

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
        // 1. Tambah kolom JSON
        Schema::table('wisata', function (Blueprint $table) {
            $table->json('galleries')->nullable()->after('gambar');
        });
        Schema::table('produk_khas', function (Blueprint $table) {
            $table->json('galleries')->nullable()->after('gambar');
        });

        // 2. Migrasi data lama ke JSON array
        $wisataGalleries = \Illuminate\Support\Facades\DB::table('wisata_galleries')->get()->groupBy('wisata_id');
        foreach ($wisataGalleries as $wisataId => $images) {
            $galleryData = $images->map(function ($item) {
                return ['image' => $item->image, 'caption' => $item->caption];
            })->toJson();
            \Illuminate\Support\Facades\DB::table('wisata')->where('id', $wisataId)->update(['galleries' => $galleryData]);
        }

        $produkGalleries = \Illuminate\Support\Facades\DB::table('produk_khas_galleries')->get()->groupBy('produk_khas_id');
        foreach ($produkGalleries as $produkId => $images) {
            $galleryData = $images->map(function ($item) {
                return ['image' => $item->image, 'caption' => $item->caption];
            })->toJson();
            \Illuminate\Support\Facades\DB::table('produk_khas')->where('id', $produkId)->update(['galleries' => $galleryData]);
        }

        // 3. Hapus tabel lama
        Schema::dropIfExists('wisata_galleries');
        Schema::dropIfExists('produk_khas_galleries');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan tabel (meski tanpa data) untuk rollback
        Schema::create('wisata_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wisata_id')->constrained('wisata')->onDelete('cascade');
            $table->string('image');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('produk_khas_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_khas_id')->constrained('produk_khas')->onDelete('cascade');
            $table->string('image');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::table('wisata', function (Blueprint $table) {
            $table->dropColumn('galleries');
        });
        Schema::table('produk_khas', function (Blueprint $table) {
            $table->dropColumn('galleries');
        });
    }
};

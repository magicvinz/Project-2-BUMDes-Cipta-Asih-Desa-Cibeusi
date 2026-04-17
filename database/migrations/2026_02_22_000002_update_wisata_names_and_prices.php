<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('wisata')->where('slug', 'curug-cibarebeuy')->update([
            'nama' => 'Curug Cibareubeuy',
            'slug' => 'curug-cibareubeuy',
            'harga_tiket' => 10000,
        ]);
        DB::table('wisata')->where('slug', 'bukit-panineungan')->update([
            'nama' => 'Bukit Panineungan/Spot Foto',
            'slug' => 'bukit-panineungan-spot-foto',
            'harga_tiket' => 10000,
        ]);
        DB::table('wisata')->where('slug', 'puncak-pasir-ipis')->update([
            'harga_tiket' => 15000,
        ]);
    }

    public function down(): void
    {
        DB::table('wisata')->where('slug', 'curug-cibareubeuy')->update([
            'nama' => 'Curug Cibarebeuy',
            'slug' => 'curug-cibarebeuy',
            'harga_tiket' => 15000,
        ]);
        DB::table('wisata')->where('slug', 'bukit-panineungan-spot-foto')->update([
            'nama' => 'Bukit Panineungan',
            'slug' => 'bukit-panineungan',
            'harga_tiket' => 25000,
        ]);
        DB::table('wisata')->where('slug', 'puncak-pasir-ipis')->update([
            'harga_tiket' => 20000,
        ]);
    }
};

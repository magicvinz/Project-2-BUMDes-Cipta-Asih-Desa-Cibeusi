<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Pastikan nama dan harga wisata sesuai: Curug 10k, Bukit 10k, Puncak 15k.
     * Aman dijalankan berulang (idempotent).
     */
    public function up(): void
    {
        DB::table('wisata')->whereIn('slug', ['curug-cibarebeuy', 'curug-cibareubeuy'])->update([
            'nama' => 'Curug Cibareubeuy',
            'slug' => 'curug-cibareubeuy',
            'harga_tiket' => 10000,
        ]);
        DB::table('wisata')->whereIn('slug', ['bukit-panineungan', 'bukit-panineungan-spot-foto'])->update([
            'nama' => 'Bukit Panineungan/Spot Foto',
            'slug' => 'bukit-panineungan-spot-foto',
            'harga_tiket' => 10000,
        ]);
        DB::table('wisata')->where('slug', 'puncak-pasir-ipis')->update([
            'nama' => 'Puncak Pasir Ipis',
            'harga_tiket' => 15000,
        ]);
    }

    public function down(): void
    {
        // optional: revert to old values if needed
    }
};

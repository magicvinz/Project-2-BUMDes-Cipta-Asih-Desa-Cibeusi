<?php

namespace Database\Seeders;

use App\Models\ProdukKhas;
use App\Models\Wisata;
use Illuminate\Database\Seeder;

class ProdukKhasSeeder extends Seeder
{
    public function run(): void
    {
        // Hubungkan produk ke Curug Cibareubeuy sebagai default sesuai relasi baru
        $wisata = Wisata::where('slug', 'curug-cibarebeuy')
            ->orWhere('slug', 'curug-cibareubeuy')
            ->first();
        $wisataId = $wisata ? $wisata->id : null;

        $data = [
            [
                'nama' => 'Gula Merah',
                'keterangan' => 'Gula merah murni hasil olahan masyarakat desa.',
                'gambar' => null,
                'urutan' => 1,
                'id_wisata' => $wisataId,
            ],
            [
                'nama' => 'Madu',
                'keterangan' => 'Madu murni hasil panen madu hutan masyarakat desa.',
                'gambar' => null,
                'urutan' => 2,
                'id_wisata' => $wisataId,
            ],
            [
                'nama' => 'Beras Hitam',
                'keterangan' => 'Beras hitam organik dengan kualitas premium dan bernutrisi tinggi.',
                'gambar' => null,
                'urutan' => 3,
                'id_wisata' => $wisataId,
            ],
            [
                'nama' => 'Kulang - Kaling',
                'keterangan' => 'Kulang - kaling segar alami hasil panen lokal yang cocok untuk hidangan.',
                'gambar' => null,
                'urutan' => 4,
                'id_wisata' => $wisataId,
            ],
        ];

        foreach ($data as $item) {
            ProdukKhas::create($item);
        }
    }
}

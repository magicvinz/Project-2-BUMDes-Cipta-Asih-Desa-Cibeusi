<?php

namespace Database\Seeders;

use App\Models\Wisata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WisataSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Curug Cibareubeuy',
                'harga_tiket' => 10000,
                'deskripsi' => 'Air terjun alami dengan pemandangan hijau yang menyejukkan.',
            ],
            [
                'nama' => 'Puncak Pasir Ipis',
                'harga_tiket' => 15000,
                'deskripsi' => 'Puncak dengan panorama alam yang memukau dengan ketinggian 1307mdpl.',
            ],
            [
                'nama' => 'Bukit Panineungan/Spot Foto',
                'harga_tiket' => 10000,
                'deskripsi' => 'Bukit untuk spot foto dengan pemandangan Curug Cibareubeuy.',
            ],
        ];

        foreach ($data as $item) {
            Wisata::create([
                'nama' => $item['nama'],
                'slug' => Str::slug($item['nama']),
                'harga_tiket' => $item['harga_tiket'],
                'deskripsi' => $item['deskripsi'],
            ]);
        }
    }
}

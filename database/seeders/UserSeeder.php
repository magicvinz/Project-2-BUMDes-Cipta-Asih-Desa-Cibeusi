<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wisata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Pengelola BUMDes (1 akun)
        User::create([
            'name' => 'Pengelola BUMDes',
            'email' => 'pengelola@siasih.com',
            'password' => Hash::make('password'),
            'role' => 'pengelola_bumdes',
            'id_wisata' => null,
        ]);

        $wisata = Wisata::all();

        // Admin Curug Cibareubeuy
        User::create([
            'name' => 'Admin Curug Cibareubeuy',
            'email' => 'admin.curug@siasih.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'id_wisata' => $wisata->where('slug', 'curug-cibareubeuy')->first()->id,
        ]);

        // Admin Puncak Pasir Ipis
        User::create([
            'name' => 'Admin Puncak Pasir Ipis',
            'email' => 'admin.puncak@siasih.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'id_wisata' => $wisata->where('slug', 'puncak-pasir-ipis')->first()->id,
        ]);

        // Admin Bukit Panineungan/Spot Foto
        User::create([
            'name' => 'Admin Bukit Panineungan',
            'email' => 'admin.bukit@siasih.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'id_wisata' => $wisata->where('slug', 'bukit-panineunganspot-foto')->first()->id ?? null,
        ]);

        // Pengunjung demo
        User::create([
            'name' => 'Pengunjung Demo',
            'email' => 'pengunjung@siasih.com',
            'password' => Hash::make('password'),
            'role' => 'pengunjung',
            'id_wisata' => null,
        ]);
    }
}

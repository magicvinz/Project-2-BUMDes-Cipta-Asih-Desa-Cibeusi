<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Wisata', function (Blueprint $table) {
            // Harga camping (0 = tidak ada opsi camping)
            $table->unsignedInteger('harga_camping')->default(0)->after('harga_tiket');
        });

        // Set nilai awal dari konstanta lama untuk Curug Cibarebeuy
        \DB::table('Wisata')
            ->whereIn('slug', ['curug-cibarebeuy', 'curug-cibareubeuy'])
            ->update(['harga_camping' => 25000]);
    }

    public function down(): void
    {
        Schema::table('Wisata', function (Blueprint $table) {
            $table->dropColumn('harga_camping');
        });
    }
};

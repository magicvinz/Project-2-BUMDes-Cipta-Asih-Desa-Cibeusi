<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->string('camping', 20)->nullable()->after('tanggal_berkunjung')->comment('Ya/Tidak - hanya untuk Curug Cibarebeuy');
        });
    }

    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->dropColumn('camping');
        });
    }
};

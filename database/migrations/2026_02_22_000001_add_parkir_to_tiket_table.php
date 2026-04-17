<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            if (!Schema::hasColumn('tiket', 'parkir_tipe')) {
                $table->string('parkir_tipe', 30)->nullable()->after('camping');
            }
            if (!Schema::hasColumn('tiket', 'parkir_harga')) {
                $table->decimal('parkir_harga', 12, 0)->default(0)->after('parkir_tipe');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->dropColumn(['parkir_tipe', 'parkir_harga']);
        });
    }
};

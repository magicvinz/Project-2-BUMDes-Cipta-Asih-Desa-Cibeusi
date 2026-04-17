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
        Schema::create('penjualan_offlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wisata_id')->constrained('wisata')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah_tiket');
            $table->decimal('total_pendapatan', 12, 0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_offlines');
    }
};

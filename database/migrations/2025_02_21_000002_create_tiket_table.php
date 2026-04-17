<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wisata_id')->constrained('wisata')->cascadeOnDelete();
            $table->string('kode_tiket', 20)->unique();
            $table->integer('jumlah')->default(1);
            $table->decimal('total_harga', 12, 0);
            $table->enum('status', ['pending', 'paid', 'used', 'expired', 'cancelled'])->default('pending');
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->date('tanggal_berkunjung');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['wisata_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('tanggal_berkunjung');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket');
    }
};

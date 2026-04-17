<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi lengkap SI-ASIH.
 * Menjalankan: php artisan migrate:fresh --seed
 * Pastikan tabel users sudah ada (migrasi default Laravel).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel wisata
        if (! Schema::hasTable('wisata')) {
            Schema::create('wisata', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('slug')->unique();
                $table->decimal('harga_tiket', 12, 0);
                $table->text('deskripsi')->nullable();
                $table->string('gambar')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tambah kolom role ke users (jika belum ada)
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 20)->default('pengunjung')->after('email');
            });
        }

        // 3. Tambah kolom wisata_id ke users (setelah wisata ada)
        if (Schema::hasTable('users') && Schema::hasTable('wisata') && ! Schema::hasColumn('users', 'wisata_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('wisata_id')->nullable()->after('role');
                $table->foreign('wisata_id')->references('id')->on('wisata')->nullOnDelete();
            });
        }

        // 4. Buat tabel tiket
        if (! Schema::hasTable('tiket')) {
            Schema::create('tiket', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('wisata_id')->constrained('wisata')->cascadeOnDelete();
                $table->string('kode_tiket', 30)->unique();
                $table->integer('jumlah')->default(1);
                $table->decimal('total_harga', 12, 0);
                $table->string('status', 20)->default('pending');
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
    }

    public function down(): void
    {
        if (Schema::hasTable('tiket')) {
            Schema::dropIfExists('tiket');
        }
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'wisata_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['wisata_id']);
                $table->dropColumn('wisata_id');
            });
        }
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
        Schema::dropIfExists('wisata');
    }
};

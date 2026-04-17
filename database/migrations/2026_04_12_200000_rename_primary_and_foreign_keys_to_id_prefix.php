<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Konvensi: PK id_<tabel>, FK id_<tabel> (contoh id_wisata, id_user, id_tiket).
 * Hanya MySQL (sesuai proyek); jalankan setelah rename_core_tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            throw new \RuntimeException('Migration rename PK/FK ini hanya mendukung MySQL.');
        }

        if (Schema::hasColumn('Wisata', 'id_wisata')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->dropForeignKeys();

        $this->renamePrimaryKeys();
        $this->renameForeignKeyColumns();

        $this->addForeignKeys();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (! Schema::hasColumn('Wisata', 'id_wisata')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->dropForeignKeysNew();

        $this->renameForeignKeyColumnsRevert();
        $this->renamePrimaryKeysRevert();

        $this->addForeignKeysOld();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function dropForeignKeys(): void
    {
        foreach (['User', 'Tiket', 'Ulasan', 'Produk_Khas', 'Penjualan_Offline', 'activity_logs'] as $table) {
            $this->dropAllForeignKeysOnTable($table);
        }
    }

    /**
     * Menghapus semua FK keluar dari tabel (nama constraint di MySQL tidak selalu sama dengan tebakan Laravel).
     */
    private function dropAllForeignKeysOnTable(string $logicalTableName): void
    {
        $db = Schema::getConnection()->getDatabaseName();
        $actual = $this->resolveTableName($db, $logicalTableName);
        if ($actual === null) {
            return;
        }
        $constraints = DB::select(
            'SELECT DISTINCT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$db, $actual]
        );
        foreach ($constraints as $c) {
            DB::statement('ALTER TABLE `'.$actual.'` DROP FOREIGN KEY `'.$c->CONSTRAINT_NAME.'`');
        }
    }

    private function resolveTableName(string $database, string $logical): ?string
    {
        $rows = DB::select(
            'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?',
            [$database]
        );
        foreach ($rows as $r) {
            if (strcasecmp($r->TABLE_NAME, $logical) === 0) {
                return $r->TABLE_NAME;
            }
        }

        return null;
    }

    private function renamePrimaryKeys(): void
    {
        Schema::table('User', fn (Blueprint $t) => $t->renameColumn('id', 'id_user'));
        Schema::table('Wisata', fn (Blueprint $t) => $t->renameColumn('id', 'id_wisata'));
        Schema::table('Tiket', fn (Blueprint $t) => $t->renameColumn('id', 'id_tiket'));
        Schema::table('Ulasan', fn (Blueprint $t) => $t->renameColumn('id', 'id_ulasan'));
        Schema::table('Produk_Khas', fn (Blueprint $t) => $t->renameColumn('id', 'id_produk_khas'));
        Schema::table('Penjualan_Offline', fn (Blueprint $t) => $t->renameColumn('id', 'id_penjualan_offline'));
        Schema::table('activity_logs', fn (Blueprint $t) => $t->renameColumn('id', 'id_activity_log'));
    }

    private function renameForeignKeyColumns(): void
    {
        Schema::table('User', fn (Blueprint $t) => $t->renameColumn('wisata_id', 'id_wisata'));
        Schema::table('Tiket', function (Blueprint $t) {
            $t->renameColumn('user_id', 'id_user');
            $t->renameColumn('wisata_id', 'id_wisata');
        });
        Schema::table('Ulasan', function (Blueprint $t) {
            $t->renameColumn('user_id', 'id_user');
            $t->renameColumn('wisata_id', 'id_wisata');
            $t->renameColumn('tiket_id', 'id_tiket');
        });
        Schema::table('Produk_Khas', fn (Blueprint $t) => $t->renameColumn('wisata_id', 'id_wisata'));
        Schema::table('Penjualan_Offline', function (Blueprint $t) {
            $t->renameColumn('wisata_id', 'id_wisata');
            $t->renameColumn('created_by', 'id_user');
        });
        Schema::table('activity_logs', fn (Blueprint $t) => $t->renameColumn('user_id', 'id_user'));
    }

    private function addForeignKeys(): void
    {
        Schema::table('User', function (Blueprint $table) {
            $table->foreign('id_wisata')->references('id_wisata')->on('Wisata')->nullOnDelete();
        });
        Schema::table('Tiket', function (Blueprint $table) {
            $table->foreign('id_user')->references('id_user')->on('User')->cascadeOnDelete();
            $table->foreign('id_wisata')->references('id_wisata')->on('Wisata')->cascadeOnDelete();
        });
        Schema::table('Ulasan', function (Blueprint $table) {
            $table->foreign('id_user')->references('id_user')->on('User')->cascadeOnDelete();
            $table->foreign('id_wisata')->references('id_wisata')->on('Wisata')->cascadeOnDelete();
            $table->foreign('id_tiket')->references('id_tiket')->on('Tiket')->nullOnDelete();
        });
        Schema::table('Produk_Khas', function (Blueprint $table) {
            $table->foreign('id_wisata')->references('id_wisata')->on('Wisata')->cascadeOnDelete();
        });
        Schema::table('Penjualan_Offline', function (Blueprint $table) {
            $table->foreign('id_wisata')->references('id_wisata')->on('Wisata')->cascadeOnDelete();
            $table->foreign('id_user')->references('id_user')->on('User')->nullOnDelete();
        });
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreign('id_user')->references('id_user')->on('User')->nullOnDelete();
        });
    }

    private function dropForeignKeysNew(): void
    {
        foreach (['User', 'Tiket', 'Ulasan', 'Produk_Khas', 'Penjualan_Offline', 'activity_logs'] as $table) {
            $this->dropAllForeignKeysOnTable($table);
        }
    }

    private function renameForeignKeyColumnsRevert(): void
    {
        Schema::table('User', fn (Blueprint $t) => $t->renameColumn('id_wisata', 'wisata_id'));
        Schema::table('Tiket', function (Blueprint $t) {
            $t->renameColumn('id_user', 'user_id');
            $t->renameColumn('id_wisata', 'wisata_id');
        });
        Schema::table('Ulasan', function (Blueprint $t) {
            $t->renameColumn('id_user', 'user_id');
            $t->renameColumn('id_wisata', 'wisata_id');
            $t->renameColumn('id_tiket', 'tiket_id');
        });
        Schema::table('Produk_Khas', fn (Blueprint $t) => $t->renameColumn('id_wisata', 'wisata_id'));
        Schema::table('Penjualan_Offline', function (Blueprint $t) {
            $t->renameColumn('id_wisata', 'wisata_id');
            $t->renameColumn('id_user', 'created_by');
        });
        Schema::table('activity_logs', fn (Blueprint $t) => $t->renameColumn('id_user', 'user_id'));
    }

    private function renamePrimaryKeysRevert(): void
    {
        Schema::table('User', fn (Blueprint $t) => $t->renameColumn('id_user', 'id'));
        Schema::table('Wisata', fn (Blueprint $t) => $t->renameColumn('id_wisata', 'id'));
        Schema::table('Tiket', fn (Blueprint $t) => $t->renameColumn('id_tiket', 'id'));
        Schema::table('Ulasan', fn (Blueprint $t) => $t->renameColumn('id_ulasan', 'id'));
        Schema::table('Produk_Khas', fn (Blueprint $t) => $t->renameColumn('id_produk_khas', 'id'));
        Schema::table('Penjualan_Offline', fn (Blueprint $t) => $t->renameColumn('id_penjualan_offline', 'id'));
        Schema::table('activity_logs', fn (Blueprint $t) => $t->renameColumn('id_activity_log', 'id'));
    }

    private function addForeignKeysOld(): void
    {
        Schema::table('User', function (Blueprint $table) {
            $table->foreign('wisata_id')->references('id')->on('Wisata')->nullOnDelete();
        });
        Schema::table('Tiket', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('User')->cascadeOnDelete();
            $table->foreign('wisata_id')->references('id')->on('Wisata')->cascadeOnDelete();
        });
        Schema::table('Ulasan', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('User')->cascadeOnDelete();
            $table->foreign('wisata_id')->references('id')->on('Wisata')->cascadeOnDelete();
            $table->foreign('tiket_id')->references('id')->on('Tiket')->nullOnDelete();
        });
        Schema::table('Produk_Khas', function (Blueprint $table) {
            $table->foreign('wisata_id')->references('id')->on('Wisata')->cascadeOnDelete();
        });
        Schema::table('Penjualan_Offline', function (Blueprint $table) {
            $table->foreign('wisata_id')->references('id')->on('Wisata')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('User')->nullOnDelete();
        });
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('User')->nullOnDelete();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan opsi status 'Dibatalkan' ke dalam kolom enum status di tabel orders
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('Pending', 'Diproses', 'Selesai', 'Dibatalkan') NOT NULL DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('Pending', 'Diproses', 'Selesai') NOT NULL DEFAULT 'Pending'");
    }
};

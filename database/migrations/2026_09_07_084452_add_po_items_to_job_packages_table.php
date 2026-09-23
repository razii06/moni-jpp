<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            // Kolom JSON untuk menyimpan daftar PO (Nama, No PO, Harga)
            $table->json('po_items')->nullable()->after('no_po');
        });
    }

    public function down(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            $table->dropColumn('po_items');
        });
    }
};
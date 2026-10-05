<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            // Presisi 15 digit angka dengan 2 desimal menampung hingga Rp 999.999.999.999,99 (999 Miliar)
            $table->decimal('owner_estimate', 15, 2)->nullable()->change();
            $table->decimal('final_harga', 15, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            $table->decimal('owner_estimate', 11, 2)->nullable()->change();
            $table->decimal('final_harga', 11, 2)->nullable()->change();
        });
    }
};
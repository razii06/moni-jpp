<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            // Menambahkan kolom periode (tipe string, boleh kosong/nullable)
            $table->string('periode')->nullable()->after('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            $table->dropColumn('periode');
        });
    }
};
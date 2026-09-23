<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            $table->string('permintaan_dari')->nullable()->after('job_package');
        });
    }

    public function down(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            $table->dropColumn('permintaan_dari');
        });
    }
};
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
            Schema::table('job_packages', function (Blueprint $table) {
                // Default 'public' memastikan semua data lama tetap muncul dan tidak error
                $table->string('visibility')->default('public')->after('status');
            });
        }

        public function down(): void
        {
            Schema::table('job_packages', function (Blueprint $table) {
                $table->dropColumn('visibility');
            });
        }
};

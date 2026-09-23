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
        if (!Schema::hasColumn('job_packages', 'google_drive_folder_id')) {
            Schema::table('job_packages', function (Blueprint $table) {
                $table->string('google_drive_folder_id')->nullable()->after('keterangan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('job_packages', 'google_drive_folder_id')) {
            Schema::table('job_packages', function (Blueprint $table) {
                $table->dropColumn('google_drive_folder_id');
            });
        }
    }
};
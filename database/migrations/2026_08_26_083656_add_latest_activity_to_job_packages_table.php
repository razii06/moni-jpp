<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            $table->text('latest_activity')->nullable()->after('no_po');
        });
    }

    public function down(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            $table->dropColumn('latest_activity');
        });
    }
};
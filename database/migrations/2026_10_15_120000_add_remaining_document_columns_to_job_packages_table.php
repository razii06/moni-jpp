<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('job_packages', 'doc_form_pbj')) {
                $table->text('doc_form_pbj')->nullable()->after('doc_rab');
            }
            if (!Schema::hasColumn('job_packages', 'doc_laporan_pekerjaan')) {
                $table->text('doc_laporan_pekerjaan')->nullable()->after('doc_bast');
            }
            if (!Schema::hasColumn('job_packages', 'doc_pendukung_lainnya')) {
                $table->text('doc_pendukung_lainnya')->nullable()->after('doc_laporan_pekerjaan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            $table->dropColumn([
                'doc_form_pbj',
                'doc_laporan_pekerjaan',
                'doc_pendukung_lainnya',
            ]);
        });
    }
};

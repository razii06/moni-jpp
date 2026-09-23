<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            $table->string('doc_rab')->nullable()->after('google_drive_folder_id');
            $table->string('doc_bak')->nullable()->after('doc_rab');
            $table->string('doc_surat_permintaan')->nullable()->after('doc_bak');
            $table->string('doc_surat_izin_prinsip')->nullable()->after('doc_surat_permintaan');
            $table->string('doc_tor')->nullable()->after('doc_surat_izin_prinsip');
            $table->string('doc_bast')->nullable()->after('doc_tor');
        });
    }

    public function down(): void
    {
        Schema::table('job_packages', function (Blueprint $table) {
            $table->dropColumn([
                'doc_rab',
                'doc_bak',
                'doc_surat_permintaan',
                'doc_surat_izin_prinsip',
                'doc_tor',
                'doc_bast',
            ]);
        });
    }
};
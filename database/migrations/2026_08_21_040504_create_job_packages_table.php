<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_packages', function (Blueprint $table) {
            $table->id();
            $table->string('job_package');
            $table->string('no_surat_bak_doc')->nullable();
            $table->date('tanggal_surat_masuk')->nullable();
            $table->string('no_service_notifikasi');
            $table->string('no_service_order');
            $table->decimal('owner_estimate', 15, 2);
            $table->decimal('final_harga', 15, 2)->nullable();
            
            // Progress Components
            $table->decimal('rab_lp002', 5, 2)->default(0)->nullable();
            $table->decimal('pbj_lp002', 5, 2)->default(0)->nullable();
            $table->decimal('progress_pekerjaan', 5, 2)->default(0)->nullable();
            $table->decimal('proses_adm_keuangan', 5, 2)->default(0)->nullable();
            $table->date('tanggal_mulai_pekerjaan')->nullable();
            $table->date('tanggal_selesai_pekerjaan')->nullable();
            $table->decimal('hasil_progres', 5, 2)->default(0);
            
            // Field PO & Lainnya
            $table->string('no_po')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('google_drive_folder_id')->nullable();

            // Kolom Dokumen (diubah menjadi json untuk Multiple Upload)
            $table->json('doc_rab')->nullable();
            $table->json('doc_bak')->nullable();
            $table->json('doc_surat_permintaan')->nullable();
            $table->json('doc_surat_izin_prinsip')->nullable();
            $table->json('doc_tor')->nullable();
            $table->json('doc_bast')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_packages');
    }
};
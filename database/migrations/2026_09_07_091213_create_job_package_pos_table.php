<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_package_pos', function (Blueprint $table) {
            $table->id();
            // Foreign key yang terhubung ke tabel job_packages
            $table->foreignId('job_package_id')
                  ->constrained('job_packages')
                  ->onDelete('cascade');
            
            $table->string('no_po');                  // Nomor PO (misal: 5450000129)
            $table->string('description')->nullable(); // Keterangan/Item (misal: PO ExtraFooding)
            $table->decimal('price', 15, 2)->default(0); // Harga PO (misal: 9100000)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_package_pos');
    }
};
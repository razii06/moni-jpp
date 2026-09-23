<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_package_permintaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_package_id')->constrained()->onDelete('cascade');
            $table->string('permintaan_dari');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_package_permintaans');
    }
};
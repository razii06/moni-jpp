<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPackagePermintaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_package_id',
        'permintaan_dari',
    ];

    public function jobPackage(): BelongsTo
    {
        return $this->belongsTo(JobPackage::class);
    }
}
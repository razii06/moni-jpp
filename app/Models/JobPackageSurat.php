<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPackageSurat extends Model
{
    protected $fillable = [
        'job_package_id',
        'no_surat_bak_doc',
    ];

    public function jobPackage(): BelongsTo
    {
        return $this->belongsTo(JobPackage::class);
    }
}
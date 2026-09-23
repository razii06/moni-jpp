<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPackagePo extends Model
{
    use HasFactory;

    protected $table = 'job_package_pos';

    protected $fillable = [
        'job_package_id',
        'no_po',
        'description',
        'price',
    ];

    /**
     * Relasi ke JobPackage (BelongsTo)
     */
    public function jobPackage()
    {
        return $this->belongsTo(JobPackage::class, 'job_package_id');
    }
}
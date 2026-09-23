<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'job_package_id',
        'user_id',
        'action',
        'description',
    ];

    public function jobPackage(): BelongsTo
    {
        return $this->belongsTo(JobPackage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
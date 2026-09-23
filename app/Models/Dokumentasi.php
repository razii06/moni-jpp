<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumentasi extends Model
{
    protected $fillable = [
        'judul',
        'kategori',
        'visibilitas',
        'file_path',
        'ukuran_file',
    ];
}

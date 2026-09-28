<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'items', 'job_package', 'visibility', 'periode', 'no_surat_bak_doc',
        'tanggal_surat_masuk', 'no_service_notifikasi', 'no_service_order',
        'owner_estimate', 'final_harga', 'rab_lp002', 'pbj_lp002',
        'progress_pekerjaan', 'tanggal_mulai_pekerjaan', 'proses_adm_keuangan',
        'tanggal_selesai_pekerjaan', 'hasil_progres', 'no_po', 'po_items',
        'latest_activity', 'keterangan', 'google_drive_folder_id',
        'doc_rab', 'doc_bak', 'doc_surat_permintaan', 'doc_surat_izin_prinsip',
        'doc_tor', 'doc_bast', 'created_by', 'status',
    ];

    protected $casts = [
        'periode'                   => 'array',
        'items'                     => 'array',
        'tanggal_surat_masuk'       => 'date',
        'tanggal_mulai_pekerjaan'   => 'date',
        'tanggal_selesai_pekerjaan' => 'date',
        'owner_estimate'            => 'decimal:2',
        'final_harga'               => 'decimal:2',
        'rab_lp002'                 => 'float',
        'pbj_lp002'                 => 'float',
        'progress_pekerjaan'        => 'float',
        'proses_adm_keuangan'       => 'float',
        'hasil_progres'             => 'float',
        'po_items'                  => 'array',
        
        // Multiple Upload Array Casts
        'doc_rab'                   => 'array',
        'doc_bak'                   => 'array',
        'doc_surat_permintaan'      => 'array',
        'doc_surat_izin_prinsip'    => 'array',
        'doc_tor'                   => 'array',
        'doc_bast'                  => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function ($jobPackage) {
            $rab       = (float) ($jobPackage->rab_lp002 ?? 0);
            $pbj       = (float) ($jobPackage->pbj_lp002 ?? 0);
            $pekerjaan = (float) ($jobPackage->progress_pekerjaan ?? 0);
            $adm       = (float) ($jobPackage->proses_adm_keuangan ?? 0);

            $total = ($rab * 0.05) + ($pbj * 0.05) + ($pekerjaan * 0.85) + ($adm * 0.05);

            $jobPackage->hasil_progres = round($total, 2);
        });
    }

    protected function ownerEstimate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => self::parseRupiahToFloat($value)
        );
    }

    protected function finalHarga(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => self::parseRupiahToFloat($value)
        );
    }

    /**
     * Membersihkan format teks/rupiah menjadi float standar database.
     */
    private static function parseRupiahToFloat(mixed $value): mixed
    {
        if (is_string($value) && $value !== '') {
            $clean = preg_replace('/[^0-9,.]/', '', $value);
            
            if (str_contains($clean, '.') && str_contains($clean, ',')) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } elseif (str_contains($clean, '.') && !str_contains($clean, ',')) {
                $clean = str_replace('.', '', $clean);
            }
            
            return (float) $clean;
        }
        
        return $value;
    }

    public function pos(): HasMany
    {
        return $this->hasMany(JobPackagePo::class, 'job_package_id');
    }

    public function suratBakDocs(): HasMany
    {
        return $this->hasMany(JobPackageSurat::class);
    }

    public function permintaanDaris(): HasMany
    {
        return $this->hasMany(JobPackagePermintaan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }
}
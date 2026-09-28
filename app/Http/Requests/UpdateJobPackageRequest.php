<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        // 1. Ubah job_package menjadi string jika dikirim sebagai array
        $jobPackage = $this->input('job_package');
        if (is_array($jobPackage)) {
            $jobPackage = implode(', ', array_filter($jobPackage));
        }

        // 2. Ubah periode menjadi string jika dikirim sebagai array
        $periode = $this->input('periode');
        if (is_array($periode)) {
            $periode = implode(' s/d ', array_filter($periode));
        }

        // 3. Normalisasi po_items ke pos jika pos kosong
        $pos = $this->input('pos', $this->input('po_items', []));

        // 4. Sanitasi data angka & desimal
        $this->merge([
            'job_package'         => $jobPackage,
            'periode'             => $periode,
            'owner_estimate'      => $this->sanitizeNumber($this->owner_estimate),
            'final_harga'         => $this->sanitizeNumber($this->final_harga),
            'rab_lp002'           => $this->sanitizeDecimal($this->rab_lp002),
            'pbj_lp002'           => $this->sanitizeDecimal($this->pbj_lp002),
            'progress_pekerjaan'  => $this->sanitizeDecimal($this->progress_pekerjaan),
            'proses_adm_keuangan' => $this->sanitizeDecimal($this->proses_adm_keuangan),
        ]);

        if (is_array($pos)) {
            $sanitizedPos = collect($pos)->map(function ($item) {
                if (isset($item['price'])) {
                    $item['price'] = $this->sanitizeNumber($item['price']);
                } elseif (isset($item['harga'])) {
                    $item['price'] = $this->sanitizeNumber($item['harga']);
                }
                return $item;
            })->toArray();

            $this->merge(['pos' => $sanitizedPos]);
        }
    }

    public function rules(): array
    {
        return [
            'items'                   => 'sometimes|array',
            'items.*'                 => 'required|string',
            'job_package'             => ['required', 'string'],
            'periode'                 => ['nullable', 'string', 'max:255'],
            'visibility'              => 'required|in:public,internal',
            'tanggal_surat_masuk'     => ['nullable', 'date'],
            'no_service_notifikasi'   => ['required', 'string', 'max:255'],
            'no_service_order'        => ['required', 'string', 'max:255'],
            'owner_estimate'          => ['required', 'numeric', 'min:0'],
            'final_harga'             => ['nullable', 'numeric', 'min:0'],

            'rab_lp002'               => ['nullable', 'numeric', 'between:0,100'],
            'pbj_lp002'               => ['nullable', 'numeric', 'between:0,100'],
            'progress_pekerjaan'      => ['nullable', 'numeric', 'between:0,100'],
            'proses_adm_keuangan'     => ['nullable', 'numeric', 'between:0,100'],

            'tanggal_mulai_pekerjaan'   => ['nullable', 'date'],
            'tanggal_selesai_pekerjaan' => ['nullable', 'date', 'after_or_equal:tanggal_mulai_pekerjaan'],

            'no_po'                   => ['nullable', 'string', 'max:255'],
            'latest_activity'         => ['nullable', 'string', 'max:2000'],
            'keterangan'              => ['nullable', 'string', 'max:2000'],

            'surat_bak_docs'          => [
                'required', 'array', 'min:1', 'max:20',
                function ($attribute, $value, $fail) {
                    $hasContent = collect($value)->contains(fn ($v) => trim((string) $v) !== '');
                    if (!$hasContent) {
                        $fail('Minimal harus ada satu No. Surat / BAK / Doc yang diisi.');
                    }
                },
            ],
            'surat_bak_docs.*'        => ['nullable', 'string', 'max:1000'],

            'permintaan_dari'         => ['nullable', 'array', 'max:20'],
            'permintaan_dari.*'       => ['nullable', 'string', 'max:255'],

            'pos'                     => ['nullable', 'array', 'max:50'],
            'pos.*.no_po'             => ['nullable', 'string', 'max:255'],
            'pos.*.description'       => ['nullable', 'string', 'max:1000'],
            'pos.*.price'             => ['nullable', 'numeric', 'min:0'],

            'doc_rab'                 => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:51200'],
            'doc_bak'                 => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:51200'],
            'doc_surat_permintaan'    => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:51200'],
            'doc_surat_izin_prinsip'   => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:51200'],
            'doc_tor'                 => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:51200'],
            'doc_bast'                => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:51200'],
        ];
    }

    public function messages(): array
    {
        return [
            'job_package.required'           => 'Nama Job Package wajib diisi.',
            'no_service_notifikasi.required' => 'Nomor Service Notifikasi wajib diisi.',
            'no_service_order.required'      => 'Nomor Service Order wajib diisi.',
            'owner_estimate.required'        => 'Nilai Owner Estimate wajib diisi.',
            'tanggal_selesai_pekerjaan.after_or_equal' => 'Tanggal selesai pekerjaan tidak boleh lebih awal dari tanggal mulai.',
            'between'                        => 'Nilai persentase :attribute harus berada di antara 0% hingga 100%.',
            'max'                            => 'Ukuran berkas :attribute tidak boleh melebihi 50 MB.',
            'mimes'                          => 'Format berkas :attribute harus berupa PDF, Word, Excel, ZIP, atau RAR.',
            'pos.max'                        => 'Jumlah rincian PO tidak boleh lebih dari 50 item.',
            'pos.*.price.numeric'            => 'Harga pada rincian PO harus berupa angka.',
            'surat_bak_docs.required'        => 'Minimal harus ada satu No. Surat / BAK / Doc.',
            'surat_bak_docs.max'             => 'Jumlah No. Surat / BAK / Doc tidak boleh lebih dari 20 item.',
            'permintaan_dari.max'            => 'Jumlah Permintaan Dari tidak boleh lebih dari 20 item.',
        ];
    }

    public function attributes(): array
    {
        return [
            'job_package'             => 'Job Package',
            'no_service_notifikasi'   => 'No. Service Notifikasi',
            'no_service_order'        => 'No. Service Order',
            'owner_estimate'          => 'Owner Estimate',
            'final_harga'             => 'Final Harga',
            'rab_lp002'               => 'RAB LP-001/002',
            'pbj_lp002'               => 'PBJ LP-001/002',
            'progress_pekerjaan'      => 'Progress Pekerjaan',
            'proses_adm_keuangan'     => 'Proses ADM Keuangan',
            'doc_rab'                 => 'Dokumen RAB',
            'doc_bak'                 => 'Dokumen BAK',
            'doc_surat_permintaan'    => 'Dokumen Surat Permintaan',
            'doc_surat_izin_prinsip'   => 'Dokumen Surat Izin Prinsip',
            'doc_tor'                 => 'Dokumen TOR',
            'doc_bast'                => 'Dokumen BAST',
            'pos.*.no_po'             => 'No. PO',
            'pos.*.description'       => 'Deskripsi Item PO',
            'pos.*.price'             => 'Harga PO',
            'surat_bak_docs.*'        => 'No. Surat/BAK/Doc',
            'permintaan_dari.*'       => 'Permintaan Dari',
        ];
    }

    private function sanitizeDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = str_replace(',', '.', trim((string) $value));
        $clean = preg_replace('/[^\d.]/', '', $clean);

        return is_numeric($clean) ? (float) $clean : null;
    }

    private function sanitizeNumber(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = preg_replace('/[^\d.,]/', '', (string) $value);

        if (strpos($clean, '.') !== false && strpos($clean, ',') !== false) {
            if (strrpos($clean, ',') > strrpos($clean, '.')) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                $clean = str_replace(',', '', $clean);
            }
        }
        elseif (strpos($clean, '.') !== false) {
            if (substr_count($clean, '.') > 1 || preg_match('/\.\d{3}$/', $clean)) {
                $clean = str_replace('.', '', $clean);
            }
        }
        elseif (strpos($clean, ',') !== false) {
            $clean = str_replace(',', '.', $clean);
        }

        return is_numeric($clean) ? (float) $clean : null;
    }
}
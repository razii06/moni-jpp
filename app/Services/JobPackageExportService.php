<?php

namespace App\Services;

use App\Models\JobPackage;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class JobPackageExportService
{
    private const STATUS_BERJALAN = 'Berjalan';
    private const STATUS_SELESAI  = 'Selesai';
    private const STATUS_BATAL    = 'Batal';

    public function generate(): Xlsx
    {
        $jobPackages = JobPackage::with(['pos', 'suratBakDocs', 'permintaanDaris'])->get();

        // Urutan: Berjalan (tenggat terdekat di atas) -> Selesai -> Batal
        $jobPackages = $jobPackages->sort(function ($a, $b) {
            $rankA = $this->statusRank($this->resolveStatus($a));
            $rankB = $this->statusRank($this->resolveStatus($b));

            if ($rankA !== $rankB) {
                return $rankA <=> $rankB;
            }

            // Khusus Berjalan: tenggat paling dekat di atas, tanpa tenggat di paling bawah kelompok
            if ($rankA === 0) {
                $deadlineA = $a->tanggal_selesai_pekerjaan?->timestamp ?? PHP_INT_MAX;
                $deadlineB = $b->tanggal_selesai_pekerjaan?->timestamp ?? PHP_INT_MAX;

                if ($deadlineA !== $deadlineB) {
                    return $deadlineA <=> $deadlineB;
                }
            }

            // Selebihnya: data terbaru di atas
            return ($b->created_at?->timestamp ?? 0) <=> ($a->created_at?->timestamp ?? 0);
        })->values();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pekerjaan JPP');

        // ================= HEADER JUDUL =================
        // Tidak di-merge agar judul tidak terpotong oleh freeze pane saat layar digeser
        $sheet->setCellValue('A1', 'Laporan Pekerjaan Dept. JPP');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // ================= HEADER KOLOM =================
        $headers = [
            'A' => 'No',
            'B' => 'Status',
            'C' => "Job Package Internal\n(SM01)",
            'D' => 'Permintaan Dari',
            'E' => "No. Surat /\nNo. BAK /\nNo. Doc",
            'F' => "Tgl Masuk\nSurat",
            'G' => "No. Service\nNotifikasi",
            'H' => "No. Service\nOrder (SO)",
            'I' => 'No. PO & Rincian Item',
            'J' => "Owner Estimate\n(OE)",
            'K' => 'Final Harga',
            'L' => "RAB LP-001\n(%)",
            'M' => "PB/J LP-002\n(%)",
            'N' => "Progress\nPekerjaan (%)",
            'O' => "Tgl Mulai\nPekerjaan",
            'P' => "Proses ADM\nKeuangan (%)",
            'Q' => "Tgl Selesai\nPekerjaan",
            'R' => "Hasil Progres\n(%)",
            'S' => 'Aktivitas Terkini',
            'T' => 'Keterangan',
        ];

        $headerRow = 3;
        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}{$headerRow}", $label);
        }

        $headerRange = "A{$headerRow}:T{$headerRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('0F2B5C');
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        $sheet->getRowDimension($headerRow)->setRowHeight(45);

        // ================= ISI DATA =================
        $row = $headerRow + 1;
        $no = 1;

        foreach ($jobPackages as $jp) {
            $status      = $this->resolveStatus($jp);
            $isCancelled = $status === self::STATUS_BATAL;

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $status);
            $sheet->setCellValue("C{$row}", $this->cleanText($jp->job_package));

            // Permintaan Dari: tanpa nomor, satu item per baris
            $permintaanItems = $jp->permintaanDaris
                ->pluck('permintaan_dari')
                ->map(fn ($v) => $this->cleanText($v))
                ->filter(fn ($v) => $v !== '')
                ->values();
            $sheet->setCellValue("D{$row}", $permintaanItems->isNotEmpty() ? $permintaanItems->implode("\n") : '-');

            // No. Surat / BAK / Doc: tanpa nomor, satu item per baris
            $suratItems = $jp->suratBakDocs
                ->pluck('no_surat_bak_doc')
                ->map(fn ($v) => $this->cleanText($v))
                ->filter(fn ($v) => $v !== '')
                ->values();
            $suratString = $suratItems->isNotEmpty()
                ? $suratItems->implode("\n")
                : $this->cleanText($jp->no_surat_bak_doc ?? '-');
            $sheet->setCellValue("E{$row}", $suratString);

            $sheet->setCellValue("F{$row}", optional($jp->tanggal_surat_masuk)->format('d/m/Y'));

            $sheet->setCellValueExplicit("G{$row}", (string) $jp->no_service_notifikasi, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("H{$row}", (string) $jp->no_service_order, DataType::TYPE_STRING);

            if ($jp->pos->isNotEmpty()) {
                $poList = [];
                foreach ($jp->pos as $idx => $p) {
                    $nopo = $p->no_po ?? '';
                    $desc = $p->description ? " - {$p->description}" : '';
                    $hrg  = $p->price > 0 ? ' (Rp ' . number_format((float) $p->price, 0, ',', '.') . ')' : '';
                    $poList[] = ($idx + 1) . ". " . trim("{$nopo}{$desc}{$hrg}");
                }
                $poString = implode("\n", $poList);
            } elseif (!empty($jp->po_items) && is_array($jp->po_items)) {
                $poList = [];
                foreach ($jp->po_items as $idx => $p) {
                    $nama = $p['nama_item'] ?? $p['description'] ?? '';
                    $nopo = $p['no_po'] ?? '';
                    $hrg  = isset($p['harga']) || isset($p['price']) ? ' (Rp ' . number_format((float) ($p['harga'] ?? $p['price']), 0, ',', '.') . ')' : '';
                    $poList[] = ($idx + 1) . ". " . trim("{$nopo} - {$nama}{$hrg}");
                }
                $poString = implode("\n", $poList);
            } else {
                $poString = (string) ($jp->no_po ?? '-');
            }
            $sheet->setCellValue("I{$row}", $poString);

            $sheet->setCellValue("J{$row}", (float) $jp->owner_estimate);
            $sheet->setCellValue("K{$row}", (float) $jp->final_harga);
            $sheet->setCellValue("L{$row}", (float) $jp->rab_lp002 / 100);
            $sheet->setCellValue("M{$row}", (float) $jp->pbj_lp002 / 100);
            $sheet->setCellValue("N{$row}", (float) $jp->progress_pekerjaan / 100);
            $sheet->setCellValue("O{$row}", optional($jp->tanggal_mulai_pekerjaan)->format('d/m/Y'));
            $sheet->setCellValue("P{$row}", (float) $jp->proses_adm_keuangan / 100); // sekarang tampil sebagai persen
            $sheet->setCellValue("Q{$row}", optional($jp->tanggal_selesai_pekerjaan)->format('d/m/Y'));
            $sheet->setCellValue("R{$row}", (float) $jp->hasil_progres / 100);
            $sheet->setCellValue("S{$row}", $this->cleanText($jp->latest_activity ?? '-'));
            $sheet->setCellValue("T{$row}", $this->cleanText($jp->keterangan));

            $sheet->getStyle("J{$row}:K{$row}")->getNumberFormat()->setFormatCode('"Rp" #,##0');
            $sheet->getStyle("L{$row}:N{$row}")->getNumberFormat()->setFormatCode('0.00%');
            $sheet->getStyle("P{$row}")->getNumberFormat()->setFormatCode('0.00%');
            $sheet->getStyle("R{$row}")->getNumberFormat()->setFormatCode('0.00%');

            if ($isCancelled) {
                $sheet->getStyle("A{$row}:T{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
                $sheet->getStyle("A{$row}:T{$row}")->getFont()
                    ->setItalic(true)->getColor()->setRGB('6B7280');
                $sheet->getStyle("B{$row}")->getFont()->setBold(true)->getColor()->setRGB('DC2626');
            } else {
                // Warna bergantung nilai: 100% hijau, 1-99% kuning, 0% merah muda
                $this->applyProgressColor($sheet, "L{$row}", $jp->rab_lp002);
                $this->applyProgressColor($sheet, "M{$row}", $jp->pbj_lp002);
                $this->applyProgressColor($sheet, "N{$row}", $jp->progress_pekerjaan);
                $this->applyProgressColor($sheet, "P{$row}", $jp->proses_adm_keuangan);
                $this->applyProgressColor($sheet, "R{$row}", $jp->hasil_progres);

                // Warna kolom Status
                if ($status === self::STATUS_SELESAI) {
                    $statusFont = '1D4ED8';
                    $statusFill = 'DBEAFE';
                } else {
                    $statusFont = '16A34A';
                    $statusFill = 'DCFCE7';
                }
                $sheet->getStyle("B{$row}")->getFont()->setBold(true)->getColor()->setRGB($statusFont);
                $sheet->getStyle("B{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($statusFill);
            }

            $sheet->getStyle("A{$row}:T{$row}")->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $sheet->getStyle("A{$row}:T{$row}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            $sheet->getStyle("A{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L{$row}:R{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$row}:K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Kolom teks: rata kiri dengan indentasi agar tidak menempel ke garis tepi
            foreach (['C', 'D', 'E', 'I', 'S', 'T'] as $textCol) {
                $sheet->getStyle("{$textCol}{$row}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setIndent(1);
            }

            $row++;
        }

        // Lebar kolom. Kolom ber-filter dilebarkan agar teks header tidak tertutup panah filter,
        // E dilebarkan agar satu nomor dokumen muat dalam satu baris, T agar Keterangan tidak terlalu tinggi
        $widths = [
            'A' => 7,  'B' => 14, 'C' => 34, 'D' => 26, 'E' => 40, 'F' => 16, 'G' => 18,
            'H' => 18, 'I' => 38, 'J' => 24, 'K' => 24, 'L' => 15, 'M' => 15, 'N' => 18,
            'O' => 16, 'P' => 18, 'Q' => 16, 'R' => 16, 'S' => 34, 'T' => 60,
        ];

        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Filter otomatis di baris header (mis. saring per Status)
        $lastRow = max($row - 1, $headerRow);
        $sheet->setAutoFilter("A{$headerRow}:T{$lastRow}");

        // Kolom No, Status, dan Job Package tetap terlihat saat menggeser ke kanan
        $sheet->freezePane('D' . ($headerRow + 1));

        return new Xlsx($spreadsheet);
    }

    /**
     * Batal      : status = 'batal'
     * Selesai    : tidak batal dan hasil progres >= 100
     * Berjalan   : selain itu (termasuk belum mulai & terlambat)
     */
    private function resolveStatus(JobPackage $jp): string
    {
        if ($jp->status === 'batal') {
            return self::STATUS_BATAL;
        }

        if ((float) $jp->hasil_progres >= 100) {
            return self::STATUS_SELESAI;
        }

        return self::STATUS_BERJALAN;
    }

    private function statusRank(string $status): int
    {
        return match ($status) {
            self::STATUS_BERJALAN => 0,
            self::STATUS_SELESAI  => 1,
            default               => 2,
        };
    }

    /**
     * Seragamkan pemisah baris (\r\n -> \n) dan rapikan spasi di tepi teks.
     */
    private function cleanText(mixed $value): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", (string) $value);

        return trim($text);
    }

    /**
     * Warna sel berdasarkan nilai (skala 0-100):
     *  - 100        : hijau      (selesai)
     *  - 1 s/d 99   : kuning     (sedang berjalan)
     *  - 0 / kosong : merah muda (belum ada progres)
     */
    private function applyProgressColor($sheet, string $cell, $value): void
    {
        $value = (float) $value;

        if ($value >= 100) {
            $color = '92D050';
        } elseif ($value > 0) {
            $color = 'FFFF00';
        } else {
            $color = 'FFC7CE';
        }

        $sheet->getStyle($cell)->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
    }
}
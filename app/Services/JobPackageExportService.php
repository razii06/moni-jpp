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
    public function generate(): Xlsx
    {
        // UPDATED: eager-load permintaanDaris juga
        $jobPackages = JobPackage::with(['pos', 'suratBakDocs', 'permintaanDaris'])
            ->orderByRaw("CASE WHEN status = 'batal' THEN 1 ELSE 0 END ASC")
            ->latest()
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pekerjaan JPP');

        // ================= HEADER JUDUL =================
        $sheet->setCellValue('A1', 'Laporan Pekerjaan Dept. JPP');
        $sheet->mergeCells('A1:T1'); // UPDATED: S -> T (nambah 1 kolom Permintaan Dari)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ================= HEADER KOLOM =================
        // UPDATED: seluruh kolom setelah B digeser satu huruf ke kanan (C jadi kolom baru "Permintaan Dari")
        $headers = [
            'A' => 'No',
            'B' => "Job Package Internal\n(SM01)",
            'C' => "Permintaan Dari", // UPDATED: kolom baru
            'D' => "No. Surat /\nNo. BAK /\nNo. Doc",
            'E' => "Tgl Masuk\nSurat",
            'F' => "No. Service\nNotifikasi",
            'G' => "No. Service\nOrder (SO)",
            'H' => "No. PO & Rincian Item",
            'I' => "Owner Estimate\n(OE)",
            'J' => 'Final Harga',
            'K' => "RAB LP-001\n(%)",
            'L' => "PB/J LP-002\n(%)",
            'M' => "Progress\nPekerjaan (%)",
            'N' => "Tgl Mulai\nPekerjaan",
            'O' => "Proses ADM\nKeuangan",
            'P' => "Tgl Selesai\nPekerjaan",
            'Q' => "Hasil Progres\n(%)",
            'R' => 'Aktivitas Terkini',
            'S' => 'Keterangan',
            'T' => 'Status',
        ];

        $headerRow = 3;
        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}{$headerRow}", $label);
        }

        $headerRange = "A{$headerRow}:T{$headerRow}"; // UPDATED: S -> T
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
            $isCancelled = $jp->status === 'batal';

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $jp->job_package);

            // UPDATED: kolom baru "Permintaan Dari" — format numbered list kalau lebih dari 1
            if ($jp->permintaanDaris->isNotEmpty()) {
                $permintaanList = [];
                foreach ($jp->permintaanDaris as $idx => $p) {
                    $permintaanList[] = ($idx + 1) . ". " . $p->permintaan_dari;
                }
                $permintaanString = implode("\n", $permintaanList);
            } else {
                $permintaanString = '-';
            }
            $sheet->setCellValue("C{$row}", $permintaanString);

            // No. Surat/BAK/Doc
            if ($jp->suratBakDocs->isNotEmpty()) {
                $suratList = [];
                foreach ($jp->suratBakDocs as $idx => $surat) {
                    $suratList[] = ($idx + 1) . ". " . $surat->no_surat_bak_doc;
                }
                $suratString = implode("\n", $suratList);
            } else {
                $suratString = (string) ($jp->no_surat_bak_doc ?? '-');
            }
            $sheet->setCellValue("D{$row}", $suratString);

            $sheet->setCellValue("E{$row}", optional($jp->tanggal_surat_masuk)->format('d/m/Y'));

            $sheet->setCellValueExplicit("F{$row}", (string) $jp->no_service_notifikasi, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("G{$row}", (string) $jp->no_service_order, DataType::TYPE_STRING);

            if ($jp->pos->isNotEmpty()) {
                $poList = [];
                foreach ($jp->pos as $idx => $p) {
                    $nopo = $p->no_po ?? '';
                    $desc = $p->description ? " - {$p->description}" : '';
                    $hrg  = $p->price > 0 ? ' (Rp ' . number_format((float)$p->price, 0, ',', '.') . ')' : '';
                    $poList[] = ($idx + 1) . ". " . trim("{$nopo}{$desc}{$hrg}");
                }
                $poString = implode("\n", $poList);
            } elseif (!empty($jp->po_items) && is_array($jp->po_items)) {
                $poList = [];
                foreach ($jp->po_items as $idx => $p) {
                    $nama = $p['nama_item'] ?? $p['description'] ?? '';
                    $nopo = $p['no_po'] ?? '';
                    $hrg  = isset($p['harga']) || isset($p['price']) ? ' (Rp ' . number_format((float)($p['harga'] ?? $p['price']), 0, ',', '.') . ')' : '';
                    $poList[] = ($idx + 1) . ". " . trim("{$nopo} - {$nama}{$hrg}");
                }
                $poString = implode("\n", $poList);
            } else {
                $poString = (string) ($jp->no_po ?? '-');
            }
            $sheet->setCellValue("H{$row}", $poString);

            $sheet->setCellValue("I{$row}", (float) $jp->owner_estimate);
            $sheet->setCellValue("J{$row}", (float) $jp->final_harga);
            $sheet->setCellValue("K{$row}", (float) $jp->rab_lp002 / 100);
            $sheet->setCellValue("L{$row}", (float) $jp->pbj_lp002 / 100);
            $sheet->setCellValue("M{$row}", (float) $jp->progress_pekerjaan / 100);
            $sheet->setCellValue("N{$row}", optional($jp->tanggal_mulai_pekerjaan)->format('d/m/Y'));
            $sheet->setCellValue("O{$row}", $jp->proses_adm_keuangan);
            $sheet->setCellValue("P{$row}", optional($jp->tanggal_selesai_pekerjaan)->format('d/m/Y'));
            $sheet->setCellValue("Q{$row}", (float) $jp->hasil_progres / 100);
            $sheet->setCellValue("R{$row}", $jp->latest_activity ?? '-');
            $sheet->setCellValue("S{$row}", $jp->keterangan);
            $sheet->setCellValue("T{$row}", $isCancelled ? 'BATAL' : 'AKTIF');

            $sheet->getStyle("I{$row}:J{$row}")->getNumberFormat()->setFormatCode('"Rp" #,##0');
            $sheet->getStyle("K{$row}:M{$row}")->getNumberFormat()->setFormatCode('0.00%');
            $sheet->getStyle("Q{$row}")->getNumberFormat()->setFormatCode('0.00%');

            if ($isCancelled) {
                $sheet->getStyle("A{$row}:T{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
                $sheet->getStyle("A{$row}:T{$row}")->getFont()
                    ->setItalic(true)->getColor()->setRGB('6B7280');
                $sheet->getStyle("T{$row}")->getFont()->setBold(true)->getColor()->setRGB('DC2626');
            } else {
                $sheet->getStyle("K{$row}:L{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('92D050');

                $this->applyProgressColor($sheet, "M{$row}", $jp->progress_pekerjaan);

                $sheet->getStyle("O{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');

                $this->applyProgressColor($sheet, "Q{$row}", $jp->hasil_progres);

                $sheet->getStyle("T{$row}")->getFont()->setBold(true)->getColor()->setRGB('16A34A');
            }

            $sheet->getStyle("A{$row}:T{$row}")->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $sheet->getStyle("A{$row}:T{$row}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("K{$row}:Q{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$row}:J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // UPDATED: lebar kolom disesuaikan dengan pergeseran huruf
        $widths = [
            'A' => 6, 'B' => 32, 'C' => 22, 'D' => 30, 'E' => 14, 'F' => 18, 'G' => 18,
            'H' => 35, 'I' => 22, 'J' => 22, 'K' => 14, 'L' => 14, 'M' => 15,
            'N' => 14, 'O' => 16, 'P' => 14, 'Q' => 14, 'R' => 32, 'S' => 45,
            'T' => 14,
        ];

        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $sheet->freezePane('A' . ($headerRow + 1));

        return new Xlsx($spreadsheet);
    }

    private function applyProgressColor($sheet, string $cell, $value): void
    {
        $value = (float) $value;

        if ($value >= 90) {
            $color = '92D050';
        } elseif ($value >= 50) {
            $color = 'FFFF00';
        } else {
            $color = 'FFC7CE';
        }

        $sheet->getStyle($cell)->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
    }
}
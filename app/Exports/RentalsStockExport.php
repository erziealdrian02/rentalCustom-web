<?php

namespace App\Exports;

use App\Models\Rentals;
use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font as SpFont;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RentalsStockExport implements FromArray, WithEvents, WithTitle
{
    protected Rentals $rental;
    protected $kirimMovements; // stock_type = RENT  (movement_id)
    protected $pulangMovements; // stock_type = RETURN/DAMAGED/LOST/SOLD (return_movement_id)
    protected array $tools;

    // Kondisi pulang yang ditampilkan sebagai sub-baris
    const CONDITIONS = [
        'RETURN' => 'Kembali (Baik)',
        'DAMAGED' => 'Rusak',
        'LOST' => 'Hilang',
        'SOLD' => 'Dijual/Lainnya',
    ];

    public function __construct(string $rentalId)
    {
        $this->rental = Rentals::with('customer')->findOrFail($rentalId);

        // IDs kirim (movement_id) → RENT
        $kirimIds = json_decode($this->rental->movement_id, true) ?? [];

        // IDs pulang (return_movement_id) → RETURN/DAMAGED/LOST/SOLD
        $pulangIds = json_decode($this->rental->return_movement_id, true) ?? [];

        $this->kirimMovements = StockMovement::with('tool')->whereIn('id', $kirimIds)->where('stock_type', 'RENT')->orderBy('created_at')->get();

        $this->pulangMovements = StockMovement::with('tool')
            ->whereIn('id', $pulangIds)
            ->whereIn('stock_type', ['RETURN', 'DAMAGED', 'LOST', 'SOLD'])
            ->orderBy('created_at')
            ->get();

        // Unique tools dari kedua sisi
        $this->tools = $this->kirimMovements
            ->map(fn($m) => $m->tool)
            ->merge($this->pulangMovements->map(fn($m) => $m->tool))
            ->unique('id')
            ->filter()
            ->values()
            ->toArray();
    }

    public function title(): string
    {
        return 'Rekap Stock Project';
    }
    public function array(): array
    {
        return [['']];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => fn(AfterSheet $e) => $this->buildSheet($e->sheet->getDelegate()),
        ];
    }

    private function buildSheet(Worksheet $ws): void
    {
        $tools = $this->tools;
        $nTools = count($tools);
        $lastCol = 3 + $nTools;
        $lastColLetter = Coordinate::stringFromColumnIndex($lastCol);

        $thin = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']];
        $thick = ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']];
        $GREY = 'D9D9D9';
        $GREEN = 'E2EFDA';
        $RED = 'FF0000';
        $IDR = '#,##0';

        // ── Helper ───────────────────────────────────────────
        $col = fn(int $i) => Coordinate::stringFromColumnIndex($i);

        $applyStyle = function (int $row, int $colIdx, $value = '', bool $bold = false, int $size = 10, string $color = '000000', ?string $bg = null, string $halign = 'left', bool $wrap = false, ?string $numFmt = null, bool $italic = false, ?string $underline = null, bool $border = true) use ($ws, $thin) {
            $c = $ws->getCellByColumnAndRow($colIdx, $row);
            // skip merged cells
            if ($c->isInMergeRange()) {
                $coordinate = $c->getCoordinate();
                $mergedCells = $ws->getMergeCells();
                $isMergeStart = false;
                foreach ($mergedCells as $mergeRange) {
                    if (str_starts_with($mergeRange, $coordinate)) {
                        $isMergeStart = true;
                        break;
                    }
                }
                if (!$isMergeStart) {
                    return;
                }
            }

            $c->setValue($value);

            $font = $ws->getStyleByColumnAndRow($colIdx, $row)->getFont();
            $font->setName('Arial')->setSize($size)->setBold($bold)->setItalic($italic);
            $font->getColor()->setRGB($color);
            if ($underline) {
                $font->setUnderline($underline);
            }

            $ws->getStyleByColumnAndRow($colIdx, $row)->getAlignment()->setHorizontal($halign)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText($wrap);

            if ($bg) {
                $ws->getStyleByColumnAndRow($colIdx, $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bg);
            }
            if ($numFmt) {
                $ws->getStyleByColumnAndRow($colIdx, $row)->getNumberFormat()->setFormatCode($numFmt);
            }
            if ($border) {
                $ws->getStyleByColumnAndRow($colIdx, $row)
                    ->getBorders()
                    ->applyFromArray(['allBorders' => $thin]);
            }
        };

        $mergeRow = function (int $row, int $c1, int $c2) use ($ws) {
            $ws->mergeCellsByColumnAndRow($c1, $row, $c2, $row);
        };

        // ── Column widths ─────────────────────────────────────
        $ws->getColumnDimension('A')->setWidth(16);
        $ws->getColumnDimension('B')->setWidth(2);
        $ws->getColumnDimension('C')->setWidth(24);
        for ($i = 0; $i < $nTools; $i++) {
            $ws->getColumnDimension($col(4 + $i))->setWidth(14);
        }

        // ── ROW 2-3: Judul ────────────────────────────────────
        $ws->mergeCells("A2:{$lastColLetter}2");
        $ws->setCellValue('A2', 'REKAP STOCK PROJECT: ' . strtoupper($this->rental->project_name ?? 'PENATAAN KAWASAN OLAH RAGA DAN RUANG TERBUKA HIJAU'));
        $ws->getStyle('A2')->getFont()->setName('Arial')->setBold(true)->setSize(11);
        $ws->getRowDimension(2)->setRowHeight(18);

        $ws->mergeCells("A3:{$lastColLetter}3");
        $ws->setCellValue('A3', 'ADHI-APG-PENTA KSO');
        $ws->getStyle('A3')->getFont()->setName('Arial')->setBold(true)->setSize(16);
        $ws->getRowDimension(3)->setRowHeight(26);

        $ws->getRowDimension(4)->setRowHeight(8);
        $ws->getRowDimension(5)->setRowHeight(8);

        // ── ROW 6: Header tabel ───────────────────────────────
        $hdr = fn(int $c, string $text) => $applyStyle(6, $c, $text, true, 10, '000000', $GREY, Alignment::HORIZONTAL_CENTER, true);

        $hdr(1, 'TANGGAL SJ');
        $hdr(2, '');
        $hdr(3, 'SURAT JALAN');
        foreach ($tools as $i => $tool) {
            $hdr(4 + $i, strtoupper($tool['name'] ?? 'TOOL ' . ($i + 1)));
        }
        $ws->getRowDimension(6)->setRowHeight(36);

        // ── ROW 7: PO.1 ──────────────────────────────────────
        $mergeRow(7, 1, 3);
        $applyStyle(7, 1, 'PO .1', true);
        $ws->getRowDimension(7)->setRowHeight(16);

        // Total qty kirim per tool di baris PO
        foreach ($tools as $i => $tool) {
            $total = $this->kirimMovements->where('tool_id', $tool['id_tools'])->sum('quantity');
            $applyStyle(7, 4 + $i, $total ?: '', true, 10, '000000', null, Alignment::HORIZONTAL_RIGHT, false, $IDR);
        }

        // ── KIRIM ROWS ────────────────────────────────────────
        $kirimStart = 8;
        $r = $kirimStart;

        // Group by reference_id (nomor surat jalan)
        $kirimGrouped = $this->kirimMovements->groupBy('reference_id');

        // ── KIRIM ROWS — A=tanggal (right-align), B=spacer, C=nomor SJ, D+=qty
        foreach ($kirimGrouped as $refId => $group) {
            $ws->getRowDimension($r)->setRowHeight(15);
            $date = \Carbon\Carbon::parse($group->first()->updated_at)->format('d-M-y');

            // ❌ HAPUS: merge A-C jadi satu
            // ✅ PISAH: A = tanggal, C = no SJ (masing-masing berdiri sendiri)
            $ws->getCellByColumnAndRow(1, $r)->setValue($date);
            $ws->getStyleByColumnAndRow(1, $r)->applyFromArray([
                'font' => ['name' => 'Arial', 'size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => $thin],
            ]);

            $ws->getCellByColumnAndRow(2, $r)->setValue('');
            $ws->getStyleByColumnAndRow(2, $r)
                ->getBorders()
                ->applyFromArray(['allBorders' => $thin]);

            $ws->getCellByColumnAndRow(3, $r)->setValue($refId);
            $ws->getStyleByColumnAndRow(3, $r)->applyFromArray([
                'font' => ['name' => 'Arial', 'size' => 10],
                'borders' => ['allBorders' => $thin],
            ]);

            foreach ($tools as $i => $tool) {
                $qty = $group->where('tool_id', $tool['id_tools'])->sum('quantity');
                $colIdx = 4 + $i;
                $ws->getCellByColumnAndRow($colIdx, $r)->setValue($qty ?: '');
                $ws->getStyleByColumnAndRow($colIdx, $r)->applyFromArray([
                    'font' => ['name' => 'Arial', 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => $thin],
                ]);
                if ($qty) {
                    $ws->getStyleByColumnAndRow($colIdx, $r)->getNumberFormat()->setFormatCode('#,##0');
                }
            }
            $r++;
        }

        $kirimEnd = $r - 1;

        // spacer
        $ws->getRowDimension($r)->setRowHeight(8);
        $r++;

        // ── TOTAL KIRIM ───────────────────────────────────────
        $totalKirimRow = $r;
        $mergeRow($r, 1, 3);
        $applyStyle($r, 1, 'TOTAL KIRIM PO.1', true, 10, '000000', $GREEN, Alignment::HORIZONTAL_LEFT);
        $ws->getRowDimension($r)->setRowHeight(17);

        for ($i = 0; $i < $nTools; $i++) {
            $letter = $col(4 + $i);
            $c = $ws->getCellByColumnAndRow(4 + $i, $r);
            $c->setValue("=SUM({$letter}{$kirimStart}:{$letter}{$kirimEnd})");
            $ws->getStyleByColumnAndRow(4 + $i, $r)->applyFromArray([
                'font' => ['name' => 'Arial', 'bold' => true, 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $GREEN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => $thin],
            ]);
            $ws->getStyleByColumnAndRow(4 + $i, $r)
                ->getNumberFormat()
                ->setFormatCode($IDR);
        }
        $r++;

        // ── SISA PO.1 ─────────────────────────────────────────
        $mergeRow($r, 1, 3);
        $applyStyle($r, 1, 'SISA PO .1', true);
        $ws->getRowDimension($r)->setRowHeight(17);

        for ($i = 0; $i < $nTools; $i++) {
            $letter = $col(4 + $i);
            $ws->getCellByColumnAndRow(4 + $i, $r)->setValue("={$letter}7-{$letter}{$totalKirimRow}");
            $ws->getStyleByColumnAndRow(4 + $i, $r)->applyFromArray([
                'font' => ['name' => 'Arial', 'bold' => true, 'size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => $thin],
            ]);
            $ws->getStyleByColumnAndRow(4 + $i, $r)
                ->getNumberFormat()
                ->setFormatCode('#,##0_);(#,##0)');
        }
        $r++;

        // spacer
        $ws->getRowDimension($r)->setRowHeight(8);
        $r++;

        // ── PULANG ROWS ───────────────────────────────────────
        $pulangStart = $r;

        // Group by reference_id lalu by stock_type
        $pulangGrouped = $this->pulangMovements->groupBy('reference_id');

        // ── PULANG: baris ref = merge A-C (tanggal + nomor ref)
        foreach ($pulangGrouped as $refId => $refGroup) {
            $date = \Carbon\Carbon::parse($refGroup->first()->updated_at)->format('d-M-y');

            $ws->getRowDimension($r)->setRowHeight(16);
            $ws->mergeCellsByColumnAndRow(1, $r, 3, $r); // ✅ merge A-C untuk baris ref pulang
            $ws->getCellByColumnAndRow(1, $r)->setValue("{$date}    {$refId}");
            $ws->getStyleByColumnAndRow(1, $r)->applyFromArray([
                'font' => ['name' => 'Arial', 'bold' => true, 'size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => $thin],
            ]);
            for ($i = 0; $i < $nTools; $i++) {
                $ws->getCellByColumnAndRow(4 + $i, $r)->setValue('');
                $ws->getStyleByColumnAndRow(4 + $i, $r)
                    ->getBorders()
                    ->applyFromArray(['allBorders' => $thin]);
            }
            $r++;

            // Sub-baris kondisi: A=kosong, B=kosong, C=label indent, D+=qty
            foreach (self::CONDITIONS as $stockType => $label) {
                $condGroup = $refGroup->where('stock_type', $stockType);
                if ($condGroup->isEmpty()) {
                    continue;
                }

                $ws->getRowDimension($r)->setRowHeight(14);
                // A dan B kosong (tidak di-merge)
                $ws->getCellByColumnAndRow(1, $r)->setValue('');
                $ws->getStyleByColumnAndRow(1, $r)
                    ->getBorders()
                    ->applyFromArray(['allBorders' => $thin]);
                $ws->getCellByColumnAndRow(2, $r)->setValue('');
                $ws->getStyleByColumnAndRow(2, $r)
                    ->getBorders()
                    ->applyFromArray(['allBorders' => $thin]);
                // C = label kondisi
                $ws->getCellByColumnAndRow(3, $r)->setValue('  └ ' . $label);
                $ws->getStyleByColumnAndRow(3, $r)->applyFromArray([
                    'font' => ['name' => 'Arial', 'size' => 9, 'italic' => true, 'color' => ['rgb' => '444444']],
                    'borders' => ['allBorders' => $thin],
                ]);

                foreach ($tools as $i => $tool) {
                    $qty = $condGroup->where('tool_id', $tool['id_tools'])->sum('quantity');
                    $colIdx = 4 + $i;
                    $ws->getCellByColumnAndRow($colIdx, $r)->setValue($qty ?: '');
                    $ws->getStyleByColumnAndRow($colIdx, $r)->applyFromArray([
                        'font' => ['name' => 'Arial', 'size' => 9],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => $thin],
                    ]);
                    if ($qty) {
                        $ws->getStyleByColumnAndRow($colIdx, $r)->getNumberFormat()->setFormatCode('#,##0');
                    }
                }
                $r++;
            }
        }

        $pulangEnd = $r - 1;

        // spacer
        $ws->getRowDimension($r)->setRowHeight(8);
        $r++;

        // ── TOTAL PULANG ──────────────────────────────────────
        $totalPulangRow = $r;
        $mergeRow($r, 1, 3);
        $applyStyle($r, 1, 'TOTAL PULANG PO. 1', true, 10, '000000', $GREEN);
        $ws->getRowDimension($r)->setRowHeight(17);

        for ($i = 0; $i < $nTools; $i++) {
            $letter = $col(4 + $i);
            $ws->getCellByColumnAndRow(4 + $i, $r)->setValue("=SUM({$letter}{$pulangStart}:{$letter}{$pulangEnd})");
            $ws->getStyleByColumnAndRow(4 + $i, $r)->applyFromArray([
                'font' => ['name' => 'Arial', 'bold' => true, 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $GREEN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => $thin],
            ]);
            $ws->getStyleByColumnAndRow(4 + $i, $r)
                ->getNumberFormat()
                ->setFormatCode($IDR);
        }
        $r++;

        // ── SISA ALAT DI PROYEK ───────────────────────────────
        $sisaRow = $r;
        $mergeRow($r, 1, 3);
        $applyStyle($r, 1, 'SISA ALAT DI PROYEK', true, 10, $RED);
        $ws->getRowDimension($r)->setRowHeight(17);

        for ($i = 0; $i < $nTools; $i++) {
            $letter = $col(4 + $i);
            $ws->getCellByColumnAndRow(4 + $i, $r)->setValue("={$letter}{$totalKirimRow}-{$letter}{$totalPulangRow}");
            $ws->getStyleByColumnAndRow(4 + $i, $r)->applyFromArray([
                'font' => ['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['rgb' => $RED]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => $thin],
            ]);
            $ws->getStyleByColumnAndRow(4 + $i, $r)
                ->getNumberFormat()
                ->setFormatCode($IDR);
        }

        // ── Outline border seluruh tabel ──────────────────────
        $ws->getStyle("A6:{$lastColLetter}{$r}")->applyFromArray([
            'borders' => ['outline' => $thick],
        ]);

        // ── Footer ────────────────────────────────────────────
        $fr = $r + 2;
        $ws->mergeCells("D{$fr}:{$lastColLetter}{$fr}");
        $ws->setCellValue("D{$fr}", 'Balikpapan, ' . now()->translatedFormat('d F Y'));
        $ws->getStyle("D{$fr}")
            ->getFont()
            ->setName('Arial')
            ->setSize(11);
        $ws->getStyle("D{$fr}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $ws->mergeCells('D' . ($fr + 1) . ":{$lastColLetter}" . ($fr + 1));
        $ws->setCellValue('D' . ($fr + 1), 'Hormat Kami,');
        $ws->getStyle('D' . ($fr + 1))
            ->getFont()
            ->setName('Arial')
            ->setSize(11);
        $ws->getStyle('D' . ($fr + 1))
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $signRow = $fr + 6;
        $ws->mergeCells("D{$signRow}:{$lastColLetter}{$signRow}");
        $ws->setCellValue("D{$signRow}", 'Raindi Andreas');
        $ws->getStyle("D{$signRow}")
            ->getFont()
            ->setName('Arial')
            ->setBold(true)
            ->setSize(11)
            ->setUnderline(SpFont::UNDERLINE_SINGLE);
        $ws->getStyle("D{$signRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $ws->mergeCells('D' . ($signRow + 1) . ":{$lastColLetter}" . ($signRow + 1));
        $ws->setCellValue('D' . ($signRow + 1), 'Direktur');
        $ws->getStyle('D' . ($signRow + 1))
            ->getFont()
            ->setName('Arial')
            ->setSize(11);
        $ws->getStyle('D' . ($signRow + 1))
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Page setup
        $ws->getPageSetup()->setOrientation('landscape')->setPaperSize(9);
        $ws->getPageMargins()->setLeft(0.5)->setRight(0.5)->setTop(0.75)->setBottom(0.75);
    }
}

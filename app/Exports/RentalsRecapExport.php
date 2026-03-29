<?php

namespace App\Exports;

use App\Models\Rentals;
use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RentalsRecapExport implements FromArray, WithEvents, WithTitle
{
    protected Rentals $rental;
    protected $movements;
    protected $customer;
    protected int $startDataRow = 10;

    public function __construct(string $rentalId)
    {
        $this->rental = Rentals::with('customer')->findOrFail($rentalId);
        $this->customer = $this->rental->customer;

        $ids = json_decode($this->rental->movement_id, true) ?? [];
        $this->movements = StockMovement::with('tool')->whereIn('id', $ids)->get();
    }

    public function title(): string
    {
        return 'Rekapitulasi Tagihan';
    }

    // FromArray — hanya mengembalikan baris kosong,
    // semua konten diisi via AfterSheet
    public function array(): array
    {
        return [['']];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->buildSheet($sheet);
            },
        ];
    }

    // ──────────────────────────────────────────────────────────
    private function buildSheet(Worksheet $sheet): void
    {
        $rental = $this->rental;
        $customer = $this->customer;
        $movements = $this->movements;

        $startDate = \Carbon\Carbon::parse($rental->rental_start_date);
        $endDate = \Carbon\Carbon::parse($rental->rental_end_date);

        // ── Column widths ────────────────────────────────────
        $widths = ['A' => 22, 'B' => 13, 'C' => 13, 'D' => 7, 'E' => 8, 'F' => 8, 'G' => 4, 'H' => 14, 'I' => 4, 'J' => 16];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // ── Styles helper ─────────────────────────────────────
        $thin = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']];
        $thick = ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']];
        $allBorder = ['allBorders' => $thin];
        $thickBorder = ['outline' => $thick];

        $IDR = '#,##0';

        // ── ROW 1: Judul ──────────────────────────────────────
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'REKAPITULASI TAGIHAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // ── ROW 2-3: Kepada / Perusahaan ─────────────────────
        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'Kepada Yth,');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(10)->setName('Arial');

        $sheet->mergeCells('A3:L3');
        $sheet->setCellValue('A3', '' . ($customer->name ?? 'Nama Customer Tidak Ditemukan') . ' ' . ($customer->address ?? 'Alamat Customer Tidak Ditemukan') . "\n" . ($customer->city ?? 'Kota Customer Tidak Ditemukan'));
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(10)->setName('Arial');

        // ── ROW 4: spacer ─────────────────────────────────────
        $sheet->getRowDimension(4)->setRowHeight(4);

        // ── ROW 5-6: Proyek & Periode ─────────────────────────
        $sheet->mergeCells('A5:L5');
        $sheet->setCellValue('A5', 'Proyek : ' . ($rental->project_name ?? 'Pembangunan Penataan Kawasan Olahraga Dan Ruang Terbuka Hijau'));
        $sheet->getStyle('A5')->getFont()->setSize(10)->setName('Arial');

        $sheet->mergeCells('A6:L6');
        $sheet->setCellValue('A6', 'Periode Sewa : ' . $startDate->format('d F Y') . ' s/d ' . $endDate->format('d F Y'));
        $sheet->getStyle('A6')->getFont()->setSize(10)->setName('Arial');

        $sheet->getRowDimension(7)->setRowHeight(6);

        // ── ROW 8-9: Table Header ─────────────────────────────
        $hdrFill = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9D9D9']];
        $hdrFont = ['bold' => true, 'size' => 9, 'name' => 'Arial'];
        $center = ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true];

        $headers8 = [
            'A8:A9' => 'Nama Barang',
            'B8:C8' => 'Periode Sewa',
            'D8:D9' => "Hari\n(D)",
            'E8:E9' => "Bulan\n(M)",
            'F8:F9' => "Qty\n(Q)",
            'G8:H9' => 'Harga  (P)',
            'I8:J9' => 'Jumlah(DxQxP/M)',
            // 'K8:L9' => '',
        ];

        foreach ($headers8 as $range => $text) {
            $sheet->mergeCells($range);
            [$startCell] = explode(':', $range);
            $sheet->setCellValue($startCell, $text);
            $sheet->getStyle($range)->applyFromArray([
                'font' => $hdrFont,
                'fill' => $hdrFill,
                'alignment' => $center,
                'borders' => $allBorder,
            ]);
        }

        // Sub-header row 9
        foreach (['B9' => 'Awal', 'C9' => 'Akhir'] as $cell => $text) {
            $sheet->setCellValue($cell, $text);
            $sheet->getStyle($cell)->applyFromArray([
                'font' => $hdrFont,
                'fill' => $hdrFill,
                'alignment' => $center,
                'borders' => $allBorder,
            ]);
        }

        $sheet->getRowDimension(8)->setRowHeight(30);
        $sheet->getRowDimension(9)->setRowHeight(18);

        // ── DATA ROWS ─────────────────────────────────────────
        $r = $this->startDataRow;
        foreach ($movements as $mov) {
            $days = max(1, $startDate->diffInDays($endDate));
            $dailyRate = $mov->tool->daily_rate ?? 0;
            $toolName = $mov->tool->name ?? $mov->tool_id;

            $sheet->getRowDimension($r)->setRowHeight(16);

            $sheet->setCellValue("A{$r}", $toolName);
            $sheet->setCellValue("B{$r}", $startDate->format('d-M-y'));
            $sheet->setCellValue("C{$r}", $endDate->format('d-M-y'));
            $sheet->setCellValue("D{$r}", $days);
            $sheet->setCellValue("E{$r}", 30);
            $sheet->setCellValue("F{$r}", $mov->quantity);
            $sheet->setCellValue("G{$r}", 'Rp');
            $sheet->setCellValue("H{$r}", $dailyRate);
            $sheet->setCellValue("I{$r}", 'Rp');
            $sheet->setCellValue("J{$r}", "=D{$r}/E{$r}*F{$r}*H{$r}");
            // $sheet->setCellValue("K{$r}", 'Rp');
            // $sheet->setCellValue("L{$r}", "=D{$r}/E{$r}*F{$r}*H{$r}");

            $sheet->getStyle("A{$r}:J{$r}")->applyFromArray([
                'font' => ['size' => 9, 'name' => 'Arial'],
                'borders' => $allBorder,
            ]);
            $sheet
                ->getStyle("B{$r}:C{$r}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet
                ->getStyle("D{$r}:F{$r}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet
                ->getStyle("G{$r}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet
                ->getStyle("H{$r}")
                ->getNumberFormat()
                ->setFormatCode($IDR);
            $sheet
                ->getStyle("H{$r}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet
                ->getStyle("I{$r}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet
                ->getStyle("J{$r}")
                ->getNumberFormat()
                ->setFormatCode($IDR);
            $sheet
                ->getStyle("J{$r}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $r++;
        }

        $lastDataRow = $r - 1;

        // ── SUMMARY: TOTAL / DPP / PPN / GRAND TOTAL ─────────
        $summaries = [['TOTAL', "=SUM(J{$this->startDataRow}:J{$lastDataRow})", 'F2F2F2'], ['DPP 11/12%', '=J' . ($lastDataRow + 1) . '*11/12', 'F2F2F2'], ['PPN 12%', '=J' . ($lastDataRow + 2) . '*12/100', 'F2F2F2'], ['GRAND TOTAL', '=J' . ($lastDataRow + 2) . '+J' . ($lastDataRow + 3), 'C6EFCE']];

        foreach ($summaries as [$label, $formula, $bg]) {
            $sheet->mergeCells("A{$r}:H{$r}");
            $sheet->setCellValue("A{$r}", $label);
            $sheet->setCellValue("I{$r}", 'Rp');
            $sheet->setCellValue("J{$r}", $formula);

            $sheet->getStyle("A{$r}:J{$r}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'name' => 'Arial'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => $allBorder,
            ]);
            $sheet
                ->getStyle("J{$r}")
                ->getNumberFormat()
                ->setFormatCode($IDR);
            $sheet->getRowDimension($r)->setRowHeight(18);
            $r++;
        }

        $grandRow = $r - 1;

        // ── TERBILANG ─────────────────────────────────────────
        $r += 1;
        $sheet->getRowDimension($r)->setRowHeight(14);
        $sheet->mergeCells("A{$r}:J{$r}");
        $sheet->setCellValue("A{$r}", 'Terbilang :');
        $sheet
            ->getStyle("A{$r}")
            ->getFont()
            ->setBold(true)
            ->setSize(9)
            ->setName('Arial');

        $r++;
        $sheet->getRowDimension($r)->setRowHeight(20);
        $sheet->mergeCells("A{$r}:J{$r}");
        $sheet->setCellValue("A{$r}", '# ' . ($rental->amount_in_words ?? 'Silahkan isi jumlah uang dalam kata...') . ' #');
        $sheet->getStyle("A{$r}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['outline' => $thick],
        ]);

        // ── FOOTER ────────────────────────────────────────────
        $r += 2;
        $sheet->mergeCells("A{$r}:D{$r}");
        $sheet->setCellValue("A{$r}", 'Balikpapan, ' . now()->translatedFormat('d F Y'));
        $sheet
            ->getStyle("A{$r}")
            ->getFont()
            ->setSize(9)
            ->setName('Arial');

        $sheet->mergeCells("E{$r}:J{$r}");
        $sheet->setCellValue("E{$r}", 'Pembayaran ditransfer ke :');
        $sheet
            ->getStyle("E{$r}")
            ->getFont()
            ->setBold(true)
            ->setSize(9)
            ->setName('Arial');

        $bankRows = [['Nama Bank', 'BRI'], ['Atas Nama', 'PT. TIANG KARUNIA NUSANTARA'], ['No Rek', '181-909-714-3']];
        $r++;
        $sheet->mergeCells("A{$r}:D{$r}");
        $sheet->setCellValue("A{$r}", 'Hormat Kami,');
        $sheet
            ->getStyle("A{$r}")
            ->getFont()
            ->setSize(9)
            ->setName('Arial');

        foreach ($bankRows as [$lbl, $val]) {
            // Label
            $sheet->mergeCells("E{$r}:F{$r}");
            $sheet->setCellValue("E{$r}", $lbl);

            // Titik dua
            $sheet->setCellValue("G{$r}", ':');

            // Value
            $sheet->mergeCells("H{$r}:J{$r}");
            $sheet->setCellValue("H{$r}", $val);

            // Styling
            $sheet->getStyle("E{$r}:J{$r}")->applyFromArray([
                'font' => ['size' => 9, 'name' => 'Arial'],
                'borders' => $allBorder,
            ]);

            $sheet->getStyle("E{$r}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 9],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2'],
                ],
            ]);

            // Alignment biar rapi
            $sheet
                ->getStyle("G{$r}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet
                ->getStyle("H{$r}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $r++;
        }

        // TTD
        $r += 3;
        $sheet->mergeCells("A{$r}:F{$r}");
        $sheet->setCellValue("A{$r}", 'Raindi Andreas');
        $sheet
            ->getStyle("A{$r}")
            ->getFont()
            ->setBold(true)
            ->setSize(9)
            ->setName('Arial');
        $r++;
        $sheet->mergeCells("A{$r}:F{$r}");
        $sheet->setCellValue("A{$r}", 'Direktur');
        $sheet
            ->getStyle("A{$r}")
            ->getFont()
            ->setSize(9)
            ->setName('Arial');

        // ── Page setup ────────────────────────────────────────
        $sheet->getPageSetup()->setOrientation('landscape');
        $sheet->getPageSetup()->setPaperSize(9); // A4
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setTop(0.75);
        $sheet->getPageMargins()->setBottom(0.75);
    }
}

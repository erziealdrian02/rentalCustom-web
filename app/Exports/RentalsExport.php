<?php

namespace App\Exports;

use App\Models\Rentals;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font as SpFont;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RentalsExport implements FromArray, WithEvents, WithTitle
{
    protected Rentals $rental;

    public function __construct(string $rentalId)
    {
        $this->rental = Rentals::with('customer')->findOrFail($rentalId);
    }

    public function title(): string
    {
        return 'Invoice';
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
        $rental = $this->rental;
        $customer = $rental->customer;

        $startDate = \Carbon\Carbon::parse($rental->rental_start_date)->translatedFormat('d F Y');
        $endDate = \Carbon\Carbon::parse($rental->rental_end_date)->translatedFormat('d F Y');
        $createdAt = \Carbon\Carbon::parse($rental->created_at)->translatedFormat('d F Y');

        $thin = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']];
        $thick = ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']];
        $IDR = '#,##0';

        // ── Column widths ─────────────────────────────────────
        $ws->getColumnDimension('A')->setWidth(3);
        $ws->getColumnDimension('B')->setWidth(10);
        $ws->getColumnDimension('C')->setWidth(50);
        $ws->getColumnDimension('D')->setWidth(3);
        $ws->getColumnDimension('E')->setWidth(14);
        $ws->getColumnDimension('F')->setWidth(18);

        // ── Helper ────────────────────────────────────────────
        $set = function (int $row, int $col, $value = '', bool $bold = false, int $size = 10, string $color = '000000', ?string $bg = null, string $halign = 'left', bool $wrap = false, ?string $numFmt = null, bool $italic = false, ?string $underline = null, ?array $border = null) use ($ws, $thin) {
            $c = $ws->getCellByColumnAndRow($col, $row);
            if ($c->isInMergeRange()) {
                return;
            }
            $c->setValue($value);
            $font = $ws->getStyleByColumnAndRow($col, $row)->getFont();
            $font->setName('Arial')->setSize($size)->setBold($bold)->setItalic($italic);
            $font->getColor()->setRGB($color);
            if ($underline) {
                $font->setUnderline($underline);
            }
            $ws->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal($halign)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText($wrap);
            if ($bg) {
                $ws->getStyleByColumnAndRow($col, $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bg);
            }
            if ($numFmt) {
                $ws->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode($numFmt);
            }
            if ($border !== null) {
                $ws->getStyleByColumnAndRow($col, $row)->getBorders()->applyFromArray($border);
            }
        };

        $mrg = fn(int $r1, int $c1, int $r2, int $c2) => $ws->mergeCellsByColumnAndRow($c1, $r1, $c2, $r2);

        $rh = fn(int $row, float $h) => $ws->getRowDimension($row)->setRowHeight($h);

        $bdrAll = ['allBorders' => $thin];

        // ══════════════════════════════════════════════════════
        // KOP SURAT
        // ══════════════════════════════════════════════════════

        // ROW 1: Nama perusahaan
        $mrg(1, 2, 1, 4);
        $set(1, 2, 'PT. TIANG KARUNIA NUSANTARA', true, 11, '000000', null, 'left', false, null, false, SpFont::UNDERLINE_SINGLE);
        $rh(1, 18);

        $rh(2, 6);

        // ROW 3-4: Sub-judul
        $mrg(3, 2, 3, 4);
        $set(3, 2, 'DISTRIBUTION & GENERAL CONTRACTOR', true, 9);
        $rh(3, 14);

        $mrg(4, 2, 4, 4);
        $set(4, 2, 'Import & Export', false, 9);
        $rh(4, 13);

        // ROW 5: Alamat
        $mrg(5, 2, 5, 6);
        $set(5, 2, 'Jl. Letjend TNI ZA Maulani RT. 021 Rw. 000 Sungainangka, ' . 'Balikpapan Selatan Kota Balikpapan, Kalimantan Timur', false, 9, '000000', null, 'left', true);
        $rh(5, 13);

        // ROW 6: Garis bawah kop
        for ($c = 2; $c <= 6; $c++) {
            $ws->getStyleByColumnAndRow($c, 6)
                ->getBorders()
                ->applyFromArray(['bottom' => $thin]);
        }
        $rh(6, 6);

        // ══════════════════════════════════════════════════════
        // JUDUL INVOICE
        // ══════════════════════════════════════════════════════

        // ROW 7: INVOICE
        $mrg(7, 2, 7, 6);
        $set(7, 2, 'INVOICE', true, 14, '000000', null, 'center');
        $rh(7, 22);

        // ROW 8: Nomor invoice
        $mrg(8, 2, 8, 6);
        $set(8, 2, 'No : ' . $rental->invoice_number, false, 11, '000000', null, 'center');
        $rh(8, 18);

        // ROW 9: Tanggal (kanan)
        $mrg(9, 5, 9, 6);
        $set(9, 5, 'Tanggal : ' . $createdAt, false, 10, '000000', null, 'right');
        $rh(9, 14);

        $rh(10, 6);

        // ROW 11-12: Kepada
        $mrg(11, 2, 11, 4);
        $set(11, 2, 'Kepada Yth,');
        $rh(11, 14);

        $mrg(12, 2, 12, 4);
        $set(12, 2, $customer->name ?? 'N/A');
        $rh(12, 14);

        $rh(13, 6);
        $rh(14, 6);

        // ══════════════════════════════════════════════════════
        // TABEL INVOICE
        // ══════════════════════════════════════════════════════

        // ROW 15: Header
        $set(15, 2, 'PO', true, 10, '000000', null, 'center', false, null, false, null, $bdrAll);
        $set(15, 3, 'URAIAN', true, 10, '000000', null, 'center', false, null, false, null, $bdrAll);
        $mrg(15, 5, 15, 6);
        $set(15, 5, 'JUMLAH', true, 10, '000000', null, 'center', false, null, false, null, $bdrAll);
        // border kolom D (spacer di tengah)
        $ws->getStyleByColumnAndRow(4, 15)->getBorders()->applyFromArray($bdrAll);
        $rh(15, 20);

        // ROW 16: Nomor PO + Nama proyek
        $set(16, 2, '1', false, 10, '000000', null, 'center', false, null, false, null, $bdrAll);
        $mrg(16, 3, 16, 4);
        $set(16, 3, 'Proyek : ' . ($rental->project_name ?? 'Pembangunan Penataan Kawasan Olahraga Dan Ruang Terbuka Hijau'), false, 10, '000000', null, 'left', true, null, false, null, $bdrAll);
        $set(16, 5, '', false, 10, '000000', null, 'right', false, null, false, null, $bdrAll);
        $set(16, 6, '', false, 10, '000000', null, 'right', false, null, false, null, $bdrAll);
        $rh(16, 18);

        // ROW 17: Periode + Harga
        $set(17, 2, '', false, 10, '000000', null, 'center', false, null, false, null, $bdrAll);
        $mrg(17, 3, 17, 4);
        $set(17, 3, 'Periode Sewa : ' . $startDate . ' s/d ' . $endDate, false, 10, '000000', null, 'left', false, null, false, null, $bdrAll);
        $set(17, 5, 'Rp', false, 10, '000000', null, 'right', false, null, false, null, $bdrAll);
        $set(17, 6, $rental->total_price, false, 10, '000000', null, 'right', false, $IDR, false, null, $bdrAll);
        $rh(17, 18);

        // ROW 18-22: baris kosong
        for ($r = 18; $r <= 22; $r++) {
            $set($r, 2, '', false, 10, '000000', null, 'left', false, null, false, null, $bdrAll);
            $mrg($r, 3, $r, 4);
            $set($r, 3, '', false, 10, '000000', null, 'left', false, null, false, null, $bdrAll);
            $set($r, 5, '', false, 10, '000000', null, 'left', false, null, false, null, $bdrAll);
            $set($r, 6, '', false, 10, '000000', null, 'left', false, null, false, null, $bdrAll);
            $rh($r, 14);
        }

        // ROW 23: Sub-total
        $set(23, 2, '', false, 10, '000000', null, 'left', false, null, false, null, $bdrAll);
        $mrg(23, 3, 23, 4);
        $set(23, 3, '.', false, 10, '000000', null, 'right', false, null, false, null, $bdrAll);
        $set(23, 5, 'Rp', false, 10, '000000', null, 'right', false, null, false, null, $bdrAll);
        $set(23, 6, $rental->total_price, false, 10, '000000', null, 'right', false, $IDR, false, null, $bdrAll);
        $rh(23, 18);

        // ROW 24: DPP 11/12
        $set(24, 2, '', false, 10, '000000', null, 'left', false, null, false, null, $bdrAll);
        $mrg(24, 3, 24, 4);
        $set(24, 3, 'DPP 11/12', false, 10, '000000', null, 'right', false, null, false, null, $bdrAll);
        $set(24, 5, 'Rp', false, 10, '000000', null, 'right', false, null, false, null, $bdrAll);
        $ws->getCellByColumnAndRow(6, 24)->setValue('=F23*11/12');
        $ws->getStyleByColumnAndRow(6, 24)->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => $thin],
            'numberFormat' => ['formatCode' => $IDR],
        ]);
        $rh(24, 18);

        // ROW 25: PPN 12%
        $set(25, 2, '', false, 10, '000000', null, 'left', false, null, false, null, $bdrAll);
        $mrg(25, 3, 25, 4);
        $set(25, 3, 'PPN 12 %', false, 10, '000000', null, 'right', false, null, false, null, $bdrAll);
        $set(25, 5, 'Rp', false, 10, '000000', null, 'right', false, null, false, null, $bdrAll);
        $ws->getCellByColumnAndRow(6, 25)->setValue('=F24*12/100');
        $ws->getStyleByColumnAndRow(6, 25)->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => $thin],
            'numberFormat' => ['formatCode' => $IDR],
        ]);
        $rh(25, 18);

        // ROW 26: GRAND TOTAL
        $set(26, 2, '', false, 10, '000000', null, 'left', false, null, false, null, $bdrAll);
        $mrg(26, 3, 26, 4);
        $set(26, 3, 'GRAND TOTAL', true, 10, '000000', null, 'right', false, null, false, null, $bdrAll);
        $set(26, 5, 'Rp', false, 10, '000000', null, 'right', false, null, false, null, $bdrAll);
        $ws->getCellByColumnAndRow(6, 26)->setValue('=F23+F25');
        $ws->getStyleByColumnAndRow(6, 26)->applyFromArray([
            'font' => ['name' => 'Arial', 'bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => $thin],
            'numberFormat' => ['formatCode' => $IDR],
        ]);
        $rh(26, 18);

        // ROW 27-28: Terbilang
        $mrg(27, 2, 27, 6);
        $set(27, 2, 'Terbilang :', false, 10, '000000', null, 'left', false, null, false, null, ['top' => $thin, 'left' => $thin, 'right' => $thin]);
        $rh(27, 14);

        $mrg(28, 2, 28, 6);
        $set(28, 2, '# ' . ($rental->amount_in_words ?? 'Seratus Tujuh Puluh Tiga Juta Sembilan Ratus Tujuh Puluh Delapan Ribu Tujuh Puluh Satu Rupiah') . ' #', false, 9, '000000', null, 'center', true, null, false, null, ['bottom' => $thin, 'left' => $thin, 'right' => $thin]);
        $rh(28, 16);

        // Outline tebal area tabel
        $tableStyle = $ws->getStyle('B15:F28');
        $tableStyle->getBorders()->applyFromArray(['outline' => $thick]);

        $rh(29, 8);

        // ══════════════════════════════════════════════════════
        // FOOTER
        // ══════════════════════════════════════════════════════

        // ROW 30: Info transfer
        $mrg(30, 2, 30, 6);
        $set(30, 2, 'Pembayaran Untuk Invoice ini dimohon ditransfer ke rekening :');
        $rh(30, 14);

        // ROW 31-33: Bank
        $bankInfo = [
            31 => ['Nama Bank', ': BNI'],
            32 => ['Atas Nama', ': PT. TIANG KARUNIA NUSANTARA'],
            33 => ['No Rek', ': 181-909-714-3'],
        ];
        foreach ($bankInfo as $r => [$label, $val]) {
            $set($r, 2, $label);
            $mrg($r, 3, $r, 6);
            $set($r, 3, $val);
            $rh($r, 14);
        }

        $rh(34, 8);

        // ROW 35-36: Note
        $mrg(35, 2, 35, 6);
        $set(35, 2, 'Note : Apabila Pembayaran dilakukan ke rekening selain tersebut diatas,');
        $rh(35, 14);

        $mrg(36, 2, 36, 6);
        $set(36, 2, '        maka pembayaran dianggap belum lunas.', false, 10, '000000', null, 'left', false, null, true);
        $rh(36, 14);

        // ROW 37-38: Hormat Kami
        $mrg(37, 5, 37, 6);
        $set(37, 5, 'Hormat Kami,', false, 10, '000000', null, 'right');
        $rh(37, 14);

        $mrg(38, 5, 38, 6);
        $set(38, 5, 'PT. TIANG KARUNIA NUSANTARA', true, 10, '000000', null, 'right');
        $rh(38, 14);

        for ($r = 39; $r <= 43; $r++) {
            $rh($r, 14);
        }

        $mrg(44, 5, 44, 6);
        $set(44, 5, 'Raindi Andreas', true, 10, '000000', null, 'right', false, null, false, SpFont::UNDERLINE_SINGLE);
        $rh(44, 14);

        $mrg(45, 5, 45, 6);
        $set(45, 5, 'Direktur', false, 10, '000000', null, 'right');
        $rh(45, 14);

        // Page setup A4 portrait
        $ws->getPageSetup()->setOrientation('portrait')->setPaperSize(9);
        $ws->getPageMargins()->setLeft(0.7)->setRight(0.7)->setTop(0.75)->setBottom(0.75);
    }
}

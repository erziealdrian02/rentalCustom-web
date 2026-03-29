<?php

namespace App\Exports;

use App\Models\Rentals;
use App\Models\Tools;
use App\Models\Customers;
use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

    class RentalsExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [];
    }

    /**
    * @return array
    */
    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    /**
    * @return array
    */
    public function registerEvents(): array
    {
        return [];
    }
}

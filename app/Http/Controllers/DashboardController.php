<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $tools = $this->getTools();
        $rentals = $this->getRentals();
        $returns = $this->getReturns();
        $monthlyRevenue = $this->getMonthlyRevenue();
        $monthlyRentals = $this->getMonthlyRentals();
        $toolStatusDistribution = $this->getToolStatusDistribution();
        $stats = $this->getDashboardStats();

        return view('dashboard', compact('tools', 'rentals', 'returns', 'monthlyRevenue', 'monthlyRentals', 'toolStatusDistribution', 'stats'));
    }

    // ----------------------------------------------------------------
    // Dummy data methods — ganti dengan Model query saat DB sudah siap
    // ----------------------------------------------------------------

    private function getTools(): array
    {
        return [
            ['id' => 1, 'name' => 'Concrete Mixer', 'category' => 'Heavy Equipment', 'dailyRate' => 150, 'status' => 'Available', 'qty' => 5],
            ['id' => 2, 'name' => 'Jackhammer', 'category' => 'Demolition', 'dailyRate' => 80, 'status' => 'Rented', 'qty' => 3],
            ['id' => 3, 'name' => 'Scaffolding Set', 'category' => 'Construction', 'dailyRate' => 200, 'status' => 'Available', 'qty' => 8],
            ['id' => 4, 'name' => 'Generator 5KVA', 'category' => 'Power', 'dailyRate' => 120, 'status' => 'Rented', 'qty' => 2],
            ['id' => 5, 'name' => 'Angle Grinder', 'category' => 'Power Tools', 'dailyRate' => 40, 'status' => 'Available', 'qty' => 10],
            ['id' => 6, 'name' => 'Water Pump', 'category' => 'Pumping', 'dailyRate' => 60, 'status' => 'Maintenance', 'qty' => 1],
            ['id' => 7, 'name' => 'Welding Machine', 'category' => 'Welding', 'dailyRate' => 90, 'status' => 'Available', 'qty' => 4],
            ['id' => 8, 'name' => 'Compressor 50L', 'category' => 'Pneumatic', 'dailyRate' => 70, 'status' => 'Rented', 'qty' => 3],
        ];
    }

    private function getRentals(): array
    {
        return [
            [
                'id' => 1,
                'invoiceNumber' => 'INV-2025-001',
                'customerName' => 'PT. Maju Jaya',
                'rentalStatus' => 'Active',
                'createdDate' => '2025-01-05',
                'rentalStartDate' => '2025-01-06',
                'rentalEndDate' => '2025-01-13',
                'driverName' => 'Budi Santoso',
                'deliveryLocation' => 'Jl. Raya Bogor No. 12',
                'estimatedDeliveryTime' => '08:00 - 10:00',
                'totalPrice' => 2100,
                'items' => [['toolName' => 'Concrete Mixer', 'quantity' => 2, 'dailyRate' => 150, 'subtotal' => 2100]],
            ],
            [
                'id' => 2,
                'invoiceNumber' => 'INV-2025-002',
                'customerName' => 'CV. Bangun Sejahtera',
                'rentalStatus' => 'Completed',
                'createdDate' => '2025-01-08',
                'rentalStartDate' => '2025-01-09',
                'rentalEndDate' => '2025-01-14',
                'driverName' => 'Andi Wijaya',
                'deliveryLocation' => 'Jl. Sudirman No. 45',
                'estimatedDeliveryTime' => '09:00 - 11:00',
                'totalPrice' => 1600,
                'items' => [['toolName' => 'Generator 5KVA', 'quantity' => 2, 'dailyRate' => 120, 'subtotal' => 1200], ['toolName' => 'Angle Grinder', 'quantity' => 1, 'dailyRate' => 40, 'subtotal' => 200]],
            ],
            [
                'id' => 3,
                'invoiceNumber' => 'INV-2025-003',
                'customerName' => 'Toko Bangunan Makmur',
                'rentalStatus' => 'Pending',
                'createdDate' => '2025-01-10',
                'rentalStartDate' => '2025-01-12',
                'rentalEndDate' => '2025-01-19',
                'driverName' => null,
                'deliveryLocation' => 'Jl. Gatot Subroto No. 8',
                'estimatedDeliveryTime' => null,
                'totalPrice' => 1400,
                'items' => [['toolName' => 'Jackhammer', 'quantity' => 1, 'dailyRate' => 80, 'subtotal' => 560], ['toolName' => 'Compressor 50L', 'quantity' => 1, 'dailyRate' => 70, 'subtotal' => 490]],
            ],
            [
                'id' => 4,
                'invoiceNumber' => 'INV-2025-004',
                'customerName' => 'PT. Konstruksi Andalan',
                'rentalStatus' => 'Active',
                'createdDate' => '2025-01-12',
                'rentalStartDate' => '2025-01-13',
                'rentalEndDate' => '2025-01-20',
                'driverName' => 'Rudi Hartono',
                'deliveryLocation' => 'Jl. MT. Haryono No. 22',
                'estimatedDeliveryTime' => '07:30 - 09:30',
                'totalPrice' => 3200,
                'items' => [['toolName' => 'Scaffolding Set', 'quantity' => 2, 'dailyRate' => 200, 'subtotal' => 2800]],
            ],
            [
                'id' => 5,
                'invoiceNumber' => 'INV-2025-005',
                'customerName' => 'CV. Karya Utama',
                'rentalStatus' => 'Overdue',
                'createdDate' => '2025-01-02',
                'rentalStartDate' => '2025-01-03',
                'rentalEndDate' => '2025-01-08',
                'driverName' => 'Slamet Riyadi',
                'deliveryLocation' => 'Jl. Ahmad Yani No. 5',
                'estimatedDeliveryTime' => '10:00 - 12:00',
                'totalPrice' => 900,
                'items' => [['toolName' => 'Water Pump', 'quantity' => 1, 'dailyRate' => 60, 'subtotal' => 300], ['toolName' => 'Welding Machine', 'quantity' => 1, 'dailyRate' => 90, 'subtotal' => 450]],
            ],
        ];
    }

    private function getReturns(): array
    {
        return [
            [
                'id' => 1,
                'returnId' => 'RET-2025-001',
                'invoiceNumber' => 'INV-2025-002',
                'customerName' => 'CV. Bangun Sejahtera',
                'status' => 'Completed',
                'originalRentalDate' => '2025-01-09',
                'requestedReturnDate' => '2025-01-14',
                'actualReturnDate' => '2025-01-14',
                'originalRevenue' => 1600,
                'totalAuditRevenueLoss' => 200,
                'items' => [
                    [
                        'toolName' => 'Generator 5KVA',
                        'quantity' => 2,
                        'condition' => 'Good',
                        'auditDetails' => ['good' => 2, 'damaged' => 0, 'lost' => 0, 'sold' => 0],
                    ],
                    [
                        'toolName' => 'Angle Grinder',
                        'quantity' => 1,
                        'condition' => 'Damaged',
                        'auditDetails' => ['good' => 0, 'damaged' => 1, 'lost' => 0, 'sold' => 0],
                    ],
                ],
            ],
            [
                'id' => 2,
                'returnId' => 'RET-2025-002',
                'invoiceNumber' => 'INV-2025-001',
                'customerName' => 'PT. Maju Jaya',
                'status' => 'Pending',
                'originalRentalDate' => '2025-01-06',
                'requestedReturnDate' => '2025-01-13',
                'actualReturnDate' => null,
                'originalRevenue' => 2100,
                'totalAuditRevenueLoss' => 0,
                'items' => [
                    [
                        'toolName' => 'Concrete Mixer',
                        'quantity' => 2,
                        'condition' => 'Pending',
                        'auditDetails' => ['good' => 0, 'damaged' => 0, 'lost' => 0, 'sold' => 0],
                    ],
                ],
            ],
            [
                'id' => 3,
                'returnId' => 'RET-2025-003',
                'invoiceNumber' => 'INV-2024-098',
                'customerName' => 'UD. Sejahtera',
                'status' => 'Completed',
                'originalRentalDate' => '2024-12-20',
                'requestedReturnDate' => '2024-12-27',
                'actualReturnDate' => '2024-12-28',
                'originalRevenue' => 840,
                'totalAuditRevenueLoss' => 90,
                'items' => [
                    [
                        'toolName' => 'Welding Machine',
                        'quantity' => 1,
                        'condition' => 'Lost',
                        'auditDetails' => ['good' => 0, 'damaged' => 0, 'lost' => 1, 'sold' => 0],
                    ],
                ],
            ],
        ];
    }

    private function getMonthlyRevenue(): array
    {
        return [['month' => 'Aug', 'revenue' => 12400], ['month' => 'Sep', 'revenue' => 15800], ['month' => 'Oct', 'revenue' => 13200], ['month' => 'Nov', 'revenue' => 18900], ['month' => 'Dec', 'revenue' => 22100], ['month' => 'Jan', 'revenue' => 19500]];
    }

    private function getMonthlyRentals(): array
    {
        return [['month' => 'Aug', 'rentals' => 24], ['month' => 'Sep', 'rentals' => 31], ['month' => 'Oct', 'rentals' => 27], ['month' => 'Nov', 'rentals' => 38], ['month' => 'Dec', 'rentals' => 44], ['month' => 'Jan', 'rentals' => 36]];
    }

    private function getToolStatusDistribution(): array
    {
        return [['label' => 'Available', 'value' => 17, 'color' => '#22c55e'], ['label' => 'Rented', 'value' => 8, 'color' => '#3b82f6'], ['label' => 'Maintenance', 'value' => 1, 'color' => '#f59e0b'], ['label' => 'Damaged', 'value' => 2, 'color' => '#ef4444']];
    }

    private function getDashboardStats(): array
    {
        return [
            'totalTools' => 28,
            'toolsAvailable' => 17,
            'toolsRented' => 8,
            'activeRentals' => 2,
            'monthlyRevenue' => 19500,
        ];
    }
}

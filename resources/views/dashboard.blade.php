@extends('layout.app')

@section('content')
    {{-- Page Content --}}
    <main id="page-content" class="flex-1 p-8">
        {{-- Content rendered by JS below --}}
    </main>

    {{-- ===================== ALL JS INLINE ===================== --}}
    <script>
        // ============================================================
        // dummy-data.js  (embedded)
        // ============================================================
        const dummyData = {
            tools: [{
                    id: 1,
                    name: 'Concrete Mixer',
                    category: 'Heavy Equipment',
                    dailyRate: 150,
                    status: 'Available',
                    qty: 5
                },
                {
                    id: 2,
                    name: 'Jackhammer',
                    category: 'Demolition',
                    dailyRate: 80,
                    status: 'Rented',
                    qty: 3
                },
                {
                    id: 3,
                    name: 'Scaffolding Set',
                    category: 'Construction',
                    dailyRate: 200,
                    status: 'Available',
                    qty: 8
                },
                {
                    id: 4,
                    name: 'Generator 5KVA',
                    category: 'Power',
                    dailyRate: 120,
                    status: 'Rented',
                    qty: 2
                },
                {
                    id: 5,
                    name: 'Angle Grinder',
                    category: 'Power Tools',
                    dailyRate: 40,
                    status: 'Available',
                    qty: 10
                },
                {
                    id: 6,
                    name: 'Water Pump',
                    category: 'Pumping',
                    dailyRate: 60,
                    status: 'Maintenance',
                    qty: 1
                },
                {
                    id: 7,
                    name: 'Welding Machine',
                    category: 'Welding',
                    dailyRate: 90,
                    status: 'Available',
                    qty: 4
                },
                {
                    id: 8,
                    name: 'Compressor 50L',
                    category: 'Pneumatic',
                    dailyRate: 70,
                    status: 'Rented',
                    qty: 3
                },
            ],

            rentals: [{
                    id: 1,
                    invoiceNumber: 'INV-2025-001',
                    customerName: 'PT. Maju Jaya',
                    rentalStatus: 'Active',
                    createdDate: '2025-01-05',
                    rentalStartDate: '2025-01-06',
                    rentalEndDate: '2025-01-13',
                    driverName: 'Budi Santoso',
                    deliveryLocation: 'Jl. Raya Bogor No. 12',
                    estimatedDeliveryTime: '08:00 - 10:00',
                    totalPrice: 2100,
                    items: [{
                        toolName: 'Concrete Mixer',
                        quantity: 2,
                        dailyRate: 150,
                        subtotal: 2100
                    }, ]
                },
                {
                    id: 2,
                    invoiceNumber: 'INV-2025-002',
                    customerName: 'CV. Bangun Sejahtera',
                    rentalStatus: 'Completed',
                    createdDate: '2025-01-08',
                    rentalStartDate: '2025-01-09',
                    rentalEndDate: '2025-01-14',
                    driverName: 'Andi Wijaya',
                    deliveryLocation: 'Jl. Sudirman No. 45',
                    estimatedDeliveryTime: '09:00 - 11:00',
                    totalPrice: 1600,
                    items: [{
                            toolName: 'Generator 5KVA',
                            quantity: 2,
                            dailyRate: 120,
                            subtotal: 1200
                        },
                        {
                            toolName: 'Angle Grinder',
                            quantity: 1,
                            dailyRate: 40,
                            subtotal: 200
                        },
                    ]
                },
                {
                    id: 3,
                    invoiceNumber: 'INV-2025-003',
                    customerName: 'Toko Bangunan Makmur',
                    rentalStatus: 'Pending',
                    createdDate: '2025-01-10',
                    rentalStartDate: '2025-01-12',
                    rentalEndDate: '2025-01-19',
                    driverName: null,
                    deliveryLocation: 'Jl. Gatot Subroto No. 8',
                    estimatedDeliveryTime: null,
                    totalPrice: 1400,
                    items: [{
                            toolName: 'Jackhammer',
                            quantity: 1,
                            dailyRate: 80,
                            subtotal: 560
                        },
                        {
                            toolName: 'Compressor 50L',
                            quantity: 1,
                            dailyRate: 70,
                            subtotal: 490
                        },
                    ]
                },
                {
                    id: 4,
                    invoiceNumber: 'INV-2025-004',
                    customerName: 'PT. Konstruksi Andalan',
                    rentalStatus: 'Active',
                    createdDate: '2025-01-12',
                    rentalStartDate: '2025-01-13',
                    rentalEndDate: '2025-01-20',
                    driverName: 'Rudi Hartono',
                    deliveryLocation: 'Jl. MT. Haryono No. 22',
                    estimatedDeliveryTime: '07:30 - 09:30',
                    totalPrice: 3200,
                    items: [{
                        toolName: 'Scaffolding Set',
                        quantity: 2,
                        dailyRate: 200,
                        subtotal: 2800
                    }, ]
                },
                {
                    id: 5,
                    invoiceNumber: 'INV-2025-005',
                    customerName: 'CV. Karya Utama',
                    rentalStatus: 'Overdue',
                    createdDate: '2025-01-02',
                    rentalStartDate: '2025-01-03',
                    rentalEndDate: '2025-01-08',
                    driverName: 'Slamet Riyadi',
                    deliveryLocation: 'Jl. Ahmad Yani No. 5',
                    estimatedDeliveryTime: '10:00 - 12:00',
                    totalPrice: 900,
                    items: [{
                            toolName: 'Water Pump',
                            quantity: 1,
                            dailyRate: 60,
                            subtotal: 300
                        },
                        {
                            toolName: 'Welding Machine',
                            quantity: 1,
                            dailyRate: 90,
                            subtotal: 450
                        },
                    ]
                },
            ],

            returns: [{
                    id: 1,
                    returnId: 'RET-2025-001',
                    invoiceNumber: 'INV-2025-002',
                    customerName: 'CV. Bangun Sejahtera',
                    status: 'Completed',
                    originalRentalDate: '2025-01-09',
                    requestedReturnDate: '2025-01-14',
                    actualReturnDate: '2025-01-14',
                    originalRevenue: 1600,
                    totalAuditRevenueLoss: 200,
                    items: [{
                            toolName: 'Generator 5KVA',
                            quantity: 2,
                            condition: 'Good',
                            auditDetails: {
                                good: 2,
                                damaged: 0,
                                lost: 0,
                                sold: 0
                            }
                        },
                        {
                            toolName: 'Angle Grinder',
                            quantity: 1,
                            condition: 'Damaged',
                            auditDetails: {
                                good: 0,
                                damaged: 1,
                                lost: 0,
                                sold: 0
                            }
                        },
                    ]
                },
                {
                    id: 2,
                    returnId: 'RET-2025-002',
                    invoiceNumber: 'INV-2025-001',
                    customerName: 'PT. Maju Jaya',
                    status: 'Pending',
                    originalRentalDate: '2025-01-06',
                    requestedReturnDate: '2025-01-13',
                    actualReturnDate: null,
                    originalRevenue: 2100,
                    totalAuditRevenueLoss: 0,
                    items: [{
                        toolName: 'Concrete Mixer',
                        quantity: 2,
                        condition: 'Pending',
                        auditDetails: {
                            good: 0,
                            damaged: 0,
                            lost: 0,
                            sold: 0
                        }
                    }, ]
                },
                {
                    id: 3,
                    returnId: 'RET-2025-003',
                    invoiceNumber: 'INV-2024-098',
                    customerName: 'UD. Sejahtera',
                    status: 'Completed',
                    originalRentalDate: '2024-12-20',
                    requestedReturnDate: '2024-12-27',
                    actualReturnDate: '2024-12-28',
                    originalRevenue: 840,
                    totalAuditRevenueLoss: 90,
                    items: [{
                        toolName: 'Welding Machine',
                        quantity: 1,
                        condition: 'Lost',
                        auditDetails: {
                            good: 0,
                            damaged: 0,
                            lost: 1,
                            sold: 0
                        }
                    }, ]
                },
            ],

            monthlyRevenue: [{
                    month: 'Aug',
                    revenue: 12400
                },
                {
                    month: 'Sep',
                    revenue: 15800
                },
                {
                    month: 'Oct',
                    revenue: 13200
                },
                {
                    month: 'Nov',
                    revenue: 18900
                },
                {
                    month: 'Dec',
                    revenue: 22100
                },
                {
                    month: 'Jan',
                    revenue: 19500
                },
            ],

            monthlyRentals: [{
                    month: 'Aug',
                    rentals: 24
                },
                {
                    month: 'Sep',
                    rentals: 31
                },
                {
                    month: 'Oct',
                    rentals: 27
                },
                {
                    month: 'Nov',
                    rentals: 38
                },
                {
                    month: 'Dec',
                    rentals: 44
                },
                {
                    month: 'Jan',
                    rentals: 36
                },
            ],

            toolStatusDistribution: [{
                    label: 'Available',
                    value: 17,
                    color: '#22c55e'
                },
                {
                    label: 'Rented',
                    value: 8,
                    color: '#3b82f6'
                },
                {
                    label: 'Maintenance',
                    value: 1,
                    color: '#f59e0b'
                },
                {
                    label: 'Damaged',
                    value: 2,
                    color: '#ef4444'
                },
            ],

            getDashboardStats() {
                return {
                    totalTools: 28,
                    toolsAvailable: 17,
                    toolsRented: 8,
                    activeRentals: 2,
                    monthlyRevenue: '19,500',
                };
            }
        };

        // ============================================================
        // app.js  (embedded)
        // ============================================================
        const App = {
            formatDate(dateStr) {
                if (!dateStr) return '-';
                const d = new Date(dateStr);
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            },

            getStatusBadgeColor(status) {
                const map = {
                    'Active': 'bg-blue-100 text-blue-800',
                    'Completed': 'bg-green-100 text-green-800',
                    'Pending': 'bg-yellow-100 text-yellow-800',
                    'Overdue': 'bg-red-100 text-red-800',
                    'Cancelled': 'bg-gray-100 text-gray-800',
                    'Good': 'bg-green-100 text-green-800',
                    'Damaged': 'bg-red-100 text-red-800',
                    'Lost': 'bg-red-200 text-red-900',
                    'Maintenance': 'bg-orange-100 text-orange-800',
                    'Available': 'bg-green-100 text-green-800',
                    'Rented': 'bg-blue-100 text-blue-800',
                };
                return map[status] || 'bg-gray-100 text-gray-800';
            }
        };

        // ============================================================
        // charts.js  (embedded)
        // ============================================================
        const ChartManager = {
            charts: {},

            initChart(canvasId, type, data, options = {}) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                if (this.charts[canvasId]) this.charts[canvasId].destroy();
                this.charts[canvasId] = new Chart(ctx, {
                    type,
                    data,
                    options
                });
            },

            createLineChartData(labels, values, label) {
                return {
                    labels,
                    datasets: [{
                        label,
                        data: values,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: 5,
                    }]
                };
            },

            createBarChartData(labels, values, label) {
                return {
                    labels,
                    datasets: [{
                        label,
                        data: values,
                        backgroundColor: 'rgba(59,130,246,0.7)',
                        borderColor: '#3b82f6',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                };
            },

            createPieChartData(labels, values, colors) {
                return {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                };
            },

            lineChartOptions() {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                };
            },

            barChartOptions() {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                };
            },

            pieChartOptions() {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 16,
                                boxWidth: 12
                            }
                        }
                    }
                };
            }
        };

        // ============================================================
        // Modal helpers
        // ============================================================
        function openRentalModal(rentalId) {
            const rental = dummyData.rentals.find(r => r.id === rentalId);
            if (!rental) return;

            const html = `
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4 pb-4 border-b">
                    <div>
                        <p class="text-gray-600 text-sm">Invoice Number</p>
                        <p class="text-lg font-semibold text-gray-900">${rental.invoiceNumber}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Created Date</p>
                        <p class="text-lg font-semibold text-gray-900">${App.formatDate(rental.createdDate)}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Customer</p>
                        <p class="text-lg font-semibold text-gray-900">${rental.customerName}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${App.getStatusBadgeColor(rental.rentalStatus)}">${rental.rentalStatus}</span>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Rental Period</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Start Date</p>
                            <p class="font-semibold text-gray-900">${App.formatDate(rental.rentalStartDate)}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">End Date</p>
                            <p class="font-semibold text-gray-900">${App.formatDate(rental.rentalEndDate)}</p>
                        </div>
                    </div>
                </div>

                ${rental.driverName ? `
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-900 mb-3">Delivery Information</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-gray-600 text-sm">Driver</p>
                                            <p class="font-semibold text-gray-900">${rental.driverName}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 text-sm">Delivery Location</p>
                                            <p class="font-semibold text-gray-900">${rental.deliveryLocation}</p>
                                        </div>
                                        ${rental.estimatedDeliveryTime ? `
                        <div class="col-span-2">
                            <p class="text-gray-600 text-sm">Estimated Delivery Time</p>
                            <p class="font-semibold text-gray-900">${rental.estimatedDeliveryTime}</p>
                        </div>` : ''}
                                    </div>
                                </div>` : `
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <p class="text-gray-900">Delivery not yet assigned</p>
                                </div>`}

                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Rented Items</h4>
                    <div class="space-y-2 border rounded-lg overflow-hidden">
                        ${rental.items.map((item, idx) => `
                                        <div class="flex items-center justify-between p-4 ${idx % 2 === 0 ? 'bg-gray-50' : 'bg-white'} border-b last:border-b-0">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900">${item.toolName}</p>
                                                <p class="text-sm text-gray-600">Qty: ${item.quantity} × ${App.formatCurrency(item.dailyRate)}/day</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-gray-900">${App.formatCurrency(item.subtotal)}</p>
                                            </div>
                                        </div>`).join('')}
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t-2">
                    <p class="text-lg font-bold text-gray-900">Total:</p>
                    <p class="text-2xl font-bold text-blue-600">${App.formatCurrency(rental.totalPrice)}</p>
                </div>
            </div>`;

            document.getElementById('rentalModalContent').innerHTML = html;
            document.getElementById('rentalModal').classList.add('active');
        }

        function closeRentalModal() {
            document.getElementById('rentalModal').classList.remove('active');
        }

        function printRentalModal() {
            window.print();
        }

        function openReturnModal(returnId) {
            const ret = dummyData.returns.find(r => r.id === returnId);
            if (!ret) return;

            const html = `
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4 pb-4 border-b">
                    <div>
                        <p class="text-gray-600 text-sm">Return ID</p>
                        <p class="text-lg font-semibold text-gray-900">${ret.returnId}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Invoice Number</p>
                        <p class="text-lg font-semibold text-gray-900">${ret.invoiceNumber}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Customer</p>
                        <p class="text-lg font-semibold text-gray-900">${ret.customerName}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${App.getStatusBadgeColor(ret.status)}">${ret.status}</span>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Return Timeline</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Rental Date</p>
                            <p class="font-semibold text-gray-900">${App.formatDate(ret.originalRentalDate)}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Expected Return</p>
                            <p class="font-semibold text-gray-900">${App.formatDate(ret.requestedReturnDate)}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Actual Return</p>
                            <p class="font-semibold text-gray-900">${ret.actualReturnDate ? App.formatDate(ret.actualReturnDate) : 'Pending'}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Returned Items</h4>
                    <div class="space-y-3">
                        ${ret.items.map(item => `
                                        <div class="border rounded-lg p-4 bg-gray-50">
                                            <div class="flex items-center justify-between mb-3">
                                                <div>
                                                    <p class="font-semibold text-gray-900">${item.toolName}</p>
                                                    <p class="text-sm text-gray-600">Quantity: ${item.quantity}</p>
                                                </div>
                                                <span class="px-3 py-1 rounded-full text-sm font-semibold ${App.getStatusBadgeColor(item.condition)}">${item.condition}</span>
                                            </div>
                                            <div class="grid grid-cols-4 gap-2 text-sm">
                                                <div class="bg-white p-2 rounded border text-center">
                                                    <p class="text-gray-600">Good</p>
                                                    <p class="font-semibold">${item.auditDetails.good}</p>
                                                </div>
                                                <div class="bg-white p-2 rounded border text-center">
                                                    <p class="text-gray-600">Damaged</p>
                                                    <p class="font-semibold">${item.auditDetails.damaged}</p>
                                                </div>
                                                <div class="bg-white p-2 rounded border text-center">
                                                    <p class="text-gray-600">Lost</p>
                                                    <p class="font-semibold">${item.auditDetails.lost}</p>
                                                </div>
                                                <div class="bg-white p-2 rounded border text-center">
                                                    <p class="text-gray-600">Sold</p>
                                                    <p class="font-semibold">${item.auditDetails.sold}</p>
                                                </div>
                                            </div>
                                        </div>`).join('')}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-gray-600 text-sm">Original Rental Revenue</p>
                        <p class="text-2xl font-bold text-green-700">${App.formatCurrency(ret.originalRevenue)}</p>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-gray-600 text-sm">Revenue Loss (Damage/Lost)</p>
                        <p class="text-2xl font-bold text-red-700">${App.formatCurrency(ret.totalAuditRevenueLoss)}</p>
                    </div>
                </div>
            </div>`;

            document.getElementById('returnModalContent').innerHTML = html;
            document.getElementById('returnModal').classList.add('active');
        }

        function closeReturnModal() {
            document.getElementById('returnModal').classList.remove('active');
        }

        function printReturnModal() {
            window.print();
        }

        // ============================================================
        // Dashboard Init
        // ============================================================
        document.addEventListener('DOMContentLoaded', () => {
            const pageTitle = document.getElementById('page-title');
            if (pageTitle) pageTitle.textContent = 'Dashboard';

            const pageContent = document.getElementById('page-content');
            if (!pageContent) return;

            const stats = dummyData.getDashboardStats();

            const cards = [{
                    title: 'Total Tools',
                    value: stats.totalTools,
                    icon: '🔧',
                    color: 'blue'
                },
                {
                    title: 'Tools Available',
                    value: stats.toolsAvailable,
                    icon: '✅',
                    color: 'green'
                },
                {
                    title: 'Tools Rented',
                    value: stats.toolsRented,
                    icon: '📦',
                    color: 'yellow'
                },
                {
                    title: 'Active Rentals',
                    value: stats.activeRentals,
                    icon: '📋',
                    color: 'purple'
                },
                {
                    title: 'Monthly Revenue',
                    value: 'Rp ' + stats.monthlyRevenue,
                    icon: '💰',
                    color: 'red'
                },
            ];

            let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">';
            cards.forEach(card => {
                html += `
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">${card.title}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">${card.value}</p>
                        </div>
                        <div class="text-2xl">${card.icon}</div>
                    </div>
                </div>`;
            });
            html += '</div>';

            // Charts
            html += `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Revenue</h3>
                    <div style="height:260px"><canvas id="revenueChart"></canvas></div>
                </div>
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Rental Activity</h3>
                    <div style="height:260px"><canvas id="rentalChart"></canvas></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tool Status</h3>
                    <div style="height:260px"><canvas id="toolStatusChart"></canvas></div>
                </div>
                <div class="lg:col-span-2 bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Rentals</h3>
                    <div id="recent-rentals-table"></div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Returns</h3>
                <div id="recent-returns-table"></div>
            </div>`;

            pageContent.innerHTML = html;

            // Init charts after DOM update
            setTimeout(() => {
                ChartManager.initChart('revenueChart', 'line',
                    ChartManager.createLineChartData(
                        dummyData.monthlyRevenue.map(d => d.month),
                        dummyData.monthlyRevenue.map(d => d.revenue),
                        'Revenue (Rp)'
                    ),
                    ChartManager.lineChartOptions()
                );

                ChartManager.initChart('rentalChart', 'bar',
                    ChartManager.createBarChartData(
                        dummyData.monthlyRentals.map(d => d.month),
                        dummyData.monthlyRentals.map(d => d.rentals),
                        'Number of Rentals'
                    ),
                    ChartManager.barChartOptions()
                );

                ChartManager.initChart('toolStatusChart', 'doughnut',
                    ChartManager.createPieChartData(
                        dummyData.toolStatusDistribution.map(d => d.label),
                        dummyData.toolStatusDistribution.map(d => d.value),
                        dummyData.toolStatusDistribution.map(d => d.color)
                    ),
                    ChartManager.pieChartOptions()
                );

                // Recent Rentals
                const recentRentals = dummyData.rentals.slice(0, 5);
                let rentalHtml = '<div class="space-y-3">';
                recentRentals.forEach(rental => {
                    rentalHtml += `
                    <button onclick="openRentalModal(${rental.id})"
                        class="w-full text-left flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">${rental.invoiceNumber}</p>
                            <p class="text-sm text-gray-600">${rental.items.map(i => i.toolName).join(', ')} - ${rental.customerName}</p>
                            <p class="text-xs text-gray-500 mt-1">${App.formatDate(rental.rentalStartDate)} s/d ${App.formatDate(rental.rentalEndDate)}</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="font-semibold text-gray-900">${App.formatCurrency(rental.totalPrice)}</p>
                            <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded ${App.getStatusBadgeColor(rental.rentalStatus)}">${rental.rentalStatus}</span>
                        </div>
                    </button>`;
                });
                rentalHtml += '</div>';
                document.getElementById('recent-rentals-table').innerHTML = rentalHtml;

                // Recent Returns
                const recentReturns = dummyData.returns.slice(0, 5);
                let returnHtml = '<div class="space-y-3">';
                recentReturns.forEach(ret => {
                    returnHtml += `
                    <button onclick="openReturnModal(${ret.id})"
                        class="w-full text-left flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">${ret.returnId}</p>
                            <p class="text-sm text-gray-600">${ret.items.map(i => i.toolName).join(', ')} - ${ret.customerName}</p>
                            <p class="text-xs text-gray-500 mt-1">Return: ${App.formatDate(ret.actualReturnDate || ret.requestedReturnDate)}</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="font-semibold text-gray-900">${App.formatCurrency(ret.originalRevenue)}</p>
                            <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded ${App.getStatusBadgeColor(ret.status)}">${ret.status}</span>
                        </div>
                    </button>`;
                });
                returnHtml += '</div>';
                document.getElementById('recent-returns-table').innerHTML = returnHtml;

            }, 100);
        });
    </script>
@endsection

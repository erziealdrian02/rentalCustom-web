// Tool Rental Management System - Dummy Data
const dummyData = {
  // Tools Master Data
  tools: [
    { id: 1, code: 'DRILL001', name: 'Power Drill', category: 'Power Tools', serialNumber: 'SN-2024-001', replacementValue: 150, status: 'Available' },
    { id: 2, code: 'SAW002', name: 'Circular Saw', category: 'Power Tools', serialNumber: 'SN-2024-002', replacementValue: 200, status: 'Rented' },
    { id: 3, code: 'SAND003', name: 'Orbital Sander', category: 'Power Tools', serialNumber: 'SN-2024-003', replacementValue: 120, status: 'Available' },
    { id: 4, code: 'IMPACT004', name: 'Impact Driver', category: 'Power Tools', serialNumber: 'SN-2024-004', replacementValue: 180, status: 'Damaged' },
    { id: 5, code: 'LADDER005', name: 'Extension Ladder', category: 'Safety Equipment', serialNumber: 'SN-2024-005', replacementValue: 250, status: 'Available' },
    { id: 6, code: 'WRENCH006', name: 'Socket Wrench Set', category: 'Hand Tools', serialNumber: 'SN-2024-006', replacementValue: 95, status: 'Rented' },
    { id: 7, code: 'LEVEL007', name: 'Digital Level', category: 'Measurement Tools', serialNumber: 'SN-2024-007', replacementValue: 85, status: 'Available' },
    { id: 8, code: 'GRINDER008', name: 'Angle Grinder', category: 'Power Tools', serialNumber: 'SN-2024-008', replacementValue: 220, status: 'Lost' },
  ],

  // Tool Categories
  categories: [
    { id: 1, name: 'Power Tools', description: 'Electric and cordless power tools' },
    { id: 2, name: 'Hand Tools', description: 'Manual hand tools and accessories' },
    { id: 3, name: 'Safety Equipment', description: 'Safety gear and protective equipment' },
    { id: 4, name: 'Measurement Tools', description: 'Precision measurement instruments' },
    { id: 5, name: 'Cleaning Equipment', description: 'Industrial cleaning tools' },
  ],

  // Warehouses
  warehouses: [
    { id: 1, name: 'Main Warehouse', location: 'Downtown', capacity: 1000, currentStock: 450 },
    { id: 2, name: 'North Branch', location: 'North District', capacity: 500, currentStock: 280 },
    { id: 3, name: 'South Facility', location: 'South Zone', capacity: 800, currentStock: 320 },
  ],

  // Customers
  customers: [
    { id: 1, name: 'John Construction Co.', email: 'john@construction.com', phone: '555-1001', status: 'Active' },
    { id: 2, name: 'Sarah Builders LLC', email: 'sarah@builders.com', phone: '555-1002', status: 'Active' },
    { id: 3, name: 'Mike Renovation', email: 'mike@renovation.com', phone: '555-1003', status: 'Active' },
    { id: 4, name: 'Emma Contractors', email: 'emma@contractors.com', phone: '555-1004', status: 'Inactive' },
    { id: 5, name: 'David Engineering', email: 'david@engineering.com', phone: '555-1005', status: 'Active' },
  ],

  // Rental Pricing
  pricing: [
    { id: 1, toolId: 1, toolName: 'Power Drill', dailyRate: 25, weeklyRate: 140, monthlyRate: 450 },
    { id: 2, toolId: 2, toolName: 'Circular Saw', dailyRate: 35, weeklyRate: 200, monthlyRate: 600 },
    { id: 3, toolId: 3, toolName: 'Orbital Sander', dailyRate: 20, weeklyRate: 110, monthlyRate: 350 },
    { id: 4, toolId: 5, toolName: 'Extension Ladder', dailyRate: 40, weeklyRate: 230, monthlyRate: 700 },
    { id: 5, toolId: 6, toolName: 'Socket Wrench Set', dailyRate: 15, weeklyRate: 85, monthlyRate: 250 },
  ],

  // Users
  users: [
    { id: 1, name: 'Admin User', email: 'admin@toolrental.com', role: 'Administrator', status: 'Active' },
    { id: 2, name: 'John Manager', email: 'john@toolrental.com', role: 'Warehouse Manager', status: 'Active' },
    { id: 3, name: 'Sarah Staff', email: 'sarah@toolrental.com', role: 'Staff', status: 'Active' },
    { id: 4, name: 'Mike Clerk', email: 'mike@toolrental.com', role: 'Clerk', status: 'Inactive' },
  ],

  // Rental Status Types
  rentalStatuses: {
    ON_TRACK: 'On Track',
    DELIVERED: 'Delivered',
    PENDING: 'Pending',
    OVERDUE: 'Menunggak',
    RETURNING: 'Returning',
    ON_CHECK: 'On Check'
  },

  // Rentals (now supports multiple tools per rental)
  rentals: [
    { 
      id: 1, 
      invoiceNumber: 'INV-2024-001', 
      customerId: 1, 
      customerName: 'John Construction Co.', 
      deliveryId: 1,
      driverId: 1,
      driverName: 'Ahmad Suryanto',
      deliveryLocation: 'Downtown Construction Site',
      estimatedDeliveryTime: '2024-01-15 09:30',
      items: [
        { toolId: 1, toolName: 'Power Drill', quantity: 2, startDate: '2024-01-15', endDate: '2024-01-22', durationType: 'daily', dailyRate: 25, subtotal: 350 },
        { toolId: 3, toolName: 'Orbital Sander', quantity: 1, startDate: '2024-01-15', endDate: '2024-01-22', durationType: 'daily', dailyRate: 20, subtotal: 140 }
      ],
      totalPrice: 490,
      rentalStatus: 'Delivered',
      createdDate: '2024-01-15',
      rentalStartDate: '2024-01-15',
      rentalEndDate: '2024-01-22'
    },
    { 
      id: 2, 
      invoiceNumber: 'INV-2024-002', 
      customerId: 2, 
      customerName: 'Sarah Builders LLC', 
      deliveryId: 2,
      driverId: 2,
      driverName: 'Budi Santoso',
      deliveryLocation: 'North District Office',
      estimatedDeliveryTime: '2024-01-18 11:15',
      items: [
        { toolId: 2, toolName: 'Circular Saw', quantity: 1, startDate: '2024-01-18', endDate: '2024-01-25', durationType: 'daily', dailyRate: 35, subtotal: 245 }
      ],
      totalPrice: 245,
      rentalStatus: 'On Track',
      createdDate: '2024-01-18',
      rentalStartDate: '2024-01-18',
      rentalEndDate: '2024-01-25'
    },
    { 
      id: 3, 
      invoiceNumber: 'INV-2024-003', 
      customerId: 3, 
      customerName: 'Mike Renovation', 
      deliveryId: null,
      driverId: null,
      driverName: null,
      deliveryLocation: null,
      estimatedDeliveryTime: null,
      items: [
        { toolId: 6, toolName: 'Socket Wrench Set', quantity: 1, startDate: '2024-01-10', endDate: '2024-01-17', durationType: 'daily', dailyRate: 15, subtotal: 105 }
      ],
      totalPrice: 105,
      rentalStatus: 'On Check',
      createdDate: '2024-01-10',
      rentalStartDate: '2024-01-10',
      rentalEndDate: '2024-01-17'
    },
    { 
      id: 4, 
      invoiceNumber: 'INV-2024-004', 
      customerId: 1, 
      customerName: 'John Construction Co.', 
      deliveryId: 3,
      driverId: 3,
      driverName: 'Citra Wijaya',
      deliveryLocation: 'South Zone Project',
      estimatedDeliveryTime: null,
      items: [
        { toolId: 5, toolName: 'Extension Ladder', quantity: 2, startDate: '2024-01-20', endDate: '2024-02-10', durationType: 'daily', dailyRate: 40, subtotal: 800 }
      ],
      totalPrice: 800,
      rentalStatus: 'Pending',
      createdDate: '2024-01-20',
      rentalStartDate: '2024-01-20',
      rentalEndDate: '2024-02-10'
    },
    { 
      id: 5, 
      invoiceNumber: 'INV-2024-005', 
      customerId: 5, 
      customerName: 'David Engineering', 
      deliveryId: null,
      driverId: null,
      driverName: null,
      deliveryLocation: null,
      estimatedDeliveryTime: null,
      items: [
        { toolId: 3, toolName: 'Orbital Sander', quantity: 1, startDate: '2024-01-22', endDate: '2024-01-29', durationType: 'daily', dailyRate: 20, subtotal: 140 },
        { toolId: 1, toolName: 'Power Drill', quantity: 1, startDate: '2024-01-22', endDate: '2024-01-29', durationType: 'daily', dailyRate: 25, subtotal: 175 }
      ],
      totalPrice: 315,
      rentalStatus: 'On Track',
      createdDate: '2024-01-22',
      rentalStartDate: '2024-01-22',
      rentalEndDate: '2024-01-29'
    },
  ],

  // Returns (Enhanced with audit details)
  returns: [
    { 
      id: 1, 
      returnId: 'RET-2024-001', 
      rentalId: 3,
      invoiceNumber: 'INV-2024-003', 
      customerId: 3,
      customerName: 'Mike Renovation', 
      originalRentalDate: '2024-01-10',
      requestedReturnDate: '2024-01-17',
      actualReturnDate: '2024-01-17',
      status: 'Completed',
      originalRevenue: 105,
      items: [
        {
          toolId: 6,
          toolName: 'Socket Wrench Set',
          quantity: 1,
          originalRate: 15,
          condition: 'Good',
          auditDetails: {
            good: 1,
            damaged: 0,
            lost: 0,
            sold: 0
          }
        }
      ],
      totalAuditRevenueLoss: 0,
      auditDate: '2024-01-17'
    },
    { 
      id: 2, 
      returnId: 'RET-2024-002', 
      rentalId: 1,
      invoiceNumber: 'INV-2024-001', 
      customerId: 1,
      customerName: 'John Construction Co.', 
      originalRentalDate: '2024-01-15',
      requestedReturnDate: '2024-01-22',
      actualReturnDate: '2024-01-23',
      status: 'Pending',
      originalRevenue: 490,
      items: [
        {
          toolId: 1,
          toolName: 'Power Drill',
          quantity: 2,
          originalRate: 25,
          condition: 'Damaged',
          auditDetails: {
            good: 1,
            damaged: 1,
            lost: 0,
            sold: 0
          }
        },
        {
          toolId: 3,
          toolName: 'Orbital Sander',
          quantity: 1,
          originalRate: 20,
          condition: 'Good',
          auditDetails: {
            good: 1,
            damaged: 0,
            lost: 0,
            sold: 0
          }
        }
      ],
      totalAuditRevenueLoss: 150,
      auditDate: null
    },
  ],

  // Stock Movement History
  stockMovement: [
    { id: 1, date: '2024-01-22', toolName: 'Power Drill', warehouseName: 'Main Warehouse', type: 'RENT', quantity: 1, reference: 'INV-2024-001' },
    { id: 2, date: '2024-01-22', toolName: 'Circular Saw', warehouseName: 'Main Warehouse', type: 'RENT', quantity: 1, reference: 'INV-2024-002' },
    { id: 3, date: '2024-01-20', toolName: 'Extension Ladder', warehouseName: 'North Branch', type: 'OUT', quantity: 2, reference: 'Warehouse Transfer' },
    { id: 4, date: '2024-01-19', toolName: 'Socket Wrench Set', warehouseName: 'Main Warehouse', type: 'RETURN', quantity: 1, reference: 'RET-2024-001' },
    { id: 5, date: '2024-01-18', toolName: 'Orbital Sander', warehouseName: 'Main Warehouse', type: 'RENT', quantity: 1, reference: 'INV-2024-005' },
    { id: 6, date: '2024-01-15', toolName: 'Impact Driver', warehouseName: 'South Facility', type: 'DAMAGED', quantity: 1, reference: 'Damage Report' },
    { id: 7, date: '2024-01-10', toolName: 'Power Drill', warehouseName: 'North Branch', type: 'IN', quantity: 3, reference: 'Stock Replenishment' },
  ],

  // Drivers
  drivers: [
    { id: 1, name: 'Ahmad Suryanto', email: 'ahmad@toolrental.com', phone: '555-2001', status: 'Active', vehicleType: 'Truck', licensePlate: 'B1234ABC' },
    { id: 2, name: 'Budi Santoso', email: 'budi@toolrental.com', phone: '555-2002', status: 'Active', vehicleType: 'Van', licensePlate: 'B5678DEF' },
    { id: 3, name: 'Citra Wijaya', email: 'citra@toolrental.com', phone: '555-2003', status: 'Active', vehicleType: 'Truck', licensePlate: 'B9012GHI' },
  ],

  // Shipping Deliveries (Enhanced with multi-rental support)
  shippings: [
    { 
      id: 1, 
      deliveryNumber: 'DEL-2024-001', 
      driverId: 1,
      driverName: 'Ahmad Suryanto',
      rentals: [
        { rentalId: 1, invoiceNumber: 'INV-2024-001', customerId: 1, customerName: 'John Construction Co.', fromLocation: 'Main Warehouse', toLocation: 'Downtown Construction Site', items: 2 }
      ],
      departureTime: '2024-01-15 08:00',
      arrivalTime: '2024-01-15 09:30',
      status: 'Delivered',
      proofImage: null,
      notes: 'Delivered successfully'
    },
    { 
      id: 2, 
      deliveryNumber: 'DEL-2024-002', 
      driverId: 2,
      driverName: 'Budi Santoso',
      rentals: [
        { rentalId: 2, invoiceNumber: 'INV-2024-002', customerId: 2, customerName: 'Sarah Builders LLC', fromLocation: 'Main Warehouse', toLocation: 'North District Office', items: 1 }
      ],
      departureTime: '2024-01-18 09:00',
      arrivalTime: '2024-01-18 11:15',
      status: 'In Transit',
      proofImage: null,
      notes: 'On the way'
    },
    { 
      id: 3, 
      deliveryNumber: 'DEL-2024-003', 
      driverId: 3,
      driverName: 'Citra Wijaya',
      rentals: [
        { rentalId: 4, invoiceNumber: 'INV-2024-004', customerId: 1, customerName: 'John Construction Co.', fromLocation: 'North Branch', toLocation: 'South Zone Project', items: 1 }
      ],
      departureTime: '2024-01-20 07:30',
      arrivalTime: null,
      status: 'Pending',
      proofImage: null,
      notes: 'Waiting for dispatch'
    },
  ],

  // Lost Tools
  lostTools: [
    { id: 1, toolCode: 'GRINDER008', toolName: 'Angle Grinder', serialNumber: 'SN-2024-008', replacementValue: 220, lostDate: '2024-01-12', invoiceNumber: 'INV-2024-008', customerName: 'Mike Renovation' },
  ],

  // Sold Tools
  soldTools: [
    { id: 1, toolCode: 'IMPACT010', toolName: 'Impact Driver (Old)', serialNumber: 'SN-2023-010', replacementValue: 180, soldDate: '2024-01-05', soldPrice: 90 },
  ],

  // Stock Overview (current warehouse stock)
  stockOverview: [
    { id: 1, toolName: 'Power Drill', warehouseName: 'Main Warehouse', quantity: 8, status: 'Good' },
    { id: 2, toolName: 'Circular Saw', warehouseName: 'Main Warehouse', quantity: 3, status: 'Good' },
    { id: 3, toolName: 'Extension Ladder', warehouseName: 'North Branch', quantity: 5, status: 'Good' },
    { id: 4, toolName: 'Orbital Sander', warehouseName: 'South Facility', quantity: 6, status: 'Good' },
    { id: 5, toolName: 'Socket Wrench Set', warehouseName: 'Main Warehouse', quantity: 12, status: 'Good' },
    { id: 6, toolName: 'Impact Driver', warehouseName: 'South Facility', quantity: 2, status: 'Damaged' },
  ],

  // Dashboard Statistics
  getDashboardStats() {
    return {
      totalTools: this.tools.length,
      toolsAvailable: this.tools.filter(t => t.status === 'Available').length,
      toolsRented: this.tools.filter(t => t.status === 'Rented').length,
      activeRentals: this.rentals.filter(r => r.status === 'Active').length,
      monthlyRevenue: 2850,
    };
  },

  // Monthly Revenue Data
  monthlyRevenue: [
    { month: 'Jan', revenue: 2850 },
    { month: 'Feb', revenue: 3200 },
    { month: 'Mar', revenue: 2950 },
    { month: 'Apr', revenue: 3450 },
    { month: 'May', revenue: 3100 },
    { month: 'Jun', revenue: 3800 },
  ],

  // Monthly Rental Activity
  monthlyRentals: [
    { month: 'Jan', rentals: 12 },
    { month: 'Feb', rentals: 15 },
    { month: 'Mar', rentals: 14 },
    { month: 'Apr', rentals: 18 },
    { month: 'May', rentals: 16 },
    { month: 'Jun', rentals: 20 },
  ],

  // Tool Status Distribution
  toolStatusDistribution: [
    { label: 'Available', value: 5, color: '#10b981' },
    { label: 'Rented', value: 2, color: '#f59e0b' },
    { label: 'Damaged', value: 1, color: '#ef4444' },
    { label: 'Lost', value: 1, color: '#6366f1' },
  ],
};

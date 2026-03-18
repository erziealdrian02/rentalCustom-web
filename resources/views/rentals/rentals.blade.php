{{-- resources/views/rentals/index.blade.php --}}
@extends('layout.app')

@section('content')
        <script>
            let selectedRental = null;

            document.addEventListener("DOMContentLoaded", () => {
                const pageTitle = document.getElementById("page-title");
                if (pageTitle) pageTitle.textContent = "Rental Transactions";

                const pageContent = document.getElementById("page-content");
                if (!pageContent) return;

                let html = `
        <div>
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Rental List</h2>
            <a href="create-rental.html" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 whitespace-nowrap">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Create Rental
            </a>
          </div>

          <!-- Summary Cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
              <p class="text-gray-600 text-sm font-medium">Total Rentals</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">${dummyData.rentals.length}</p>
            </div>

            <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
              <p class="text-gray-600 text-sm font-medium">Active Rentals</p>
              <p class="text-3xl font-bold text-green-600 mt-2">${dummyData.rentals.filter((r) => r.status === "Active").length}</p>
            </div>

            <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
              <p class="text-gray-600 text-sm font-medium">Completed</p>
              <p class="text-3xl font-bold text-blue-600 mt-2">${dummyData.rentals.filter((r) => r.status === "Completed").length}</p>
            </div>

            <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
              <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
              <p class="text-3xl font-bold text-purple-600 mt-2">${App.formatCurrency(dummyData.rentals.reduce((sum, r) => sum + r.totalPrice, 0))}</p>
            </div>
          </div>

          <!-- Rentals Table -->
          <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Invoice</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tools</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Rental Period</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total Price</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  ${dummyData.rentals
                      .map((rental) => {
                          const statusColor =
                              rental.status === "Active"
                                  ? "bg-green-100 text-green-800"
                                  : "bg-blue-100 text-blue-800";
                          const toolsCount = rental.items
                              ? rental.items.length
                              : 1;
                          const startDate =
                              rental.rentalStartDate || rental.startDate;
                          const endDate =
                              rental.rentalEndDate || rental.endDate;
                          return `
                      <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="openRentalModal(${rental.id})">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">${rental.invoiceNumber}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${rental.customerName}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                          <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">${toolsCount} tool${toolsCount > 1 ? "s" : ""}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                          ${new Date(startDate).toLocaleDateString()} - ${new Date(endDate).toLocaleDateString()}
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">${App.formatCurrency(rental.totalPrice)}</td>
                        <td class="px-4 py-3 text-sm">
                          <span class="inline-block px-3 py-1 rounded-full text-xs font-medium ${statusColor}">${rental.status}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                          <button onclick="event.stopPropagation(); openRentalModal(${rental.id})" class="text-blue-600 hover:text-blue-700 font-medium">View</button>
                        </td>
                      </tr>
                    `;
                      })
                      .join("")}
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Modal -->
        <div id="rentalModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
          <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div id="modalContent"></div>
          </div>
        </div>
      `;

                pageContent.innerHTML = html;
            });

            function openRentalModal(rentalId) {
                const rental = dummyData.rentals.find((r) => r.id === rentalId);
                if (!rental) return;

                selectedRental = rental;
                const modal = document.getElementById("rentalModal");
                const modalContent = document.getElementById("modalContent");

                // Format items
                const items = rental.items || [
                    {
                        toolId: rental.toolId,
                        toolName: rental.toolName,
                        quantity: 1,
                        startDate: rental.startDate,
                        endDate: rental.endDate,
                        dailyRate: rental.totalPrice / 7,
                        subtotal: rental.totalPrice,
                    },
                ];

                const startDate = rental.rentalStartDate || rental.startDate;
                const endDate = rental.rentalEndDate || rental.endDate;

                let itemsHtml = items
                    .map(
                        (item) => `
        <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded mb-3">
          <div class="flex justify-between items-start mb-2">
            <div>
              <h4 class="font-semibold text-gray-900">${item.toolName}</h4>
              <p class="text-sm text-gray-600">Quantity: ${item.quantity}</p>
            </div>
            <span class="text-lg font-bold text-blue-600">${App.formatCurrency(item.subtotal)}</span>
          </div>
          <div class="grid grid-cols-2 gap-2 text-sm">
            <div>
              <p class="text-gray-600">Daily Rate:</p>
              <p class="font-medium">${App.formatCurrency(item.dailyRate)}</p>
            </div>
            <div>
              <p class="text-gray-600">Duration:</p>
              <p class="font-medium">${new Date(item.endDate) - new Date(item.startDate) > 0 ? Math.ceil((new Date(item.endDate) - new Date(item.startDate)) / (1000 * 60 * 60 * 24)) : 1} days</p>
            </div>
          </div>
        </div>
      `,
                    )
                    .join("");

                const statusColor =
                    rental.status === "Active"
                        ? "bg-green-100 text-green-800"
                        : "bg-blue-100 text-blue-800";

                modalContent.innerHTML = `
        <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-start">
          <div>
            <h3 class="text-2xl font-bold text-gray-900">${rental.invoiceNumber}</h3>
            <p class="text-gray-600 text-sm mt-1">Created: ${new Date(rental.createdDate || new Date()).toLocaleDateString()}</p>
          </div>
          <button onclick="closeRentalModal()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="p-6 space-y-6">
          <!-- Customer Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
              <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Customer Information</h4>
              <p class="text-lg font-semibold text-gray-900">${rental.customerName}</p>
              ${(() => {
                  const customer = dummyData.customers.find(
                      (c) => c.id === rental.customerId,
                  );
                  return customer
                      ? `
                  <p class="text-sm text-gray-600 mt-2">${customer.email}</p>
                  <p class="text-sm text-gray-600">${customer.phone}</p>
                `
                      : "";
              })()}
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
              <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Rental Status</h4>
              <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${statusColor}">${rental.status}</span>
              <p class="text-sm text-gray-600 mt-3">Rental Period:</p>
              <p class="text-sm font-medium text-gray-900">${new Date(startDate).toLocaleDateString()} to ${new Date(endDate).toLocaleDateString()}</p>
            </div>
          </div>

          <!-- Items Details -->
          <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Rental Items (${items.length})</h4>
            ${itemsHtml}
          </div>

          <!-- Summary -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-200">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-gray-600 text-sm">Total Amount</p>
                <p class="text-4xl font-bold text-blue-600 mt-2">${App.formatCurrency(rental.totalPrice)}</p>
              </div>
              <div class="text-right">
                <p class="text-gray-600 text-sm">Total Items</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">${items.length}</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <button onclick="printRentalDetails()" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
              </svg>
              Print Details
            </button>
            <button onclick="closeRentalModal()" class="flex-1 bg-gray-300 text-gray-900 py-3 rounded-lg hover:bg-gray-400 transition font-medium">
              Close
            </button>
          </div>
        </div>
      `;

                modal.classList.remove("hidden");
                document.body.style.overflow = "hidden";
            }

            function closeRentalModal() {
                const modal = document.getElementById("rentalModal");
                modal.classList.add("hidden");
                document.body.style.overflow = "auto";
                selectedRental = null;
            }

            function printRentalDetails() {
                if (!selectedRental) return;

                const rental = selectedRental;
                const items = rental.items || [
                    {
                        toolName: rental.toolName,
                        quantity: 1,
                        dailyRate: rental.totalPrice / 7,
                        subtotal: rental.totalPrice,
                    },
                ];

                const startDate = rental.rentalStartDate || rental.startDate;
                const endDate = rental.rentalEndDate || rental.endDate;
                const customer = dummyData.customers.find(
                    (c) => c.id === rental.customerId,
                );

                let printContent = `
        <!DOCTYPE html>
        <html>
        <head>
          <title>${rental.invoiceNumber}</title>
          <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
            .header { border-bottom: 3px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
            .header h1 { margin: 0 0 5px 0; color: #1e40af; }
            .header p { margin: 5px 0; color: #666; }
            .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
            .info-box { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
            .info-box h3 { margin: 0 0 10px 0; font-size: 12px; color: #666; text-transform: uppercase; }
            .info-box p { margin: 5px 0; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
            th { background-color: #f3f4f6; text-align: left; padding: 10px; font-weight: bold; border-bottom: 2px solid #ddd; }
            td { padding: 10px; border-bottom: 1px solid #eee; }
            .total-section { background-color: #eff6ff; padding: 20px; border-radius: 5px; border-left: 4px solid #2563eb; }
            .total-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
            .total-amount { font-size: 24px; font-weight: bold; color: #2563eb; }
            .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 12px; color: #666; }
            @media print { body { margin: 0; padding: 0; } }
          </style>
        </head>
        <body>
          <div class="header">
            <h1>ToolRental Pro</h1>
            <p>Tool Rental Management System</p>
            <p>Invoice: ${rental.invoiceNumber}</p>
          </div>

          <div class="info-grid">
            <div class="info-box">
              <h3>Customer Information</h3>
              <p><strong>${rental.customerName}</strong></p>
              ${customer ? `<p>${customer.email}</p><p>${customer.phone}</p>` : ""}
            </div>
            <div class="info-box">
              <h3>Rental Period</h3>
              <p><strong>From:</strong> ${new Date(startDate).toLocaleDateString()}</p>
              <p><strong>To:</strong> ${new Date(endDate).toLocaleDateString()}</p>
              <p><strong>Status:</strong> ${rental.status}</p>
            </div>
          </div>

          <h3>Rental Items</h3>
          <table>
            <thead>
              <tr>
                <th>Tool Name</th>
                <th>Quantity</th>
                <th>Daily Rate</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              ${items
                  .map(
                      (item) => `
                <tr>
                  <td>${item.toolName}</td>
                  <td>${item.quantity}</td>
                  <td>${App.formatCurrency(item.dailyRate)}</td>
                  <td>${App.formatCurrency(item.subtotal)}</td>
                </tr>
              `,
                  )
                  .join("")}
            </tbody>
          </table>

          <div class="total-section">
            <div class="total-row">
              <span>Total Items:</span>
              <strong>${items.length}</strong>
            </div>
            <div class="total-row">
              <span>Total Amount Due:</span>
              <strong class="total-amount">${App.formatCurrency(rental.totalPrice)}</strong>
            </div>
          </div>

          <div class="footer">
            <p>This is a computer-generated document. Printed on ${new Date().toLocaleString()}</p>
            <p>&copy; 2024 ToolRental Pro. All rights reserved.</p>
          </div>
        </body>
        </html>
      `;

                const printWindow = window.open("", "", "width=800,height=600");
                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.print();
            }

            // Close modal when clicking outside
            document.addEventListener("click", (e) => {
                const modal = document.getElementById("rentalModal");
                if (modal && e.target === modal) {
                    closeRentalModal();
                }
            });
        </script>
@endsection

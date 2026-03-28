<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\RentalsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\SpecialController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WarehouseController;
use App\Models\Dashboard;

// Route::get('/', function () {
//     return view('login');
// });

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    // Reset Password (dari link email)
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/change-password', [AuthController::class, 'changePassword'])->name('password.change');

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/master/tools', [ToolsController::class, 'masterTools'])->name('master.tools');
    Route::post('/master/tools/post', [ToolsController::class, 'masterToolsStore'])->name('tools.store');
    Route::put('/master/tools/update/{id_tools}', [ToolsController::class, 'masterToolsUpdate'])->name('tools.update');
    Route::delete('/master/tools/delete/{id_tools}', [ToolsController::class, 'masterToolsDestroy'])->name('tools.destroy');

    Route::get('/master/categories', [CategoriesController::class, 'masterCategories'])->name('master.categories');
    Route::post('/master/categories/post', [CategoriesController::class, 'masterCategoriesStore'])->name('categories.store');
    Route::put('/master/categories/update/{id}', [CategoriesController::class, 'masterCategoriesUpdate'])->name('categories.update');
    Route::delete('/master/categories/delete/{id}', [CategoriesController::class, 'masterCategoriesDestroy'])->name('categories.destroy');

    Route::get('/master/warehouses', [WarehouseController::class, 'masterWarehouses'])->name('master.warehouses');
    Route::post('/master/warehouses/post', [WarehouseController::class, 'masterWarehousesStore'])->name('warehouses.store');
    Route::put('/master/warehouses/update/{id}', [WarehouseController::class, 'masterWarehousesUpdate'])->name('warehouses.update');
    Route::delete('/master/warehouses/delete/{id}', [WarehouseController::class, 'masterWarehousesDestroy'])->name('warehouses.destroy');

    Route::get('/master/customers', [CustomersController::class, 'masterCustomers'])->name('master.customers');
    Route::post('/master/customers/post', [CustomersController::class, 'masterCustomersStore'])->name('customers.store');
    Route::put('/master/customers/update/{id}', [CustomersController::class, 'masterCustomersUpdate'])->name('customers.update');
    Route::delete('/master/customers/delete/{id}', [CustomersController::class, 'masterCustomersDestroy'])->name('customers.destroy');

    Route::get('/master/pricing', [PricingController::class, 'masterPricing'])->name('master.pricing');
    Route::post('/master/pricing/post', [PricingController::class, 'masterPricingStore'])->name('pricing.store');
    Route::put('/master/pricing/update/{id}', [PricingController::class, 'masterPricingUpdate'])->name('pricing.update');

    Route::get('/master/users', [UsersController::class, 'masterUsers'])->name('master.users');
    Route::post('/master/users/post', [UsersController::class, 'masterUsersStore'])->name('users.store');
    Route::put('/master/users/update/{id}', [UsersController::class, 'masterUsersUpdate'])->name('users.update');
    Route::put('/master/users/reset/{id}', [UsersController::class, 'masterUsersReset'])->name('users.reset');
    Route::delete('/master/users/delete/{id}', [UsersController::class, 'masterUsersDestroy'])->name('users.destroy');

    Route::get('/stock/overview', [StockController::class, 'overview'])->name('stock.overview');
    Route::get('/stock/restock/form', [StockController::class, 'restockForm'])->name('stock.restock.form');
    Route::post('/stock/restock/store', [StockController::class, 'restockStore'])->name('stock.restock.store');
    Route::get('/stock/movement', [StockController::class, 'movement'])->name('stock.movement');

    Route::get('/transactions/rentals', [RentalsController::class, 'rental'])->name('transactions.rentals');
    Route::get('/transactions/rentals/form', [RentalsController::class, 'rentalForm'])->name('transactions.rentals.form');
    Route::post('/transactions/rentals/post', [RentalsController::class, 'rentalStore'])->name('transactions.rentals.store');
    Route::get('/transactions/rentals/detail/{id}', [RentalsController::class, 'show'])->name('transactions.rentals.show');

    Route::get('/shipping/list', [ShippingController::class, 'list'])->name('shipping.list');
    Route::get('/shipping/create', [ShippingController::class, 'form'])->name('shipping.form');
    Route::post('/shipping/create/post', [ShippingController::class, 'store'])->name('shipping.store');

    Route::get('/monitoring/active', [MonitoringController::class, 'monitoringActive'])->name('monitoring.active');

    Route::get('/return', [ReturnsController::class, 'returnsTools'])->name('returns.tools');
    Route::get('/return/form', [ReturnsController::class, 'returnsFrom'])->name('returns.form');
    Route::post('/return/form/post', [ReturnsController::class, 'returnStore'])->name('returns.store');

    Route::get('/lost/tools', [SpecialController::class, 'lostTools'])->name('lost.tools');
    Route::get('/sold/tools', [SpecialController::class, 'soldTools'])->name('sold.tools');

    Route::get('/reports/rentals', [ReportsController::class, 'rentalReports'])->name('reports.rental');
    Route::get('/reports/rentals/export', [ReportsController::class, 'rentalReportsexport'])->name('reports.rental.export');
    Route::get('/reports/revenue', [ReportsController::class, 'revenueReports'])->name('reports.revenue');
    Route::get('/reports/revenue/export', [ReportsController::class, 'revenueReportsexport'])->name('reports.revenue.export');
    Route::get('/reports/inventory', [ReportsController::class, 'inventoryReports'])->name('reports.inventory');
    Route::get('/reports/inventory/export', [ReportsController::class, 'inventoryReportsexport'])->name('reports.inventory.export');
});

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Auth::routes();

// Admin Dashboard Routes (Protected by authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Customer Management Routes
    Route::get('/admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/admin/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/admin/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/admin/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/admin/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/admin/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/admin/customers/{customer}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    
    // Staff Management Routes
    Route::get('/admin/staff', [StaffController::class, 'index'])->name('admin.staff.index');
    Route::get('/admin/staff/create', [StaffController::class, 'create'])->name('admin.staff.create');
    Route::post('/admin/staff', [StaffController::class, 'store'])->name('admin.staff.store');
    Route::get('/admin/staff/{staff}', [StaffController::class, 'show'])->name('admin.staff.show');
    Route::get('/admin/staff/{staff}/edit', [StaffController::class, 'edit'])->name('admin.staff.edit');
    Route::put('/admin/staff/{staff}', [StaffController::class, 'update'])->name('admin.staff.update');
    Route::delete('/admin/staff/{staff}', [StaffController::class, 'destroy'])->name('admin.staff.destroy');
    
    // Settings Routes
    Route::get('/admin/settings', [SettingsController::class, 'profile'])->name('admin.settings.profile');
    Route::post('/admin/settings', [SettingsController::class, 'updateProfile'])->name('admin.settings.profile.update');
    Route::get('/admin/settings/password', [SettingsController::class, 'password'])->name('admin.settings.password');
    Route::post('/admin/settings/password', [SettingsController::class, 'updatePassword'])->name('admin.settings.password.update');
    
    // Invoice Routes
    Route::get('/admin/invoices', [InvoiceController::class, 'index'])->name('admin.invoices.index');
    Route::get('/admin/invoices/create', [InvoiceController::class, 'create'])->name('admin.invoices.create');
    Route::post('/admin/invoices', [InvoiceController::class, 'store'])->name('admin.invoices.store');
    Route::get('/admin/invoices/{invoice}', [InvoiceController::class, 'show'])->name('admin.invoices.show');
    Route::get('/admin/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('admin.invoices.edit');
    Route::put('/admin/invoices/{invoice}', [InvoiceController::class, 'update'])->name('admin.invoices.update');
    Route::delete('/admin/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('admin.invoices.destroy');
    Route::post('/admin/find-customer', [InvoiceController::class, 'findCustomerByMobile'])->name('admin.find.customer');
    
    // Payment Routes
    Route::get('/admin/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/admin/payments/create', [PaymentController::class, 'create'])->name('admin.payments.create');
    Route::post('/admin/payments', [PaymentController::class, 'store'])->name('admin.payments.store');
    Route::get('/admin/payments/{payment}', [PaymentController::class, 'show'])->name('admin.payments.show');
    Route::get('/admin/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('admin.payments.edit');
    Route::put('/admin/payments/{payment}', [PaymentController::class, 'update'])->name('admin.payments.update');
    Route::delete('/admin/payments/{payment}', [PaymentController::class, 'destroy'])->name('admin.payments.destroy');
});

// Redirect /home to admin dashboard for authenticated users
Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');

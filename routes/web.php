<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;

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
});

// Redirect /home to admin dashboard for authenticated users
Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');

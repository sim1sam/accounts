<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\Admin\CancellationController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AccountTransactionController;
use App\Http\Controllers\Admin\LedgerReportController;

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
    
    // Delivery Routes
    Route::get('/admin/deliveries', [DeliveryController::class, 'index'])->name('admin.deliveries.index');
    Route::get('/admin/deliveries/create', [DeliveryController::class, 'create'])->name('admin.deliveries.create');
    Route::post('/admin/deliveries', [DeliveryController::class, 'store'])->name('admin.deliveries.store');
    Route::get('/admin/deliveries/{delivery}', [DeliveryController::class, 'show'])->name('admin.deliveries.show');
    Route::get('/admin/deliveries/{delivery}/edit', [DeliveryController::class, 'edit'])->name('admin.deliveries.edit');
    Route::put('/admin/deliveries/{delivery}', [DeliveryController::class, 'update'])->name('admin.deliveries.update');
    Route::delete('/admin/deliveries/{delivery}', [DeliveryController::class, 'destroy'])->name('admin.deliveries.destroy');
    
    // Cancellation Routes
    Route::get('/admin/cancellations', [CancellationController::class, 'index'])->name('admin.cancellations.index');
    Route::get('/admin/cancellations/create', [CancellationController::class, 'create'])->name('admin.cancellations.create');
    Route::post('/admin/cancellations', [CancellationController::class, 'store'])->name('admin.cancellations.store');
    Route::get('/admin/cancellations/{cancellation}', [CancellationController::class, 'show'])->name('admin.cancellations.show');
    Route::get('/admin/cancellations/{cancellation}/edit', [CancellationController::class, 'edit'])->name('admin.cancellations.edit');
    Route::put('/admin/cancellations/{cancellation}', [CancellationController::class, 'update'])->name('admin.cancellations.update');
    Route::delete('/admin/cancellations/{cancellation}', [CancellationController::class, 'destroy'])->name('admin.cancellations.destroy');
    
    // Refund Routes
    Route::get('/admin/refunds', [RefundController::class, 'index'])->name('admin.refunds.index');
    Route::get('/admin/refunds/create', [RefundController::class, 'create'])->name('admin.refunds.create');
    Route::post('/admin/refunds', [RefundController::class, 'store'])->name('admin.refunds.store');
    Route::get('/admin/refunds/{refund}', [RefundController::class, 'show'])->name('admin.refunds.show');
    Route::get('/admin/refunds/{refund}/edit', [RefundController::class, 'edit'])->name('admin.refunds.edit');
    Route::put('/admin/refunds/{refund}', [RefundController::class, 'update'])->name('admin.refunds.update');
    Route::delete('/admin/refunds/{refund}', [RefundController::class, 'destroy'])->name('admin.refunds.destroy');
    
    // Bank Routes
    Route::get('/admin/banks', [BankController::class, 'index'])->name('admin.banks.index');
    Route::get('/admin/banks/create', [BankController::class, 'create'])->name('admin.banks.create');
    Route::post('/admin/banks', [BankController::class, 'store'])->name('admin.banks.store');
    Route::get('/admin/banks/{bank}', [BankController::class, 'show'])->name('admin.banks.show');
    Route::get('/admin/banks/{bank}/edit', [BankController::class, 'edit'])->name('admin.banks.edit');
    Route::put('/admin/banks/{bank}', [BankController::class, 'update'])->name('admin.banks.update');
    Route::delete('/admin/banks/{bank}', [BankController::class, 'destroy'])->name('admin.banks.destroy');
    Route::post('/admin/banks/{bank}/adjust-balance', [BankController::class, 'adjustBalance'])->name('admin.banks.adjust-balance');
    
    // Currency Routes
    Route::get('/admin/currencies', [CurrencyController::class, 'index'])->name('admin.currencies.index');
    Route::get('/admin/currencies/create', [CurrencyController::class, 'create'])->name('admin.currencies.create');
    Route::post('/admin/currencies', [CurrencyController::class, 'store'])->name('admin.currencies.store');
    Route::get('/admin/currencies/{currency}', [CurrencyController::class, 'show'])->name('admin.currencies.show');
    Route::get('/admin/currencies/{currency}/edit', [CurrencyController::class, 'edit'])->name('admin.currencies.edit');
    Route::put('/admin/currencies/{currency}', [CurrencyController::class, 'update'])->name('admin.currencies.update');
    Route::delete('/admin/currencies/{currency}', [CurrencyController::class, 'destroy'])->name('admin.currencies.destroy');
    
    // Account Routes
    Route::get('/admin/accounts', [AccountController::class, 'index'])->name('admin.accounts.index');
    Route::get('/admin/accounts/create', [AccountController::class, 'create'])->name('admin.accounts.create');
    Route::post('/admin/accounts', [AccountController::class, 'store'])->name('admin.accounts.store');
    Route::get('/admin/accounts/{account}', [AccountController::class, 'show'])->name('admin.accounts.show');
    Route::get('/admin/accounts/{account}/edit', [AccountController::class, 'edit'])->name('admin.accounts.edit');
    Route::put('/admin/accounts/{account}', [AccountController::class, 'update'])->name('admin.accounts.update');
    Route::delete('/admin/accounts/{account}', [AccountController::class, 'destroy'])->name('admin.accounts.destroy');
    Route::post('/admin/accounts/{account}/adjust-balance', [AccountController::class, 'adjustBalance'])->name('admin.accounts.adjust-balance');
    
    // Transaction Routes
    Route::get('/admin/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
    Route::get('/admin/transactions/{transaction}', [TransactionController::class, 'show'])->name('admin.transactions.show');
    
    // Expense Routes
    Route::get('/admin/expenses', [ExpenseController::class, 'index'])->name('admin.expenses.index');
    Route::get('/admin/expenses/create', [ExpenseController::class, 'create'])->name('admin.expenses.create');
    Route::post('/admin/expenses', [ExpenseController::class, 'store'])->name('admin.expenses.store');
    Route::get('/admin/expenses/{expense}', [ExpenseController::class, 'show'])->name('admin.expenses.show');
    Route::get('/admin/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('admin.expenses.edit');
    Route::put('/admin/expenses/{expense}', [ExpenseController::class, 'update'])->name('admin.expenses.update');
    Route::delete('/admin/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('admin.expenses.destroy');
    Route::get('/admin/expenses/{expense}/payment', [ExpenseController::class, 'showPaymentForm'])->name('admin.expenses.payment');
    Route::post('/admin/expenses/{expense}/payment', [ExpenseController::class, 'processPayment'])->name('admin.expenses.payment.process');
    
    // Account Transaction Routes
    Route::get('/admin/account-transactions', [AccountTransactionController::class, 'index'])->name('admin.account-transactions.index');
    Route::get('/admin/accounts/{account}/transactions', [AccountTransactionController::class, 'show'])->name('admin.account-transactions.show');
    
    // Ledger Report Routes
    Route::get('/admin/reports/ledger', [LedgerReportController::class, 'index'])->name('admin.reports.ledger');
});

// Redirect /home to admin dashboard for authenticated users
Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');

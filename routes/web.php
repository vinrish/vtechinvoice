<?php

declare(strict_types=1);

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\UserController;
use App\Models\Client;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::get('users', [UserController::class, 'index'])->name('users.index');
Route::post('users', [UserController::class, 'store'])->name('users.store');
Route::get('users/create', [UserController::class, 'create'])->name('users.create');
Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// Clients
Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
Route::get('clients/create', function () {
    return Inertia::render('clients/Create');
})->name('clients.create');
Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');
Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

// Quotations
Route::get('quotations', [QuotationController::class, 'index'])->name('quotations.index');
Route::get('quotations/create', function () {
    return Inertia::render('quotations/Create', [
        'clients' => Client::select('id', 'name', 'email')->orderBy('name')->get(),
    ]);
})->name('quotations.create');
Route::post('quotations', [QuotationController::class, 'store'])->name('quotations.store');
Route::get('quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convertToInvoice'])->name('quotations.convert');

// Invoices
Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('invoices/create', function () {
    return Inertia::render('invoices/Create', [
        'clients' => Client::select('id', 'name', 'email')->orderBy('name')->get(),
    ]);
})->name('invoices.create');
Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
Route::get('invoices/{invoice}/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.download');
Route::post('invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
Route::post('invoices/{invoice}/send-whatsapp', [InvoiceController::class, 'sendWhatsApp'])->name('invoices.send-whatsapp');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

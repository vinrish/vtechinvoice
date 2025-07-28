<?php

declare(strict_types=1);

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('users', [UserController::class, 'index']);
Route::get('users/create', [UserController::class, 'create'])->name('users.create');
Route::get('users/{user}', [UserController::class, 'edit'])->name('users.edit');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);

// Products
Route::resource('products', ProductController::class);

// Sales
Route::resource('sales', SaleController::class)->except(['edit', 'update']);

// Expenses
Route::resource('expenses', ExpenseController::class)->except(['edit', 'update', 'show']);

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


// Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
    Route::get('/journal',   [ReportController::class, 'journal'])->name('journal');
});



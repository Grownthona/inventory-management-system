<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Products
Route::resource('products', ProductController::class);

// Sales
Route::resource('sales', SaleController::class)->except(['edit', 'update']);

// Expenses
Route::resource('expenses', ExpenseController::class)->except(['edit', 'update', 'show']);



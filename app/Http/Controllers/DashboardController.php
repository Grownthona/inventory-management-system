<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts  = Product::count();
        $totalSaleToday = Sale::whereDate('sale_date', today())->sum('net_amount');
        $totalExpToday  = Expense::whereDate('expense_date', today())->sum('amount');
        $totalDue       = Sale::sum('due_amount');

        $recentSales    = Sale::with('product')->latest()->take(5)->get();
        $lowStockProducts = Product::where('current_stock', '<=', 5)->get();

        $monthlySales = Sale::selectRaw("DATE_FORMAT(sale_date, '%Y-%m') as month, SUM(net_amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'totalProducts', 'totalSaleToday', 'totalExpToday',
            'totalDue', 'recentSales', 'lowStockProducts', 'monthlySales'
        ));
    }
}
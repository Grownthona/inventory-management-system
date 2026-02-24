<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Financial Report with date-wise filter
     * Shows: Total Sales, Total Expenses, Profit/Loss per day
     */
    public function financial(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate   = $request->input('to_date',   now()->toDateString());

        // --- Date-wise Sales ---
        $dailySales = Sale::selectRaw('
                sale_date,
                COUNT(*) as total_transactions,
                SUM(gross_amount) as total_gross,
                SUM(discount) as total_discount,
                SUM(vat_amount) as total_vat,
                SUM(net_amount) as total_net,
                SUM(paid_amount) as total_paid,
                SUM(due_amount) as total_due,
                SUM(quantity) as total_units
            ')
            ->whereBetween('sale_date', [$fromDate, $toDate])
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get()
            ->keyBy('sale_date');

        // --- Date-wise Expenses ---
        $dailyExpenses = Expense::selectRaw('
                expense_date,
                SUM(amount) as total_amount,
                COUNT(*) as total_count
            ')
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->groupBy('expense_date')
            ->orderBy('expense_date')
            ->get()
            ->keyBy('expense_date');

        // Merge dates
        $allDates = collect($dailySales->keys()->merge($dailyExpenses->keys())->unique()->sort()->values());

        $reportRows = $allDates->map(function ($date) use ($dailySales, $dailyExpenses) {
            $sale    = $dailySales->get($date);
            $expense = $dailyExpenses->get($date);

            $totalSale    = $sale    ? $sale->total_net    : 0;
            $totalExpense = $expense ? $expense->total_amount : 0;
            $profit       = $totalSale - $totalExpense;

            return [
                'date'          => $date,
                'total_sale'    => $totalSale,
                'total_expense' => $totalExpense,
                'profit_loss'   => $profit,
                'sale_detail'   => $sale,
                'expense_detail'=> $expense,
            ];
        });

        // --- Summary Totals ---
        $summary = [
            'total_gross_sales' => Sale::whereBetween('sale_date', [$fromDate, $toDate])->sum('gross_amount'),
            'total_discount'    => Sale::whereBetween('sale_date', [$fromDate, $toDate])->sum('discount'),
            'total_vat'         => Sale::whereBetween('sale_date', [$fromDate, $toDate])->sum('vat_amount'),
            'total_net_sales'   => Sale::whereBetween('sale_date', [$fromDate, $toDate])->sum('net_amount'),
            'total_paid'        => Sale::whereBetween('sale_date', [$fromDate, $toDate])->sum('paid_amount'),
            'total_due'         => Sale::whereBetween('sale_date', [$fromDate, $toDate])->sum('due_amount'),
            'total_expenses'    => Expense::whereBetween('expense_date', [$fromDate, $toDate])->sum('amount'),
        ];

        // COGS from journal (inventory credited during sales)
        $summary['total_cogs']       = Sale::whereBetween('sale_date', [$fromDate, $toDate])
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->selectRaw('SUM(sales.quantity * products.purchase_price) as cogs')
            ->value('cogs') ?? 0;

        $summary['gross_profit']     = $summary['total_net_sales'] - $summary['total_cogs'];
        $summary['net_profit_loss']  = $summary['gross_profit'] - $summary['total_expenses'];

        return view('reports.financial', compact('reportRows', 'summary', 'fromDate', 'toDate'));
    }

    /**
     * Journal Ledger Report
     */
    public function journal(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate   = $request->input('to_date',   now()->toDateString());

        $journals = JournalEntry::with('lines')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->orderBy('entry_date')
            ->get();

        return view('reports.journal', compact('journals', 'fromDate', 'toDate'));
    }
}
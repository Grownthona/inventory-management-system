<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest('expense_date')->get();
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'category'     => 'required|string',
            'expense_date' => 'required|date',
            'description'  => 'nullable|string',
        ]);

        $expense = Expense::create($validated);

        // Journal Entry for Expense
        $this->recordExpenseJournal($expense);

        return redirect()->route('expenses.index')
            ->with('success', "Expense '{$expense->title}' of {$expense->amount} TK recorded.");
    }

    public function destroy(Expense $expense)
    {
        JournalEntry::where('reference_type', 'expense')->where('reference_id', $expense->id)->delete();
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }

    /**
     * Journal for expense:
     *   DR Expense Account (e.g. Rent Expense)   500
     *   CR Cash / Bank                            500
     */
    private function recordExpenseJournal(Expense $expense): void
    {
        $journal = JournalEntry::create([
            'reference_no'   => 'EXP-' . str_pad($expense->id, 5, '0', STR_PAD_LEFT),
            'reference_type' => 'expense',
            'reference_id'   => $expense->id,
            'entry_date'     => $expense->expense_date,
            'description'    => "Expense: {$expense->title} ({$expense->category})",
        ]);

        $journal->lines()->createMany([
            [
                'account_name' => ucfirst($expense->category) . ' Expense',
                'account_type' => 'expense',
                'debit'        => $expense->amount,
                'credit'       => 0,
            ],
            [
                'account_name' => 'Cash / Bank',
                'account_type' => 'asset',
                'debit'        => 0,
                'credit'       => $expense->amount,
            ],
        ]);
    }
}
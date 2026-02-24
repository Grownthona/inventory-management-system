<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('product')->latest()->get();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('current_stock', '>', 0)->get();
        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'    => 'required|exists:products,id',
            'customer_name' => 'nullable|string|max:255',
            'quantity'      => 'required|integer|min:1',
            'discount'      => 'nullable|numeric|min:0',
            'vat_percent'   => 'nullable|numeric|min:0|max:100',
            'paid_amount'   => 'nullable|numeric|min:0',
            'sale_date'     => 'required|date',
            'note'          => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Stock check
        if ($product->current_stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => "Insufficient stock! Available: {$product->current_stock} units."])->withInput();
        }

        $discount   = $validated['discount']    ?? 0;
        $vatPercent = $validated['vat_percent'] ?? 0;
        $paidAmount = $validated['paid_amount'] ?? 0;

        // Calculate financials
        $amounts = Sale::calculateAmounts(
            $validated['quantity'],
            $product->sell_price,
            $discount,
            $vatPercent,
            $paidAmount
        );

        $invoiceNo = 'INV-' . strtoupper(uniqid());

        $sale = Sale::create([
            'invoice_no'    => $invoiceNo,
            'product_id'    => $product->id,
            'customer_name' => $validated['customer_name'] ?? 'Walk-in Customer',
            'quantity'      => $validated['quantity'],
            'unit_price'    => $product->sell_price,
            'gross_amount'  => $amounts['grossAmount'],
            'discount'      => $discount,
            'vat_percent'   => $vatPercent,
            'vat_amount'    => $amounts['vatAmount'],
            'net_amount'    => $amounts['netAmount'],
            'paid_amount'   => $paidAmount,
            'due_amount'    => $amounts['dueAmount'],
            'sale_date'     => $validated['sale_date'],
            'note'          => $validated['note'] ?? null,
        ]);

        // Reduce current stock
        $product->decrement('current_stock', $validated['quantity']);

        // Generate accounting journal entries
        $this->recordSaleJournal($sale, $product);

        return redirect()->route('sales.index')
            ->with('success', "Sale recorded! Invoice: {$invoiceNo}. Net: {$amounts['netAmount']} TK. Due: {$amounts['dueAmount']} TK.");
    }

    public function show(Sale $sale)
    {
        $sale->load(['product', 'journalEntry.lines']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Generate double-entry accounting journal for a sale
     *
     * Business Scenario:
     *  - Sold 10 units @ 200 TK = 2000 TK (Gross)
     *  - Discount: 50 TK
     *  - VAT 5% on (2000 - 50) = 97.5 TK
     *  - Net Amount = 2047.5 TK
     *  - Cash Received = 1000 TK
     *  - Due (Accounts Receivable) = 1047.5 TK
     *
     * Journal:
     *   DR Cash/Bank                    1000.00
     *   DR Accounts Receivable          1047.50
     *   DR Discount Allowed               50.00
     *   CR Sales Revenue                2000.00
     *   CR VAT Payable                    97.50
     *
     *   DR Cost of Goods Sold (COGS)    1000.00  (10 units × 100 purchase price)
     *   CR Inventory                    1000.00
     */
    private function recordSaleJournal(Sale $sale, Product $product): void
    {
        $cogs = $sale->quantity * $product->purchase_price;

        $journal = JournalEntry::create([
            'reference_no'   => 'JE-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT),
            'reference_type' => 'sale',
            'reference_id'   => $sale->id,
            'entry_date'     => $sale->sale_date,
            'description'    => "Sale Journal: Invoice {$sale->invoice_no} | Customer: {$sale->customer_name}",
        ]);

        $lines = [];


        // === REVENUE SIDE ===

        // DR Cash/Bank (amount received)
        if ($sale->paid_amount > 0) {
            $lines[] = [
                'account_name' => 'Cash / Bank',
                'account_type' => 'asset',
                'debit'        => $sale->paid_amount,
                'credit'       => 0,
            ];
        }

        // DR Accounts Receivable (due amount)
        if ($sale->due_amount > 0) {
            $lines[] = [
                'account_name' => 'Accounts Receivable',
                'account_type' => 'asset',
                'debit'        => $sale->due_amount,
                'credit'       => 0,
            ];
        }

        // DR Discount Allowed (expense for seller)
        if ($sale->discount > 0) {
            $lines[] = [
                'account_name' => 'Discount Allowed',
                'account_type' => 'expense',
                'debit'        => $sale->discount,
                'credit'       => 0,
            ];
        }

        // CR Sales Revenue (gross amount)
        $lines[] = [
            'account_name' => 'Sales Revenue',
            'account_type' => 'revenue',
            'debit'        => 0,
            'credit'       => $sale->gross_amount,
        ];

        // CR VAT Payable
        if ($sale->vat_amount > 0) {
            $lines[] = [
                'account_name' => 'VAT Payable',
                'account_type' => 'liability',
                'debit'        => 0,
                'credit'       => $sale->vat_amount,
            ];
        }

        // === COST OF GOODS SOLD SIDE ===

        // DR Cost of Goods Sold (COGS)
        $lines[] = [
            'account_name' => 'Cost of Goods Sold (COGS)',
            'account_type' => 'expense',
            'debit'        => $cogs,
            'credit'       => 0,
        ];

        // CR Inventory (reduce inventory asset)
        $lines[] = [
            'account_name' => 'Inventory (Stock)',
            'account_type' => 'asset',
            'debit'        => 0,
            'credit'       => $cogs,
        ];

        // CR Customer Overpayment (if paid more than net)
        $overpayment = max(0, $sale->paid_amount - $sale->net_amount);
        if ($overpayment > 0) {
            $lines[] = [
                'account_name' => 'Customer Overpayment',
                'account_type' => 'liability',
                'debit'        => 0,
                'credit'       => $overpayment,
            ];
        }

        // Add this temporarily:
        //dd($journal->id, $journal->wasRecentlyCreated, $lines);

        $created = $journal->lines()->createMany($lines);

        Log::info('Journal lines created: ' . count($created));
    }

    public function destroy(Sale $sale)
    {
        // Restore stock
        $sale->product->increment('current_stock', $sale->quantity);
        // Delete journal
        $journal = JournalEntry::where('reference_type', 'sale')->where('reference_id', $sale->id)->first();
        $journal?->delete();
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Sale deleted and stock restored.');
    }
}
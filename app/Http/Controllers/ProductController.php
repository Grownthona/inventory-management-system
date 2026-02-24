<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\JournalEntry;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'sku'            => 'nullable|string|unique:products,sku',
            'purchase_price' => 'required|numeric|min:0',
            'sell_price'     => 'required|numeric|min:0',
            'opening_stock'  => 'required|integer|min:0',
            'description'    => 'nullable|string',
        ]);

        $validated['current_stock'] = $validated['opening_stock'];

        $product = Product::create($validated);

        // Journal Entry: Record opening stock purchase value
        $this->recordPurchaseJournal($product);

        return redirect()->route('products.index')
            ->with('success', "Product '{$product->name}' created successfully with opening stock of {$product->opening_stock} units.");
    }

    public function show(Product $product)
    {
        $product->load('sales');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'sku'            => 'nullable|string|unique:products,sku,' . $product->id,
            'purchase_price' => 'required|numeric|min:0',
            'sell_price'     => 'required|numeric|min:0',
            'description'    => 'nullable|string',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', "Product '{$product->name}' updated successfully.");
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Record journal entry for opening stock (inventory purchase)
     */
    private function recordPurchaseJournal(Product $product): void
    {
        $totalValue = $product->opening_stock * $product->purchase_price;

        $journal = JournalEntry::create([
            'reference_no'   => 'PUR-' . str_pad($product->id, 5, '0', STR_PAD_LEFT),
            'reference_type' => 'purchase',
            'reference_id'   => $product->id,
            'entry_date'     => now()->toDateString(),
            'description'    => "Opening stock entry for: {$product->name} ({$product->opening_stock} units @ {$product->purchase_price} TK)",
        ]);

        // DR Inventory / Stock Asset
        $journal->lines()->create([
            'account_name' => 'Inventory (Stock)',
            'account_type' => 'asset',
            'debit'        => $totalValue,
            'credit'       => 0,
        ]);

        // CR Capital / Accounts Payable
        $journal->lines()->create([
            'account_name' => 'Capital / Accounts Payable',
            'account_type' => 'liability',
            'debit'        => 0,
            'credit'       => $totalValue,
        ]);
    }
}
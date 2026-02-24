@extends('layouts.app')
@section('title', 'Product Details')
@section('breadcrumb', 'Inventory / Products / Details')

@section('content')
<div class="page-header d-flex justify-content-between">
    <h4><i class="bi bi-box-seam me-2 text-primary"></i>{{ $product->name }}</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Product Info</h6></div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr><th class="text-muted">SKU</th><td>{{ $product->sku ?? '—' }}</td></tr>
                    <tr><th class="text-muted">Purchase Price</th><td><strong>{{ number_format($product->purchase_price, 2) }} TK</strong></td></tr>
                    <tr><th class="text-muted">Sell Price</th><td><strong class="text-success">{{ number_format($product->sell_price, 2) }} TK</strong></td></tr>
                    <tr><th class="text-muted">Margin</th><td>{{ number_format((($product->sell_price - $product->purchase_price) / $product->purchase_price) * 100, 1) }}%</td></tr>
                    <tr><th class="text-muted">Opening Stock</th><td>{{ $product->opening_stock }} units</td></tr>
                    <tr><th class="text-muted">Total Sold</th><td>{{ $product->total_sold }} units</td></tr>
                    <tr><th class="text-muted">Current Stock</th>
                        <td>
                            @if($product->current_stock == 0)
                                <span class="badge bg-danger">Out of Stock</span>
                            @else
                                <span class="badge bg-success">{{ $product->current_stock }} units</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th class="text-muted">Stock Value</th><td>{{ number_format($product->current_stock * $product->purchase_price, 2) }} TK</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Sales History</h6></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Invoice</th><th>Customer</th><th>Qty</th><th>Net Amount</th><th>Due</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @forelse($product->sales as $sale)
                        <tr>
                            <td><a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_no }}</a></td>
                            <td>{{ $sale->customer_name }}</td>
                            <td>{{ $sale->quantity }}</td>
                            <td>{{ number_format($sale->net_amount, 2) }} TK</td>
                            <td>
                                @if($sale->due_amount > 0)
                                    <span class="badge bg-danger">{{ number_format($sale->due_amount, 2) }}</span>
                                @else
                                    <span class="badge bg-success">Paid</span>
                                @endif
                            </td>
                            <td>{{ $sale->sale_date->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No sales recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
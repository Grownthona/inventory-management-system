<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    </head>
    <body class="p-4">

        <!-- your existing body content unchanged -->
         <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h4><i class="bi bi-cart-check me-2 text-success"></i>Sales</h4>
                <p class="text-muted mb-0">All recorded sales transactions</p>
            </div>
            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> New Sale
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Qty</th>
                                <th>Gross</th>
                                <th>Discount</th>
                                <th>VAT</th>
                                <th>Net Amount</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                            <tr>
                                <td><a href="{{ route('sales.show', $sale) }}" class="fw-semibold text-decoration-none">{{ $sale->invoice_no }}</a></td>
                                <td>{{ $sale->product->name }}</td>
                                <td>{{ $sale->customer_name }}</td>
                                <td>{{ $sale->quantity }}</td>
                                <td>{{ number_format($sale->gross_amount, 2) }}</td>
                                <td class="text-danger">{{ $sale->discount > 0 ? number_format($sale->discount, 2) : '—' }}</td>
                                <td class="text-info">{{ $sale->vat_amount > 0 ? number_format($sale->vat_amount, 2) : '—' }}</td>
                                <td class="text-success fw-bold">{{ number_format($sale->net_amount, 2) }} TK</td>
                                <td class="text-success">{{ number_format($sale->paid_amount, 2) }}</td>
                                <td>
                                    @if($sale->due_amount > 0)
                                        <span class="badge bg-danger">{{ number_format($sale->due_amount, 2) }}</span>
                                    @else
                                        <span class="badge bg-success">Paid</span>
                                    @endif
                                </td>
                                <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                        <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Delete this sale? Stock will be restored.')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-5">
                                    <i class="bi bi-cart-x fs-1 d-block mb-3 opacity-50"></i>
                                    No sales recorded yet. <a href="{{ route('sales.create') }}">Record first sale</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($sales->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4">Totals</td>
                                <td>{{ number_format($sales->sum('gross_amount'), 2) }}</td>
                                <td>{{ number_format($sales->sum('discount'), 2) }}</td>
                                <td>{{ number_format($sales->sum('vat_amount'), 2) }}</td>
                                <td class="text-success">{{ number_format($sales->sum('net_amount'), 2) }} TK</td>
                                <td>{{ number_format($sales->sum('paid_amount'), 2) }}</td>
                                <td class="text-danger">{{ number_format($sales->sum('due_amount'), 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
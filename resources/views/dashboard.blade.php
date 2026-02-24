@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
        <p class="text-muted mb-0">Overview of your inventory & financials</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>New Sale
        </a>
        <a href="{{ route('products.create') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-box-seam me-1"></i>Add Product
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-box-seam text-primary"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Products</div>
                    <div class="fw-bold fs-4">{{ $totalProducts }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-cart-check text-success"></i>
                </div>
                <div>
                    <div class="text-muted small">Today's Sales</div>
                    <div class="fw-bold fs-4">{{ number_format($totalSaleToday, 2) }} TK</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10">
                    <i class="bi bi-cash-stack text-danger"></i>
                </div>
                <div>
                    <div class="text-muted small">Today's Expenses</div>
                    <div class="fw-bold fs-4">{{ number_format($totalExpToday, 2) }} TK</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10">
                    <i class="bi bi-exclamation-circle text-warning"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Due (Receivable)</div>
                    <div class="fw-bold fs-4">{{ number_format($totalDue, 2) }} TK</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Recent Sales -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Sales</h6>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Net Amount</th>
                                <th>Due</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                            <tr>
                                <td><a href="{{ route('sales.show', $sale) }}" class="text-decoration-none">{{ $sale->invoice_no }}</a></td>
                                <td>{{ $sale->product->name }}</td>
                                <td>{{ $sale->customer_name }}</td>
                                <td class="text-success fw-bold">{{ number_format($sale->net_amount, 2) }} TK</td>
                                <td>
                                    @if($sale->due_amount > 0)
                                        <span class="badge bg-danger">{{ number_format($sale->due_amount, 2) }} TK</span>
                                    @else
                                        <span class="badge bg-success">Paid</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $sale->sale_date->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No sales yet. <a href="{{ route('sales.create') }}">Create first sale</a></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock & Quick Actions -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Low Stock Alert</h6>
            </div>
            <div class="card-body">
                @forelse($lowStockProducts as $p)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                    <span class="small">{{ $p->name }}</span>
                    <span class="badge bg-{{ $p->current_stock == 0 ? 'danger' : 'warning' }}">
                        {{ $p->current_stock }} left
                    </span>
                </div>
                @empty
                <p class="text-muted text-center py-3">
                    <i class="bi bi-check-circle text-success fs-4 d-block mb-2"></i>
                    All products have healthy stock!
                </p>
                @endforelse
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-lightning me-2 text-primary"></i>Quick Actions</h6>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-cart-plus me-1"></i> Record New Sale
                </a>
                <a href="{{ route('products.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Add Product
                </a>
                <a href="{{ route('expenses.create') }}" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-dash-circle me-1"></i> Add Expense
                </a>
                <a href="{{ route('reports.financial') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-bar-chart me-1"></i> View Reports
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
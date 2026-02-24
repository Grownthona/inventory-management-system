@extends('layouts.app')
@section('title', 'Products')
@section('breadcrumb', 'Inventory / Products')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="bi bi-box-seam me-2 text-primary"></i>Products</h4>
        <p class="text-muted mb-0">Manage your inventory products</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Product
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th>Purchase Price</th>
                        <th>Sell Price</th>
                        <th>Opening Stock</th>
                        <th>Current Stock</th>
                        <th>Total Sold</th>
                        <th>Stock Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $i => $product)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            @if($product->description)
                                <div class="text-muted small">{{ Str::limit($product->description, 40) }}</div>
                            @endif
                        </td>
                        <td><span class="text-muted">{{ $product->sku ?? '—' }}</span></td>
                        <td>{{ number_format($product->purchase_price, 2) }} TK</td>
                        <td class="text-success fw-bold">{{ number_format($product->sell_price, 2) }} TK</td>
                        <td>{{ $product->opening_stock }}</td>
                        <td>
                            @if($product->current_stock == 0)
                                <span class="badge bg-danger">Out of Stock</span>
                            @elseif($product->current_stock <= 5)
                                <span class="badge bg-warning text-dark">{{ $product->current_stock }} (Low)</span>
                            @else
                                <span class="badge bg-success">{{ $product->current_stock }}</span>
                            @endif
                        </td>
                        <td>{{ $product->total_sold }}</td>
                        <td>{{ number_format($product->current_stock * $product->purchase_price, 2) }} TK</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            <i class="bi bi-box-seam fs-1 d-block mb-3 text-muted opacity-50"></i>
                            No products yet. <a href="{{ route('products.create') }}">Add your first product</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
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

    <div class="page-header">
        <h4><i class="bi bi-pencil me-2 text-warning"></i>Edit Product</h4>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('products.update', $product) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Product Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">SKU</label>
                                    <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Purchase Price (TK)</label>
                                    <input type="number" name="purchase_price" step="0.01" class="form-control" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Sell Price (TK)</label>
                                    <input type="number" name="sell_price" step="0.01" class="form-control" value="{{ old('sell_price', $product->sell_price) }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                                </div>
                            </div>
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Current Stock: <strong>{{ $product->current_stock }} units</strong> — Stock can only be changed through sales transactions.
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-check-circle me-1"></i> Update Product
                                </button>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
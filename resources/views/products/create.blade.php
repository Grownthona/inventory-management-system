@extends('layouts.app')
@section('title', 'Add Product')
@section('breadcrumb', 'Inventory / Products / Add')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-plus-circle me-2 text-primary"></i>Add New Product</h4>
    <p class="text-muted mb-0">Enter product details and opening stock</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Product Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="e.g. Mobile Phone Model X" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">SKU / Code</label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                   value="{{ old('sku') }}" placeholder="e.g. MOB-001">
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Purchase Price (TK) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">TK</span>
                                <input type="number" name="purchase_price" step="0.01" min="0"
                                       class="form-control @error('purchase_price') is-invalid @enderror"
                                       value="{{ old('purchase_price', 100) }}" required>
                            </div>
                            @error('purchase_price')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sell Price (TK) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">TK</span>
                                <input type="number" name="sell_price" step="0.01" min="0"
                                       class="form-control @error('sell_price') is-invalid @enderror"
                                       value="{{ old('sell_price', 200) }}" required>
                            </div>
                            @error('sell_price')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Opening Stock (units) <span class="text-danger">*</span></label>
                            <input type="number" name="opening_stock" min="0"
                                   class="form-control @error('opening_stock') is-invalid @enderror"
                                   value="{{ old('opening_stock', 50) }}" required>
                            @error('opening_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">A journal entry (Dr. Inventory / Cr. Capital) will be auto-created.</div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Optional product description...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Preview Box -->
                    <div class="alert alert-info mt-4" id="preview-box">
                        <strong><i class="bi bi-info-circle me-1"></i>Opening Stock Journal Entry Preview:</strong>
                        <div class="mt-2 small">
                            <div class="d-flex justify-content-between"><span>DR Inventory (Stock)</span> <span id="prev-value">5,000.00 TK</span></div>
                            <div class="d-flex justify-content-between"><span>CR Capital / Accounts Payable</span> <span id="prev-value2">5,000.00 TK</span></div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Save Product
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updatePreview() {
    const pp = parseFloat(document.querySelector('[name=purchase_price]').value) || 0;
    const os = parseInt(document.querySelector('[name=opening_stock]').value) || 0;
    const val = (pp * os).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    document.getElementById('prev-value').textContent = val + ' TK';
    document.getElementById('prev-value2').textContent = val + ' TK';
}
document.querySelector('[name=purchase_price]').addEventListener('input', updatePreview);
document.querySelector('[name=opening_stock]').addEventListener('input', updatePreview);
updatePreview();
</script>
@endpush
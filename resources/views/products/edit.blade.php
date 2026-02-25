@extends('layouts.app')
@section('title', 'Edit Product')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Product</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf
            @method('PUT')

            {{-- Product Name --}}
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $product->name) }}"
                    class="form-control"
                    required
                >
            </div>

            {{-- SKU --}}
            <div class="mb-3">
                <label class="form-label">SKU</label>
                <input
                    type="text"
                    name="sku"
                    value="{{ old('sku', $product->sku) }}"
                    class="form-control"
                >
            </div>

            {{-- Purchase Price --}}
            <div class="mb-3">
                <label class="form-label">Purchase Price</label>
                <input
                    type="number"
                    step="0.01"
                    name="purchase_price"
                    value="{{ old('purchase_price', $product->purchase_price) }}"
                    class="form-control"
                    required
                >
            </div>

            {{-- Sell Price --}}
            <div class="mb-3">
                <label class="form-label">Sell Price</label>
                <input
                    type="number"
                    step="0.01"
                    name="sell_price"
                    value="{{ old('sell_price', $product->sell_price) }}"
                    class="form-control"
                    required
                >
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea
                    name="description"
                    class="form-control"
                    rows="3"
                >{{ old('description', $product->description) }}</textarea>
            </div>

            <button class="btn btn-primary">
                Update Product
            </button>

            <a href="{{ route('products.show', $product) }}" class="btn btn-secondary">
                Cancel
            </a>
        </form>
    </div>
</div>
@endsection
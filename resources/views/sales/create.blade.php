@extends('layouts.app')
@section('title', 'New Sale')
@section('breadcrumb', 'Inventory / Sales / New Sale')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-cart-plus me-2 text-success"></i>Record New Sale</h4>
    <p class="text-muted mb-0">Fill in the details below. Amounts are calculated automatically.</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_select" class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">-- Select Product --</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    data-price="{{ $product->sell_price }}"
                                    data-stock="{{ $product->current_stock }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (Stock: {{ $product->current_stock }})
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text" id="stock-info"></div>
                            @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control"
                                   value="{{ old('customer_name') }}" placeholder="Walk-in Customer">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Unit Price (TK)</label>
                            <input type="text" id="unit_price_display" class="form-control" readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" min="1"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', 10) }}" required>
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Discount (TK)</label>
                            <input type="number" name="discount" id="discount" step="0.01" min="0"
                                   class="form-control" value="{{ old('discount', 50) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">VAT (%)</label>
                            <input type="number" name="vat_percent" id="vat_percent" step="0.01" min="0" max="100"
                                   class="form-control" value="{{ old('vat_percent', 5) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Payment Received (TK)</label>
                            <input type="number" name="paid_amount" id="paid_amount" step="0.01" min="0"
                                   class="form-control" value="{{ old('paid_amount', 1000) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sale Date <span class="text-danger">*</span></label>
                            <input type="date" name="sale_date" class="form-control"
                                   value="{{ old('sale_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Note</label>
                            <input type="text" name="note" class="form-control" value="{{ old('note') }}" placeholder="Optional note">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle me-1"></i> Save Sale & Generate Journal
                        </button>
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Calculation Panel -->
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-header bg-success text-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-calculator me-2"></i>Live Calculation</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Gross Amount</td>
                        <td class="text-end fw-bold" id="calc-gross">0.00 TK</td>
                    </tr>
                    <tr>
                        <td class="text-muted">(-) Discount</td>
                        <td class="text-end text-danger" id="calc-discount">0.00 TK</td>
                    </tr>
                    <tr>
                        <td class="text-muted">(+) VAT Amount</td>
                        <td class="text-end text-info" id="calc-vat">0.00 TK</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-bold">Net Amount</td>
                        <td class="text-end fw-bold text-success fs-5" id="calc-net">0.00 TK</td>
                    </tr>
                    <tr>
                        <td class="text-muted">(-) Payment Received</td>
                        <td class="text-end" id="calc-paid">0.00 TK</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-bold">Due Amount</td>
                        <td class="text-end fw-bold text-danger fs-5" id="calc-due">0.00 TK</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-3 border-info">
            <div class="card-header bg-info text-white py-2">
                <h6 class="mb-0 small fw-bold"><i class="bi bi-journal-text me-1"></i>Journal Preview</h6>
            </div>
            <div class="card-body p-2">
                <div class="small font-monospace" id="journal-preview">
                    <div class="text-muted">Select a product to preview journal entries</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const productSelect = document.getElementById('product_select');
const quantityInput = document.getElementById('quantity');
const discountInput = document.getElementById('discount');
const vatInput      = document.getElementById('vat_percent');
const paidInput     = document.getElementById('paid_amount');
const unitPriceDisp = document.getElementById('unit_price_display');

function fmt(n) { return parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }

function calculate() {
    const opt    = productSelect.options[productSelect.selectedIndex];
    if (!opt || !opt.value) return;

    const price  = parseFloat(opt.dataset.price) || 0;
    const qty    = parseInt(quantityInput.value)  || 0;
    const disc   = parseFloat(discountInput.value) || 0;
    const vat    = parseFloat(vatInput.value)      || 0;
    const paid   = parseFloat(paidInput.value)     || 0;

    const gross    = price * qty;
    const vatAmt   = ((gross - disc) * vat / 100);
    const net      = gross - disc + vatAmt;
    const due      = Math.max(0, net - paid);

    document.getElementById('calc-gross').textContent    = fmt(gross) + ' TK';
    document.getElementById('calc-discount').textContent  = fmt(disc) + ' TK';
    document.getElementById('calc-vat').textContent      = fmt(vatAmt) + ' TK';
    document.getElementById('calc-net').textContent      = fmt(net) + ' TK';
    document.getElementById('calc-paid').textContent     = fmt(paid) + ' TK';
    document.getElementById('calc-due').textContent      = fmt(due) + ' TK';

    // Journal Preview
    const jp = document.getElementById('journal-preview');
    let html = `<div class="mb-1 text-primary fw-bold">── Revenue Entries ──</div>`;
    if (paid > 0) html += `<div>DR Cash/Bank <span class="float-end text-success">${fmt(paid)}</span></div>`;
    if (due > 0)  html += `<div>DR Accts Receivable <span class="float-end text-success">${fmt(due)}</span></div>`;
    if (disc > 0) html += `<div>DR Discount Allowed <span class="float-end text-success">${fmt(disc)}</span></div>`;
    html += `<div>CR Sales Revenue <span class="float-end text-danger">${fmt(gross)}</span></div>`;
    if (vatAmt > 0) html += `<div>CR VAT Payable <span class="float-end text-danger">${fmt(vatAmt)}</span></div>`;
    html += `<div class="mt-2 mb-1 text-primary fw-bold">── COGS Entries ──</div>`;
    html += `<div class="text-muted">(Calculated from purchase price)</div>`;
    jp.innerHTML = html;
}

productSelect.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt && opt.value) {
        unitPriceDisp.value = parseFloat(opt.dataset.price).toFixed(2) + ' TK';
        document.getElementById('stock-info').textContent = `Available stock: ${opt.dataset.stock} units`;
    }
    calculate();
});

[quantityInput, discountInput, vatInput, paidInput].forEach(el => el.addEventListener('input', calculate));
calculate();
</script>
@endpush
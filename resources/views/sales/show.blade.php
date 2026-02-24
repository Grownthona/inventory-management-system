@extends('layouts.app')

@section('title', 'Invoice ' . $sale->invoice_no)
@section('breadcrumb', 'Sales > Invoice ' . $sale->invoice_no)

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h4><i class="bi bi-receipt me-2 text-success"></i>Invoice: {{ $sale->invoice_no }}</h4>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i>Print
        </button>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <!-- Invoice Card -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white py-3">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-0">Sales Invoice</h5>
                    <span>{{ $sale->invoice_no }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-muted small">Customer</div>
                        <div class="fw-bold">{{ $sale->customer_name }}</div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="text-muted small">Date</div>
                        <div class="fw-bold">{{ $sale->sale_date->format('d M Y') }}</div>
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $sale->product->name }}</td>
                            <td>{{ $sale->quantity }}</td>
                            <td>{{ number_format($sale->unit_price, 2) }} TK</td>
                            <td class="text-end">{{ number_format($sale->gross_amount, 2) }} TK</td>
                        </tr>
                    </tbody>
                </table>

                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Gross Amount:</td>
                                <td class="text-end">{{ number_format($sale->gross_amount, 2) }} TK</td>
                            </tr>
                            @if($sale->discount > 0)
                            <tr>
                                <td class="text-danger">(-) Discount:</td>
                                <td class="text-end text-danger">{{ number_format($sale->discount, 2) }} TK</td>
                            </tr>
                            @endif
                            @if($sale->vat_amount > 0)
                            <tr>
                                <td class="text-info">(+) VAT ({{ $sale->vat_percent }}%):</td>
                                <td class="text-end text-info">{{ number_format($sale->vat_amount, 2) }} TK</td>
                            </tr>
                            @endif
                            <tr class="border-top fw-bold">
                                <td>Net Amount:</td>
                                <td class="text-end text-success fs-5">{{ number_format($sale->net_amount, 2) }} TK</td>
                            </tr>
                            <tr>
                                <td>Payment Received:</td>
                                <td class="text-end text-success">{{ number_format($sale->paid_amount, 2) }} TK</td>
                            </tr>
                            <tr class="border-top">
                                <td class="fw-bold">Due Balance:</td>
                                <td class="text-end">
                                    @if($sale->due_amount > 0)
                                        <span class="text-danger fw-bold fs-5">{{ number_format($sale->due_amount, 2) }} TK</span>
                                    @else
                                        <span class="badge bg-success">FULLY PAID</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Journal Entries -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2 text-primary"></i>Journal Entry</h6>
                @if($sale->journalEntry)
                    <small class="text-muted">Ref: {{ $sale->journalEntry->reference_no }}</small>
                @endif
            </div>
            <div class="card-body p-0">
                @if($sale->journalEntry)
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Account</th>
                            <th>Type</th>
                            <th class="text-end">Dr</th>
                            <th class="text-end">Cr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->journalEntry->lines as $line)
                        <tr>
                            <td class="small">{{ $line->account_name }}</td>
                            <td>
                                <span class="badge bg-{{ $line->account_type == 'asset' ? 'primary' : ($line->account_type == 'revenue' ? 'success' : ($line->account_type == 'expense' ? 'danger' : ($line->account_type == 'liability' ? 'warning text-dark' : 'secondary'))) }} bg-opacity-10 text-{{ $line->account_type == 'asset' ? 'primary' : ($line->account_type == 'revenue' ? 'success' : ($line->account_type == 'expense' ? 'danger' : ($line->account_type == 'liability' ? 'warning' : 'secondary'))) }} small">{{ $line->account_type }}</span>
                            </td>
                            <td class="text-end text-success small">{{ $line->debit > 0 ? number_format($line->debit, 2) : '—' }}</td>
                            <td class="text-end text-danger small">{{ $line->credit > 0 ? number_format($line->credit, 2) : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2">Total</td>
                            <td class="text-end text-success">{{ number_format($sale->journalEntry->lines->sum('debit'), 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($sale->journalEntry->lines->sum('credit'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="p-3 text-center text-muted small bg-light">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Balanced: Total Debit = Total Credit ✓
                </div>
                @else
                <div class="p-3 text-center text-muted">No journal entry found.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
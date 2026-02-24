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

        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h4><i class="bi bi-journal-text me-2 text-primary"></i>Journal Ledger</h4>
                <p class="text-muted mb-0">All accounting journal entries (double-entry bookkeeping)</p>
            </div>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer me-1"></i> Print
            </button>
        </div>

        <!-- Date Filter -->
        <div class="card mb-4">
            <div class="card-body py-3">
                <form action="{{ route('reports.journal') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                        <a href="{{ route('reports.journal') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @forelse($journals as $journal)
        <div class="card mb-3">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-{{ $journal->reference_type == 'sale' ? 'success' : ($journal->reference_type == 'expense' ? 'danger' : 'primary') }} me-2">
                        {{ strtoupper($journal->reference_type) }}
                    </span>
                    <strong>{{ $journal->reference_no }}</strong>
                    <span class="text-muted ms-2 small">{{ $journal->description }}</span>
                </div>
                <small class="text-muted">{{ $journal->entry_date->format('d M Y') }}</small>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:35%">Account Name</th>
                            <th style="width:15%">Account Type</th>
                            <th class="text-end" style="width:25%">Debit (DR)</th>
                            <th class="text-end" style="width:25%">Credit (CR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journal->lines as $line)
                        <tr>
                            <td>
                                @if($line->debit > 0)
                                    <span class="ms-0">{{ $line->account_name }}</span>
                                @else
                                    <span class="ms-4 text-muted fst-italic">{{ $line->account_name }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $typeColors = ['asset' => 'primary', 'liability' => 'warning', 'revenue' => 'success', 'expense' => 'danger', 'equity' => 'info'];
                                    $color = $typeColors[$line->account_type] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-15 text-{{ $color }} small border border-{{ $color }} border-opacity-25">
                                    {{ ucfirst($line->account_type) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($line->debit > 0)
                                    <strong class="text-success">{{ number_format($line->debit, 2) }} TK</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($line->credit > 0)
                                    <strong class="text-danger">{{ number_format($line->credit, 2) }} TK</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="fw-bold">Total</td>
                            <td class="text-end fw-bold text-success">{{ number_format($journal->lines->sum('debit'), 2) }} TK</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($journal->lines->sum('credit'), 2) }} TK</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-journal-x fs-1 d-block mb-3 opacity-50"></i>
                No journal entries found for the selected date range.
            </div>
        </div>
        @endforelse

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

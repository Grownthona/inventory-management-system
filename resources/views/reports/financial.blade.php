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
                <h4><i class="bi bi-bar-chart-line me-2 text-success"></i>Financial Report</h4>
                <p class="text-muted mb-0">Date-wise sales, expenses & profit/loss summary</p>
            </div>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer me-1"></i> Print
            </button>
        </div>

        <!-- Date Filter -->
        <div class="card mb-4">
            <div class="card-body py-3">
                <form action="{{ route('reports.financial') }}" method="GET" class="row g-3 align-items-end">
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
                        <a href="{{ route('reports.financial') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-success bg-opacity-10 border-0">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Total Net Sales</div>
                        <div class="fw-bold fs-4 text-success">{{ number_format($summary['total_net_sales'], 2) }} TK</div>
                        <div class="small text-muted mt-1">Gross: {{ number_format($summary['total_gross_sales'], 2) }} TK</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger bg-opacity-10 border-0">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Total Expenses</div>
                        <div class="fw-bold fs-4 text-danger">{{ number_format($summary['total_expenses'], 2) }} TK</div>
                        <div class="small text-muted mt-1">COGS: {{ number_format($summary['total_cogs'], 2) }} TK</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info bg-opacity-10 border-0">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Gross Profit</div>
                        <div class="fw-bold fs-4 text-info">{{ number_format($summary['gross_profit'], 2) }} TK</div>
                        <div class="small text-muted mt-1">After COGS deduction</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 {{ $summary['net_profit_loss'] >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Net Profit / Loss</div>
                        <div class="fw-bold fs-4 {{ $summary['net_profit_loss'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($summary['net_profit_loss'], 2) }} TK
                        </div>
                        <div class="small text-muted mt-1">
                            {{ $summary['net_profit_loss'] >= 0 ? '✅ Profit' : '❌ Loss' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Summary Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 bg-light">
                    <div class="card-body py-2">
                        <span class="text-muted small">Total Discount Given: </span>
                        <span class="fw-bold text-warning">{{ number_format($summary['total_discount'], 2) }} TK</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light">
                    <div class="card-body py-2">
                        <span class="text-muted small">Total VAT Collected: </span>
                        <span class="fw-bold text-info">{{ number_format($summary['total_vat'], 2) }} TK</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light">
                    <div class="card-body py-2">
                        <span class="text-muted small">Total Due (Receivable): </span>
                        <span class="fw-bold text-danger">{{ number_format($summary['total_due'], 2) }} TK</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date-wise Table -->
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-table me-2 text-primary"></i>Date-wise Financial Summary</h6>
                <small class="text-muted">{{ $fromDate }} to {{ $toDate }}</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Sales (Net)</th>
                                <th>Discount</th>
                                <th>VAT</th>
                                <th>Units Sold</th>
                                <th>Total Expenses</th>
                                <th>Profit / Loss</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportRows as $row)
                            <tr>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</strong>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($row['date'])->format('l') }}</div>
                                </td>
                                <td class="text-success fw-bold">
                                    {{ number_format($row['total_sale'], 2) }} TK
                                    @if($row['sale_detail'])
                                        <div class="text-muted small">{{ $row['sale_detail']->total_transactions }} transaction(s)</div>
                                    @endif
                                </td>
                                <td class="text-warning">
                                    {{ $row['sale_detail'] ? number_format($row['sale_detail']->total_discount, 2) : '—' }}
                                </td>
                                <td class="text-info">
                                    {{ $row['sale_detail'] ? number_format($row['sale_detail']->total_vat, 2) : '—' }}
                                </td>
                                <td>
                                    {{ $row['sale_detail'] ? $row['sale_detail']->total_units . ' units' : '—' }}
                                </td>
                                <td class="text-danger fw-bold">
                                    {{ number_format($row['total_expense'], 2) }} TK
                                    @if($row['expense_detail'])
                                        <div class="text-muted small">{{ $row['expense_detail']->total_count }} item(s)</div>
                                    @endif
                                </td>
                                <td>
                                    @if($row['profit_loss'] >= 0)
                                        <span class="profit-positive">
                                            <i class="bi bi-arrow-up-circle me-1"></i>{{ number_format($row['profit_loss'], 2) }} TK
                                        </span>
                                    @else
                                        <span class="profit-negative">
                                            <i class="bi bi-arrow-down-circle me-1"></i>{{ number_format(abs($row['profit_loss']), 2) }} TK
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-3 opacity-50"></i>
                                    No data found for the selected date range.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($reportRows->isNotEmpty())
                        <tfoot class="table-dark fw-bold">
                            <tr>
                                <td>Grand Total</td>
                                <td class="text-success">{{ number_format($summary['total_net_sales'], 2) }} TK</td>
                                <td class="text-warning">{{ number_format($summary['total_discount'], 2) }}</td>
                                <td class="text-info">{{ number_format($summary['total_vat'], 2) }}</td>
                                <td>—</td>
                                <td class="text-danger">{{ number_format($summary['total_expenses'], 2) }} TK</td>
                                <td class="{{ $summary['net_profit_loss'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($summary['net_profit_loss'], 2) }} TK
                                </td>
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
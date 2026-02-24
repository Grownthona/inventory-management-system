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
                <h4><i class="bi bi-cash-stack me-2 text-danger"></i>Expenses</h4>
                <p class="text-muted mb-0">Track all business expenses</p>
            </div>
            <a href="{{ route('expenses.create') }}" class="btn btn-danger">
                <i class="bi bi-plus-circle me-1"></i> Add Expense
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>#</th><th>Title</th><th>Category</th><th>Amount</th><th>Date</th><th>Description</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $i => $expense)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $expense->title }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($expense->category) }}</span></td>
                                <td class="text-danger fw-bold">{{ number_format($expense->amount, 2) }} TK</td>
                                <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                <td class="text-muted small">{{ $expense->description ?? '—' }}</td>
                                <td>
                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Delete expense?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No expenses recorded. <a href="{{ route('expenses.create') }}">Add one</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($expenses->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3">Total</td>
                                <td class="text-danger">{{ number_format($expenses->sum('amount'), 2) }} TK</td>
                                <td colspan="3"></td>
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
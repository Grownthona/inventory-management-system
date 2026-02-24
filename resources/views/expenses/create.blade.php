@extends('layouts.app')
@section('title', 'Add Expense')
@section('breadcrumb', 'Inventory / Expenses / Add')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-dash-circle me-2 text-danger"></i>Add Expense</h4>
</div>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Expense Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. Office Rent" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="general">General</option>
                                <option value="rent">Rent</option>
                                <option value="salary">Salary</option>
                                <option value="utilities">Utilities</option>
                                <option value="transport">Transport</option>
                                <option value="marketing">Marketing</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount (TK) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">TK</span>
                                <input type="number" name="amount" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Optional details...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 small">
                        <i class="bi bi-info-circle me-1"></i>
                        A journal entry will be auto-created: <strong>DR [Category] Expense / CR Cash/Bank</strong>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-danger"><i class="bi bi-check-circle me-1"></i> Save Expense</button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
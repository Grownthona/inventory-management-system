<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventory Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary: #1a73e8;
            --dark-bg: #0f172a;
            --sidebar-bg: #1e293b;
        }
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            box-shadow: 2px 0 10px rgba(0,0,0,0.2);
        }
        .sidebar .brand {
            padding: 20px 16px;
            background: var(--dark-bg);
            border-bottom: 1px solid #334155;
        }
        .sidebar .brand h5 { color: #fff; margin: 0; font-weight: 700; font-size: 1rem; }
        .sidebar .brand small { color: #94a3b8; font-size: 0.75rem; }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 10px 20px;
            border-radius: 0;
            transition: all 0.2s;
            font-size: 0.875rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.08);
            border-left: 3px solid var(--primary);
        }
        .sidebar .nav-link i { margin-right: 8px; width: 16px; }
        .sidebar .section-label {
            color: #475569;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 16px 20px 4px;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
        }
        .content-area { padding: 24px; }
        .stat-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
        .table thead th { background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge-status { font-size: 0.7rem; padding: 4px 8px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .page-header { margin-bottom: 24px; }
        .page-header h4 { font-weight: 700; color: #0f172a; margin: 0; }
        .profit-positive { color: #16a34a; font-weight: 600; }
        .profit-negative { color: #dc2626; font-weight: 600; }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="brand">
        <h5><i class="bi bi-box-seam-fill text-primary me-2"></i>InvManager</h5>
        <small>Inventory & Accounting</small>
    </div>

    <nav class="mt-2">
        <div class="section-label">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="section-label">Inventory</div>
        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Products
        </a>
        <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
            <i class="bi bi-cart-check"></i> Sales
        </a>
        <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i> Expenses
        </a>

        <div class="section-label">Accounting</div>
        <a href="{{ route('reports.journal') }}" class="nav-link {{ request()->routeIs('reports.journal') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Journal Ledger
        </a>

        <div class="section-label">Reports</div>
        <a href="{{ route('reports.financial') }}" class="nav-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Financial Report
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="topbar d-flex align-items-center justify-content-between">
        <div>
            <span class="text-muted small">@yield('breadcrumb', 'Dashboard')</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-success">System Active</span>
            <small class="text-muted">{{ now()->format('d M Y') }}</small>
        </div>
    </div>

    <div class="content-area">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartLedger') - Invoice & Budget Management System</title>

    <!-- Bootstrap 5 CSS & Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Clean, comfortable typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 240px;
            --topbar-height: 58px;
            --primary-accent: #2563eb;
            --sidebar-bg: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-hover: #334155;
            --body-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-main: #334155;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.90rem;
            line-height: 1.5;
            background-color: var(--body-bg);
            color: var(--text-main);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Layout Structure */
        #app-wrapper {
            display: flex;
            min-height: 100vh;
        }

        #sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease;
            z-index: 1040;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        #sidebar .brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            color: #f8fafc;
            font-size: 1.08rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
            letter-spacing: -0.2px;
        }

        #sidebar .nav-list {
            padding: 0.75rem 0.65rem;
            overflow-y: auto;
            flex-grow: 1;
        }

        .nav-section-title {
            font-size: 0.72rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.6px;
            color: #64748b;
            padding: 0.65rem 0.6rem 0.3rem 0.6rem;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            color: #cbd5e1;
            border-radius: 0.375rem;
            font-size: 0.88rem;
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 0.2rem;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .nav-link-custom i {
            font-size: 1.05rem;
            margin-right: 0.65rem;
            width: 18px;
            text-align: center;
            opacity: 0.9;
        }

        .nav-link-custom:hover {
            color: #ffffff;
            background-color: var(--sidebar-hover);
        }

        .nav-link-custom.active {
            color: #ffffff;
            background-color: #2563eb;
            font-weight: 600;
        }

        #main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background-color: var(--body-bg);
        }

        /* Clean Topbar */
        #topbar {
            height: var(--topbar-height);
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        /* Dedicated Header & Breadcrumb Bar below topbar */
        .page-header-bar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
        }

        .page-header-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.3px;
            margin-bottom: 0.15rem;
        }

        .page-header-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0;
        }

        .page-breadcrumb {
            font-size: 0.82rem;
            margin-bottom: 0;
        }

        .page-breadcrumb ol {
            margin-bottom: 0;
            padding: 0;
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 0.35rem;
        }

        .page-breadcrumb li {
            display: flex;
            align-items: center;
            color: var(--text-muted);
        }

        .page-breadcrumb li a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .page-breadcrumb li a:hover {
            color: var(--primary-accent);
        }

        .page-breadcrumb li.active {
            color: #1e293b;
            font-weight: 600;
        }

        .page-breadcrumb .breadcrumb-separator {
            font-size: 0.65rem;
            color: #94a3b8;
            margin: 0 0.15rem;
        }

        .page-body {
            padding: 1.5rem;
            flex-grow: 1;
        }

        /* Clean, comfortable card design */
        .card {
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            background-color: #ffffff;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1.15rem;
        }

        .card-body {
            padding: 1.15rem;
        }

        /* Comfortable tables */
        .table {
            font-size: 0.88rem;
            margin-bottom: 0;
        }

        .table > :not(caption) > * > * {
            padding: 0.6rem 0.85rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table thead th {
            font-size: 0.76rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        /* Forms & Inputs */
        .form-control, .form-select {
            font-size: 0.88rem;
            padding: 0.45rem 0.75rem;
            border-radius: 0.375rem;
            border-color: #cbd5e1;
        }

        .form-control:focus, .form-select:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 2.5px rgba(59, 130, 246, 0.15);
        }

        .form-label {
            font-size: 0.84rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
            color: #475569;
        }

        /* Buttons */
        .btn {
            font-size: 0.88rem;
            padding: 0.42rem 0.85rem;
            border-radius: 0.375rem;
            font-weight: 500;
        }

        .btn-sm {
            font-size: 0.80rem;
            padding: 0.28rem 0.6rem;
        }

        /* Badges */
        .badge {
            font-weight: 500;
            font-size: 0.76rem;
            padding: 0.28rem 0.55rem;
            border-radius: 0.3rem;
        }

        /* KPI cards */
        .kpi-card {
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            background: #ffffff;
            padding: 1rem 1.15rem;
        }

        .kpi-title {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .kpi-value {
            font-size: 1.38rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            letter-spacing: -0.3px;
        }

        .kpi-icon {
            width: 38px;
            height: 38px;
            border-radius: 0.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        /* Pagination */
        .pagination {
            font-size: 0.84rem;
            margin-bottom: 0;
        }

        .page-link {
            padding: 0.35rem 0.7rem;
        }

        /* Responsive toggle */
        @media (max-width: 991.98px) {
            #sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                transform: translateX(-100%);
            }
            #sidebar.show {
                transform: translateX(0);
            }
            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background-color: rgba(0, 0, 0, 0.4);
                z-index: 1030;
                display: none;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }

        @media print {
            #sidebar, #topbar, .page-header-bar, .btn-print-hide, .card-header-actions, .pagination, .filter-card {
                display: none !important;
            }
            #main-content {
                margin: 0 !important;
                padding: 0 !important;
            }
            .page-body {
                padding: 0 !important;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="app-wrapper">
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

        <!-- Left Navigation Sidebar -->
        <aside id="sidebar">
            <a href="{{ route('dashboard') }}" class="brand">
                <i class="bi bi-wallet2 text-primary me-2 fs-5"></i>
                <span>Smart<span class="text-primary">Ledger</span></span>
            </a>

            <div class="nav-list">
                <div class="nav-section-title">Overview</div>
                <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>

                <div class="nav-section-title">Billing</div>
                <a href="{{ route('sales-invoices.index') }}" class="nav-link-custom {{ request()->routeIs('sales-invoices.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Sales Invoices</span>
                </a>
                <a href="{{ route('purchase-invoices.index') }}" class="nav-link-custom {{ request()->routeIs('purchase-invoices.*') ? 'active' : '' }}">
                    <i class="bi bi-bag-check"></i>
                    <span>Purchase Bills</span>
                </a>

                <div class="nav-section-title">Finance & Controls</div>
                <a href="{{ route('budgets.index') }}" class="nav-link-custom {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                    <i class="bi bi-pie-chart"></i>
                    <span>Budgets</span>
                </a>
                <a href="{{ route('expenses.index') }}" class="nav-link-custom {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i>
                    <span>Expenses</span>
                </a>
                <a href="{{ route('reports.index') }}" class="nav-link-custom {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Reports</span>
                </a>

                <div class="nav-section-title">Master Data</div>
                <a href="{{ route('customers.index') }}" class="nav-link-custom {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>
                <a href="{{ route('suppliers.index') }}" class="nav-link-custom {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    <span>Suppliers</span>
                </a>
                <a href="{{ route('products.index') }}" class="nav-link-custom {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Items & Services</span>
                </a>
            </div>

            <div class="p-3 border-top border-secondary border-opacity-25 text-center">
                <span class="text-muted" style="font-size: 0.74rem;">SmartLedger &bull; Laravel 12</span>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div id="main-content">
            <!-- Topbar (clean system actions & user profile) -->
            <header id="topbar">
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" type="button" onclick="toggleSidebar()">
                        <i class="bi bi-list fs-6"></i>
                    </button>
                    <span class="text-muted d-none d-sm-inline small">
                        <i class="bi bi-shield-check text-primary me-1"></i> Management Portal
                    </span>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted small">
                        <i class="bi bi-calendar3 me-1 text-primary"></i> {{ date('l, M d, Y') }}
                    </span>

                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border d-flex align-items-center gap-2 px-2.5 py-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle text-primary fs-6"></i>
                            <span class="text-dark fw-semibold small">{{ auth()->user()->name ?? 'Administrator' }}</span>
                            <i class="bi bi-chevron-down text-muted" style="font-size: 0.7rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-1" style="font-size: 0.85rem;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-semibold text-dark">{{ auth()->user()->name ?? 'Admin' }}</div>
                                <div class="text-muted small">{{ auth()->user()->email ?? 'admin@example.com' }}</div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1">Administrator</span>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Dedicated Header & Breadcrumb Bar below the topbar -->
            <section class="page-header-bar">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <!-- Left End: Title and Subtitle -->
                    <div>
                        <h1 class="page-header-title">@yield('page_title', 'Dashboard')</h1>
                        <p class="page-header-subtitle">@yield('page_subtitle', 'Invoice & Budget Management Platform')</p>
                    </div>

                    <!-- Right End: Breadcrumb Navigation -->
                    <div class="d-flex align-items-center">
                        <nav class="page-breadcrumb" aria-label="breadcrumb">
                            <ol>
                                <li>
                                    <a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Home</a>
                                </li>
                                @php
                                    $currentRoute = request()->route()?->getName();
                                    $breadcrumbSection = match(true) {
                                        str_starts_with((string)$currentRoute, 'sales-invoices') => ['Sales Invoices', route('sales-invoices.index')],
                                        str_starts_with((string)$currentRoute, 'purchase-invoices') => ['Purchase Bills', route('purchase-invoices.index')],
                                        str_starts_with((string)$currentRoute, 'budgets') => ['Budgets', route('budgets.index')],
                                        str_starts_with((string)$currentRoute, 'expenses') => ['Expenses', route('expenses.index')],
                                        str_starts_with((string)$currentRoute, 'reports') => ['Reports', route('reports.index')],
                                        str_starts_with((string)$currentRoute, 'customers') => ['Customers', route('customers.index')],
                                        str_starts_with((string)$currentRoute, 'suppliers') => ['Suppliers', route('suppliers.index')],
                                        str_starts_with((string)$currentRoute, 'products') => ['Items & Services', route('products.index')],
                                        default => null,
                                    };
                                @endphp
                                @if ($breadcrumbSection)
                                    <li>
                                        <span class="breadcrumb-separator"><i class="bi bi-chevron-right"></i></span>
                                        <a href="{{ $breadcrumbSection[1] }}">{{ $breadcrumbSection[0] }}</a>
                                    </li>
                                @endif
                                <li>
                                    <span class="breadcrumb-separator"><i class="bi bi-chevron-right"></i></span>
                                    <span class="active">@yield('page_title', 'Dashboard')</span>
                                </li>
                            </ol>
                        </nav>
                        @hasSection('header_actions')
                            <div class="ms-3">
                                @yield('header_actions')
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <!-- Page Body -->
            <main class="page-body">
                <x-alert />
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Confirm Delete Modal Component -->
    <x-confirm-delete />

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>

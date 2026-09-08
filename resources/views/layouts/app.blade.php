<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Business Management & Inventory System">
    <title>@yield('title', 'Dashboard') — BizManager</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
<div class="app-layout">

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">🏢</div>
            <div>
                <div class="sidebar-brand-text">{{ \App\Models\Setting::get('business_name', 'BizManager') }}</div>
                <div class="sidebar-brand-sub">{{ \App\Models\Setting::get('business_tagline', 'Management System') }}</div>
            </div>
        </div>

        <nav class="sidebar-nav">

            <!-- MAIN -->
            <div class="sidebar-section-title">Main</div>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span>
                Dashboard
            </a>

            <!-- CATALOG -->
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'staff']))
            <div class="sidebar-section-title">Catalog</div>

            <a href="{{ route('products.index') }}"
               class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <span class="icon">📦</span>
                Products
            </a>

            <a href="{{ route('categories.index') }}"
               class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <span class="icon">🗂️</span>
                Categories
            </a>
            @endif

            <!-- PARTIES -->
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <div class="sidebar-section-title">Parties</div>

            <a href="{{ route('suppliers.index') }}"
               class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <span class="icon">🏭</span>
                Suppliers
            </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'manager', 'cashier']))
            <a href="{{ route('customers.index') }}"
               class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <span class="icon">👤</span>
                Customers
            </a>
            @endif

            <!-- TRANSACTIONS -->
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <div class="sidebar-section-title">Transactions</div>

            <a href="{{ route('purchases.index') }}"
               class="sidebar-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                <span class="icon">📥</span>
                Purchases
            </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'manager', 'cashier']))
            <a href="{{ route('sales.index') }}"
               class="sidebar-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <span class="icon">📤</span>
                Sales
            </a>
            @endif

            <!-- RETURNS -->
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'cashier']))
            <div class="sidebar-section-title">Returns</div>

            <a href="{{ route('returns.sales.index') }}"
               class="sidebar-link {{ request()->routeIs('returns.sales.*') ? 'active' : '' }}">
                <span class="icon">↩️</span>
                Sale Returns
            </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <a href="{{ route('returns.purchases.index') }}"
               class="sidebar-link {{ request()->routeIs('returns.purchases.*') ? 'active' : '' }}">
                <span class="icon">🔄</span>
                Purchase Returns
            </a>
            @endif

            <!-- INVENTORY -->
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'staff']))
            <div class="sidebar-section-title">Inventory</div>

            <a href="{{ route('inventory.index') }}"
               class="sidebar-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}">
                <span class="icon">🏪</span>
                Stock Overview
            </a>

            <a href="{{ route('inventory.low-stock') }}"
               class="sidebar-link {{ request()->routeIs('inventory.low-stock') ? 'active' : '' }}">
                <span class="icon">⚠️</span>
                Low Stock
            </a>

            <a href="{{ route('inventory.movements') }}"
               class="sidebar-link {{ request()->routeIs('inventory.movements') ? 'active' : '' }}">
                <span class="icon">🔄</span>
                Stock Movements
            </a>
            @endif

            <!-- FINANCE -->
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <div class="sidebar-section-title">Finance</div>

            <a href="{{ route('payments.index') }}"
               class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <span class="icon">💰</span>
                Payments
            </a>

            <a href="{{ route('balances.suppliers') }}"
               class="sidebar-link {{ request()->routeIs('balances.suppliers') ? 'active' : '' }}">
                <span class="icon">📋</span>
                Supplier Balances
            </a>

            <a href="{{ route('balances.customers') }}"
               class="sidebar-link {{ request()->routeIs('balances.customers') ? 'active' : '' }}">
                <span class="icon">📋</span>
                Customer Balances
            </a>
            @endif

            <!-- REPORTS -->
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <div class="sidebar-section-title">Reports</div>

            <a href="{{ route('reports.sales') }}"
               class="sidebar-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                <span class="icon">📈</span>
                Sales Report
            </a>

            <a href="{{ route('reports.purchases') }}"
               class="sidebar-link {{ request()->routeIs('reports.purchases') ? 'active' : '' }}">
                <span class="icon">📉</span>
                Purchase Report
            </a>

            <a href="{{ route('reports.inventory') }}"
               class="sidebar-link {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                <span class="icon">🗃️</span>
                Inventory Report
            </a>

            <a href="{{ route('reports.profit') }}"
               class="sidebar-link {{ request()->routeIs('reports.profit') ? 'active' : '' }}">
                <span class="icon">💵</span>
                Profit Report
            </a>
            @endif

            <!-- ADMIN -->
            @if(auth()->user()->role === 'admin')
            <div class="sidebar-section-title">Administration</div>

            <a href="{{ route('users.index') }}"
               class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <span class="icon">👥</span>
                Users
            </a>

            <a href="{{ route('settings.index') }}"
               class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <span class="icon">⚙️</span>
                Settings
            </a>
            @endif

        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="min-width:0;">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">{{ auth()->user()->role }}</div>
                </div>
            </div>
        </div>

    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="hamburger" id="hamburger" aria-label="Toggle sidebar">☰</button>
                <div>
                    <div class="topbar-title">@yield('page_title', 'Dashboard')</div>
                    @hasSection('breadcrumb')
                    <div class="topbar-breadcrumb">
                        <a href="{{ route('dashboard') }}">Home</a>
                        <span>›</span>
                        @yield('breadcrumb')
                    </div>
                    @endif
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="topbar-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success" id="flash-alert">
                    <span class="alert-icon">✅</span>
                    <span>{{ session('success') }}</span>
                    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" id="flash-alert">
                    <span class="alert-icon">❌</span>
                    <span>{{ session('error') }}</span>
                    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <span class="alert-icon">⚠️</span>
                    <ul style="margin:0;padding-left:16px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

    </div>

</div>

<script>
    // Sidebar toggle for mobile
    const hamburger = document.getElementById('hamburger');
    const sidebar   = document.getElementById('sidebar');

    if (hamburger) {
        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    // Auto-dismiss flash alerts after 4 seconds
    const flashAlert = document.getElementById('flash-alert');
    if (flashAlert) {
        setTimeout(() => {
            flashAlert.style.opacity = '0';
            flashAlert.style.transition = 'opacity 0.4s';
            setTimeout(() => flashAlert.remove(), 400);
        }, 4000);
    }
</script>

@stack('scripts')
</body>
</html>

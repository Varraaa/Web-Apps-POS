<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin VarStore')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}"><span>V</span> VarStore <small>ADMIN</small></a>
            <div class="admin-profile">
                <div class="admin-avatar">AD</div>
                <div><strong>Administrator</strong><small>Store manager</small></div>
            </div>
            <p class="admin-nav-label">Workspace</p>
            <nav class="admin-nav">
                <a class="{{ request()->routeIs('admin.dashboard') ? 'admin-nav-active' : '' }}"
                    href="{{ route('admin.dashboard') }}"><span>▦</span> Overview</a>
                <a class="{{ request()->routeIs('admin.products.*') ? 'admin-nav-active' : '' }}"
                    href="{{ route('admin.products.index') }}"><span>◫</span> Products</a>
                <a class="{{ request()->routeIs('admin.orders.*') ? 'admin-nav-active' : '' }}"
                    href="{{ route('admin.orders.index') }}"><span>□</span> Orders</a>
            </nav>
            <div class="admin-sidebar-bottom">
                <form method="POST" action="{{ route('logout') }}">@csrf<button>Sign out</button></form>
            </div>
        </aside>
        <main class="admin-main">
            <header class="admin-topbar">
                <div class="admin-mobile-brand">VarStore <span>/ Admin</span></div>
                <div class="admin-top-actions"><span>{{ now()->format('d M Y') }}</span>
                    <div class="admin-avatar">AD</div>
                </div>
            </header>
            <div class="admin-content">@yield('content')</div>
        </main>
    </div>
</body>

</html>

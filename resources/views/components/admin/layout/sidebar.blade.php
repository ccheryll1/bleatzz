@php
    $user = auth()->user();
    $isManager = $user && $user->isManager();
    $isSeller = $user && $user->isSeller();
@endphp

<aside class="admin-sidebar">
    <div class="admin-sidebar-logo">
        <div class="admin-logo-box">
            <span class="admin-logo-text">BLEATZ</span>
            <span class="admin-logo-sub">ADMIN</span>
        </div>
    </div>

    <nav class="admin-sidebar-nav">
        @if($isManager)
            <div class="admin-menu-group">
                <span class="admin-menu-label">MANAGER</span>

                <a href="{{ route('manager.dashboard') }}"
                   class="admin-menu-item {{ request()->routeIs('manager.dashboard') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">▦</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('manager.users.index') }}"
                   class="admin-menu-item {{ request()->routeIs('manager.users.*') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">◎</span>
                    <span>Kelola User</span>
                </a>

                <a href="{{ route('manager.canteens.index') }}"
                   class="admin-menu-item {{ request()->routeIs('manager.canteens.*') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">▣</span>
                    <span>Kelola Kantin</span>
                </a>

                <a href="{{ route('manager.menus.index') }}"
                   class="admin-menu-item {{ request()->routeIs('manager.menus.*') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">▤</span>
                    <span>Monitoring Menu</span>
                </a>

                <a href="{{ route('manager.toppings.index') }}"
                   class="admin-menu-item {{ request()->routeIs('manager.toppings.*') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">▥</span>
                    <span>Monitoring Topping</span>
                </a>

                <a href="{{ route('manager.reports.index') }}"
                   class="admin-menu-item {{ request()->routeIs('manager.reports.*') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">₱</span>
                    <span>Laporan Keuangan</span>
                </a>
            </div>
        @endif

        @if($isSeller)
            <div class="admin-menu-group">
                <span class="admin-menu-label">SELLER</span>

                <a href="{{ route('seller.dashboard') }}"
                   class="admin-menu-item {{ request()->routeIs('seller.dashboard') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">▦</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('seller.orders.index') }}"
                   class="admin-menu-item {{ request()->routeIs('seller.orders.*') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">📦</span>
                    <span>Pesanan</span>
                </a>

                <a href="{{ route('seller.canteens.index') }}"
                   class="admin-menu-item {{ request()->routeIs('seller.canteens.index') || request()->routeIs('seller.canteens.create') || request()->routeIs('seller.canteens.edit') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">▣</span>
                    <span>Kantin Saya</span>
                </a>

                <a href="{{ route('seller.canteens.menus.index', auth()->user()->canteens()->first() ?? 0) }}"
                   class="admin-menu-item {{ request()->routeIs('seller.canteens.menus.*') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">▤</span>
                    <span>Kelola Menu</span>
                </a>

                <a href="{{ route('seller.canteens.toppings.index', auth()->user()->canteens()->first() ?? 0) }}"
                   class="admin-menu-item {{ request()->routeIs('seller.canteens.toppings.*') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">▥</span>
                    <span>Kelola Topping</span>
                </a>

                <a href="{{ route('seller.finance.index') }}"
                   class="admin-menu-item {{ request()->routeIs('seller.finance.*') || request()->routeIs('seller.canteens.finance.*') || request()->routeIs('seller.canteens.transactions.*') ? 'is-active' : '' }}">
                    <span class="admin-menu-icon">₱</span>
                    <span>Keuangan</span>
                </a>
            </div>
        @endif

        <div class="admin-menu-group admin-menu-group-bottom">
            <span class="admin-menu-label">UMUM</span>

            <a href="{{ route('home') }}"
               class="admin-menu-item">
                <span class="admin-menu-icon">←</span>
                <span>Kembali ke Landing</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="admin-menu-logout-form">
                @csrf
                <button type="submit" class="admin-menu-item admin-menu-item-btn">
                    <span class="admin-menu-icon">✕</span>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </nav>
</aside>

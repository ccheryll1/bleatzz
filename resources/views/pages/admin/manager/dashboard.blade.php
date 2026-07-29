<x-admin-layout title="Dashboard" page-title="Dashboard Manager">
    @php
        $totalBuyers = App\Models\User::where('role', 'buyer')->count();
        $totalSellers = App\Models\User::where('role', 'seller')->count();
        $totalCanteens = App\Models\Canteen::count();
        $totalMenus = App\Models\Menu::count();
    @endphp

    <div class="admin-stat-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Buyer</div>
            <div class="admin-stat-value">{{ $totalBuyers }}</div>
            <div class="admin-stat-accent"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Seller</div>
            <div class="admin-stat-value">{{ $totalSellers }}</div>
            <div class="admin-stat-accent" style="background: var(--color-cyan);"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Kantin</div>
            <div class="admin-stat-value">{{ $totalCanteens }}</div>
            <div class="admin-stat-accent" style="background: var(--color-warning);"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Menu</div>
            <div class="admin-stat-value">{{ $totalMenus }}</div>
            <div class="admin-stat-accent" style="background: var(--color-error);"></div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Selamat Datang, Manager!</h2>
        </div>
        <div class="admin-card-body">
            <p>Ini adalah halaman dashboard manager. Gunakan menu di sidebar untuk mengelola user, kantin, dan memantau menu serta topping yang dijual.</p>
        </div>
    </div>
</x-admin-layout>

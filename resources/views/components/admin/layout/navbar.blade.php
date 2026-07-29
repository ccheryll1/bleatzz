@php
    $user = auth()->user();
    $pendingOrderCount = 0;
    if ($user?->isSeller()) {
        $canteenIds = $user->canteens()->select('canteens.id')->pluck('canteens.id')->toArray();
        $pendingOrderCount = \App\Models\Transaction::whereIn(
            'canteen_id',
            $canteenIds
        )->where('status', 'pending')->count();
    }
@endphp

<header class="admin-navbar">
    <div class="admin-navbar-left">
        <h1 class="admin-page-title">
            @yield('page-title', isset($pageTitle) ? $pageTitle : 'Dashboard')
        </h1>
    </div>

    <div class="admin-navbar-right">
        @if($user?->isSeller())
            <a href="{{ route('seller.orders.index', ['status' => 'pending']) }}" class="admin-navbar-pending-badge" style="position: relative; display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; background: var(--color-gray-50); border: 2px solid var(--color-teal); border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600; color: var(--color-black); transition: all 0.2s;">
                <span style="font-size: 14px;">📬</span>
                <span>{{ $pendingOrderCount }} Pesanan</span>
                @if($pendingOrderCount > 0)
                    <span style="position: absolute; top: -6px; right: -6px; background: var(--color-error); color: white; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; border: 2px solid white;">
                        {{ min($pendingOrderCount, 9) }}{{ $pendingOrderCount > 9 ? '+' : '' }}
                    </span>
                @endif
            </a>
        @endif

        <div class="admin-user-info">
            <div class="admin-user-avatar">
                {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
            </div>
            <div class="admin-user-meta">
                <span class="admin-user-name">{{ $user?->name ?? 'Guest' }}</span>
                <span class="admin-user-role">
                    @if($user?->isManager())
                        <span class="admin-role-badge admin-role-manager">MANAGER</span>
                    @elseif($user?->isSeller())
                        <span class="admin-role-badge admin-role-seller">SELLER</span>
                    @else
                        <span class="admin-role-badge admin-role-buyer">BUYER</span>
                    @endif
                </span>
            </div>
        </div>
    </div>
</header>

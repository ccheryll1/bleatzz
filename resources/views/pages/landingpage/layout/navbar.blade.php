<nav class="navbar">
    <div class="navbar__inner">

        {{-- Logo --}}
        <a href="{{ url('/') }}" class="navbar__logo">
            <span class="navbar__logo-text">Ble<span>atz</span></span>
        </a>

        {{-- Nav Links --}}
        <ul class="navbar__links">
            <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Beranda</a></li>
            <li><a href="{{ route('canteen.index') }}" class="{{ request()->is('canteen*') ? 'active' : '' }}">Kantin</a></li>
            <li><a href="{{ route('menu.index') }}" class="{{ request()->is('menu*') ? 'active' : '' }}">Menu</a></li>
            @auth
                <li><a href="{{ route('buyer.orders.index') }}" class="{{ request()->is('buyer/orders*') ? 'active' : '' }}">Pesanan</a></li>
            @else
                <li><a href="{{ route('login') }}">Pesanan</a></li>
            @endauth
        </ul>

        {{-- Actions --}}
        <div class="navbar__actions">
            @auth
                {{-- Favorite Icon --}}
                <a href="{{ route('favorite.index') }}" class="{{ request()->is('favorite*') ? 'active' : '' }}" title="Favorit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                    </svg>
                </a>

                {{-- Cart Icon --}}
                <a href="{{ route('cart.index') }}" class="navbar__icon-btn navbar__cart-btn {{ request()->is('cart*') ? 'active' : '' }}" title="Keranjang">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4M5 13a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                    <span class="navbar__cart-badge" id="navbarCartBadge" style="display:none;">0</span>
                </a>

                {{-- Profile Dropdown --}}
                <div class="navbar__profile" id="navProfileDropdown">
                    <button type="button" class="navbar__profile-trigger" id="navProfileBtn"
                            aria-haspopup="menu" aria-expanded="false"
                            title="{{ Auth::user()->name }}">
                        <span class="navbar__profile-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"
                             style="color:#777;">
                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </button>

                    <div class="navbar__profile-menu" role="menu">
                        <div class="navbar__profile-header">
                            <div class="navbar__profile-name">{{ Auth::user()->name }}</div>
                            <div class="navbar__profile-email">@ {{ Auth::user()->username }}</div>
                            <div class="navbar__profile-role">
                                @if(Auth::user()->isManager())
                                    MANAGER
                                @elseif(Auth::user()->isSeller())
                                    SELLER
                                @else
                                    BUYER
                                @endif
                            </div>
                        </div>

                        @if(Auth::user()->isManager())
                            <a href="{{ route('manager.dashboard') }}" role="menuitem"
                               class="navbar__profile-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;">
                                    <path d="M3 0h10a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H3a3 3 0 0 1-3-3V3a3 3 0 0 1 3-3m0 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2z"/>
                                    <path d="M2 5h12v1H2zm0 2h12v1H2zm0 2h6v1H2zm0 2h12v1H2zm8 0h4v1h-4z"/>
                                </svg>
                                <span>Dashboard Manager</span>
                            </a>
                        @elseif(Auth::user()->isSeller())
                            <a href="{{ route('seller.dashboard') }}" role="menuitem"
                               class="navbar__profile-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;">
                                    <path d="M3 0h10a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H3a3 3 0 0 1-3-3V3a3 3 0 0 1 3-3m0 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2z"/>
                                    <path d="M2 5h12v1H2zm0 2h12v1H2zm0 2h6v1H2zm0 2h12v1H2zm8 0h4v1h-4z"/>
                                </svg>
                                <span>Dashboard Seller</span>
                            </a>
                        @endif

                        @if(Auth::user()->isBuyer())
                            <a href="{{ route('buyer.transactions.index') }}" role="menuitem"
                               class="navbar__profile-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;">
                                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4M5 13a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                                </svg>
                                <span>Pesanan Aktif</span>
                            </a>
                            <a href="{{ route('buyer.transactions.history') }}" role="menuitem"
                               class="navbar__profile-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z"/>
                                    <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5zM4 7.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5z"/>
                                </svg>
                                <span>Riwayat Transaksi</span>
                            </a>
                        @endif

                        <a href="{{ route('profile.edit') }}" role="menuitem"
                           class="navbar__profile-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.029 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                            </svg>
                            <span>Pengaturan Profil</span>
                        </a>

                        <div class="navbar__profile-divider"></div>

                        <form method="POST" action="{{ route('logout') }}" style="margin:0;" role="none">
                            @csrf
                            <button type="submit" role="menuitem"
                                    class="navbar__profile-item navbar__profile-item--danger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;">
                                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Favorite Icon --}}
                <a href="{{ route('login') }}" class="navbar__icon-btn" title="Favorit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                    </svg>
                </a>

                {{-- Cart Icon --}}
                <a href="{{ route('login') }}" class="navbar__icon-btn" title="Keranjang">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4M5 13a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                </a>

                {{-- Profile / Login Icon --}}
                <a href="{{ route('login') }}" class="navbar__icon-btn" title="Masuk">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.029 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                    </svg>
                </a>
            @endauth
        </div>

        {{-- Mobile toggle --}}
        <button class="navbar__toggle" id="navToggle" aria-label="Toggle menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Drawer --}}
    <div class="navbar__drawer" id="navDrawer">
        <a href="{{ url('/') }}">Beranda</a>
        <a href="{{ route('canteen.index') }}">Kantin</a>
        <a href="{{ route('menu.index') }}">Menu</a>
        @auth
            <a href="{{ route('buyer.orders.index') }}">Pesanan</a>
            @if(Auth::user()->isBuyer())
                <a href="{{ route('buyer.transactions.history') }}">Riwayat Transaksi</a>
            @endif
        @else
            <a href="{{ route('login') }}">Pesanan</a>
        @endauth
        <div class="navbar__drawer-actions">
            @auth
                <a href="{{ route('profile.edit') }}" class="navbar__btn-login">Profil</a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="navbar__btn-register" style="cursor:pointer;">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="navbar__btn-login">Masuk</a>
                <a href="{{ route('register') }}" class="navbar__btn-register">Daftar</a>
            @endauth
        </div>
    </div>
</nav>

<script>
    (function () {
        const toggle = document.getElementById('navToggle');
        const drawer = document.getElementById('navDrawer');
        if (toggle && drawer) {
            toggle.addEventListener('click', function () {
                drawer.classList.toggle('open');
            });
        }

        const profileWrap = document.getElementById('navProfileDropdown');
        const profileBtn  = document.getElementById('navProfileBtn');
        if (profileWrap && profileBtn) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                const open = profileWrap.classList.toggle('is-open');
                profileBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });

            document.addEventListener('click', function (e) {
                if (! profileWrap.contains(e.target)) {
                    profileWrap.classList.remove('is-open');
                    profileBtn.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && profileWrap.classList.contains('is-open')) {
                    profileWrap.classList.remove('is-open');
                    profileBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }
    })();
</script>

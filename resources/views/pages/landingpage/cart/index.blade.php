@extends('pages.landingpage.layout.content')

@section('title', 'Keranjang Misi — Bleatz')

@section('content')

<main class="bleatz-content">

    {{-- ── Hero Section ── --}}
    <x-landingpage.cart.hero-section />

    {{-- ── Cart Items + Summary ── --}}
    <section class="cart-section">
        <div class="cart-section__inner">

            {{-- Left: Cart items --}}
            <div class="cart-section__items">

                @if (isset($cartItems) && $cartItems->isNotEmpty())

                    {{-- ── Info Banner: Multi-Canteen Checkout ── --}}
                    @if ($canteenCount > 1)
                        <div class="admin-card" style="margin-bottom:20px; border-left:6px solid var(--color-teal);">
                            <div class="admin-card-body" style="padding:14px 18px;">
                                <div style="display:flex; align-items:flex-start; gap:12px;">
                                    <div style="flex-shrink:0; width:32px; height:32px; border-radius:50%; background:var(--color-teal); color:var(--color-black); display:flex; align-items:center; justify-content:center; font-weight:900; font-size:16px; font-family:'Courier New', monospace;">
                                        {{ $canteenCount }}
                                    </div>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:13px; font-weight:900; color:var(--color-black); text-transform:uppercase; margin-bottom:4px;">
                                            ✨ MULTI-KANTIN CHECKOUT
                                        </div>
                                        <div style="font-size:12px; color:var(--color-gray-700); font-weight:600; line-height:1.5;">
                                            Anda memesan dari <strong style="color:var(--color-teal);">{{ $canteenCount }} kantin berbeda</strong>.
                                            Saat menekan tombol <strong>AJUKAN PESANAN</strong>, sistem akan otomatis membuat
                                            <strong>{{ $canteenCount }} pesanan terpisah</strong> (1 per kantin)
                                            agar setiap penjual langsung menerima pesanan dari kantinnya.
                                            Setiap pesanan akan memiliki status pembayaran dan pelacakan terpisah.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="admin-card" style="margin-bottom:20px;">
                            <div class="admin-card-body" style="padding:12px 18px;">
                                <div style="font-size:12px; color:var(--color-gray-600); font-weight:600;">
                                    💡 Tips: Tambahkan menu dari kantin lain untuk memanfaatkan fitur multi-kantin!
                                    Cukup pilih item yang ingin dicheckout dan semua pesanan dari berbagai kantin akan langsung dikirim.
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ── Cart Items Grouped by Canteen ── --}}
                    @foreach ($groupedCartItems as $canteenId => $items)
                        @php
                            $sampleCanteen = $items->first()?->menu?->canteen;
                            $canteenName = $sampleCanteen?->name ?? 'Kantin Lainnya';
                            $canteenCode = $sampleCanteen?->canteen_code ?? '';
                            $canteenItemsSubtotal = $items->sum(fn($it) => $it->subtotal);
                            $canteenItemsCount = $items->count();
                            $canteenItemsQty = $items->sum('quantity');
                        @endphp

                        <div class="admin-card" style="margin-bottom:20px; overflow:hidden;">
                            <div style="padding:12px 18px; background:var(--color-gray-100); border-bottom:3px solid var(--color-black); display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                                <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                                    <div style="flex-shrink:0; width:28px; height:28px; border:2.5px solid var(--color-black); border-radius:4px; background:var(--color-teal); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:900; color:var(--color-black); box-shadow:3px 3px 0 var(--color-black);">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div style="min-width:0;">
                                        <div style="font-size:14px; font-weight:900; color:var(--color-black); text-transform:uppercase; font-family:'Courier New', monospace; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;">
                                            🍽️ {{ $canteenName }}
                                        </div>
                                        @if ($canteenCode)
                                            <div style="font-size:10px; color:var(--color-gray-600); font-weight:700; text-transform:uppercase; margin-top:2px;">
                                                KODE: {{ $canteenCode }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center; gap:16px; flex-shrink:0;">
                                    <div style="text-align:right;">
                                        <div style="font-size:10px; color:var(--color-gray-500); font-weight:700; text-transform:uppercase;">
                                            {{ $canteenItemsQty }} item · {{ $canteenItemsCount }} varian
                                        </div>
                                        <div style="font-size:13px; font-weight:900; color:var(--color-black);">
                                            SUB-TOTAL: Rp{{ number_format($canteenItemsSubtotal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="padding:12px 12px 4px 12px; display:flex; flex-direction:column; gap:12px;">
                                @foreach ($items as $cartItem)
                                    <x-landingpage.card.card-cart :cartItem="$cartItem" />
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                @else
                    <div class="cart-section__empty">
                        <svg class="cart-section__empty-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <p class="cart-section__empty-text">
                            KERANJANG MISI KOSONG
                        </p>
                        <p class="cart-section__empty-sub">
                            Belum ada misi yang siap dijalankan. Jelajahi menu kantin kampus dan pilih amunisi terbaik untuk misi harianmu!
                        </p>
                        <a href="{{ route('menu.index') }}" class="cart-section__empty-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                            <span>Lihat Menu</span>
                        </a>
                    </div>
                @endif

            </div>

            {{-- Right: Order Summary --}}
            <div class="cart-section__summary">
                @if ($canteenCount > 0)
                    <div class="admin-card" style="margin-bottom:16px;">
                        <div class="admin-card-body" style="padding:14px 16px;">
                            <div style="font-size:10px; font-weight:800; color:var(--color-gray-500); text-transform:uppercase; margin-bottom:6px;">
                                Ringkasan Multi-Kantin
                            </div>
                            <div style="display:flex; align-items:center; justify-content:space-between;">
                                <div style="font-size:13px; font-weight:700; color:var(--color-black);">
                                    🛒 Total Kantin Terlibat
                                </div>
                                <div style="font-size:15px; font-weight:900; color:var(--color-teal); font-family:'Courier New', monospace;">
                                    {{ $canteenCount }} KANTIN
                                </div>
                            </div>
                            @if ($canteenCount > 1)
                                <div style="margin-top:8px; padding-top:8px; border-top:1.5px dashed var(--color-gray-300); font-size:10.5px; color:var(--color-gray-600); font-weight:600; line-height:1.5;">
                                    ⚠️ Checkout sekali → {{ $canteenCount }} pesanan terpisah dibuat otomatis.
                                    Setiap kantin memproses pesanan Anda secara mandiri.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <x-landingpage.card.card-summary
                    :totalItems="$totalItems"
                    :subtotal="$subtotal"
                    :serviceFee="$serviceFee"
                    :total="$total"
                />
            </div>

        </div>
    </section>

</main>

@include('components.landingpage.modal.modal-waitconfirmation')
@include('components.landingpage.modal.modal-acceptorder')

@endsection

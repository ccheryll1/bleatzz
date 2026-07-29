{{--
    Reusable Card Summary — Dossier Summary / Ringkasan Pesanan
    ──────────────────────────────────────────────────────────
    Props:
      $totalItems — jumlah total item (quantity sum) — fallback, gak dipake kalo ada selection
      $subtotal   — total harga tanpa service fee — fallback
      $serviceFee — biaya layanan
      $total      — grand total (subtotal + service fee) — fallback
--}}

@props(['totalItems', 'subtotal', 'serviceFee', 'total'])

<aside class="card-summary" x-data="{
    selectedSubtotal: 0,
    selectedQty: 0,

    recomputeFromGlobal() {
        const map = (window.__cartSelection && window.__cartSelection.map) || {};
        let s = 0, q = 0;
        for (const id in map) {
            const it = map[id] || {};
            s += Number(it.subtotal) || 0;
            q += Number(it.qty) || 0;
        }
        this.selectedSubtotal = s;
        this.selectedQty = q;
    },

    serviceFee: {{ $serviceFee }},
    get computedSubtotal() { return this.selectedSubtotal; },
    get computedQty() { return this.selectedQty; },
    get computedTotal() {
        return this.computedQty > 0
            ? this.computedSubtotal + this.serviceFee
            : 0;
    },
    get hasSelection() { return this.computedQty > 0; },

    pad(n, len) {
        const s = String(n);
        return s.length >= len ? s : (new Array(len - s.length + 1).join('0')) + s;
    },

    fmt(num) {
        return Number(num || 0).toLocaleString('id-ID');
    },

    handleCheckout() {
        if (!this.hasSelection) {
            alert('Pilih minimal 1 item');
            return;
        }
        window.submitCheckout('{{ $total }}');
    },

    init() {
        this.recomputeFromGlobal();
        this.$watch('$store', () => {});
        window.addEventListener('cart-selection-changed', () => this.recomputeFromGlobal());
    },
}">

    {{-- Teal accent border on right + bottom (offset shadow effect) --}}
    <div class="card-summary__teal-border"></div>

    <div class="card-summary__inner">

        {{-- ── Title ── --}}
        <h2 class="card-summary__title">
            DOSSIER SUMMARY
        </h2>

        <div class="card-summary__divider card-summary__divider--solid"></div>

        {{-- ── Summary rows ── --}}
        <div class="card-summary__rows">

            <div class="card-summary__row">
                <span class="card-summary__label">TOTAL ITEMS</span>
                <span class="card-summary__value card-summary__value--num" x-text="pad(computedQty, 2)">
                    {{ str_pad(0, 2, '0', STR_PAD_LEFT) }}
                </span>
            </div>

            <div class="card-summary__row">
                <span class="card-summary__label">SUBTOTAL</span>
                <span class="card-summary__value card-summary__value--price" x-text="'RP ' + fmt(computedSubtotal)">
                    RP {{ number_format(0, 0, ',', '.') }}
                </span>
            </div>

            <div class="card-summary__row">
                <span class="card-summary__label">SERVICE FEE</span>
                <span class="card-summary__value card-summary__value--price" x-show="hasSelection" x-text="'RP ' + fmt(serviceFee)">
                    RP {{ number_format($serviceFee, 0, ',', '.') }}
                </span>
                <span class="card-summary__value card-summary__value--price" x-show="!hasSelection" x-cloak style="opacity: 0.45;">
                    RP 0
                </span>
            </div>

        </div>

        {{-- Dashed divider before TOTAL BAYAR --}}
        <div class="card-summary__divider card-summary__divider--dashed"></div>

        {{-- ── Grand total ── --}}
        <div class="card-summary__total-row">
            <span class="card-summary__total-label">TOTAL BAYAR</span>
            <span class="card-summary__total-value" x-text="'RP ' + fmt(computedTotal)">
                RP {{ number_format(0, 0, ',', '.') }}
            </span>
        </div>

        {{-- ── CTA Button ── --}}
        <div class="card-summary__cta-wrap">
            <button
                type="button"
                class="card-summary__cta-btn"
                :disabled="!hasSelection"
                @click="handleCheckout()"
            >
                <span class="card-summary__cta-text">AJUKAN PESANAN</span>
                <svg class="card-summary__cta-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </div>

        {{-- ── Confirmation note ── --}}
        <p class="card-summary__note">
            Pesanan akan diproses setelah dikonfirmasi oleh penjual.
        </p>

        {{-- ── Security footer ── --}}
        <div class="card-summary__security">
            <svg class="card-summary__security-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
            <span class="card-summary__security-text">TRANSAKSI TERENKRIPSI & AMAN</span>
        </div>

    </div>

</aside>

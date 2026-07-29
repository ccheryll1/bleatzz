{{--
    Reusable Card Cart — Item di Keranjang
    ─────────────────────────────────────
    Props:
      $cartItem — App\Models\CartItem instance (with ->menu, ->toppings, ->quantity, ->notes, ->subtotal)
--}}

@props(['cartItem'])

@php
    $menu = $cartItem->menu;
    $toppings = $cartItem->toppings;
    $toppingTotal = $toppings->sum('price');
    $menuPrice = $menu ? $menu->price : 0;
    $unitPrice = $menuPrice + $toppingTotal;
@endphp

<article class="card-cart" data-cart-item-id="{{ $cartItem->id }}" x-data="{
    id: {{ $cartItem->id }},
    selected: false,
    qty: {{ $cartItem->quantity }},
    unitPrice: {{ $unitPrice }},
    subtotal: {{ $cartItem->subtotal }},
    notes: @js($cartItem->notes ?? ''),

    editingNotes: false,
    draftNotes: '',

    loadingQty: false,
    loadingNotes: false,

    menuId: {{ $menu ? $menu->id : 0 }},
    toppingIds: @js($toppings->pluck('id')->toArray()),

    getCsrf() {
        return document.querySelector('meta[name=csrf-token]')?.content
            || document.querySelector('input[name=_token]')?.value || '';
    },

    formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    },

    recomputeSubtotal() {
        this.subtotal = this.unitPrice * this.qty;
    },

    syncSelection() {
        window.__cartSelection = window.__cartSelection || { map: {}, evt: 0 };
        if (this.selected) {
            window.__cartSelection.map[this.id] = { subtotal: this.subtotal, qty: this.qty };
        } else {
            delete window.__cartSelection.map[this.id];
        }
        window.__cartSelection.evt++;
        window.dispatchEvent(new CustomEvent('cart-selection-changed', {
            detail: { selection: { ...window.__cartSelection.map } }
        }));
    },

    async updateQtyViaAjax() {
        if (this.loadingQty) return;
        this.loadingQty = true;

        const fd = new URLSearchParams();
        fd.append('_method', 'PATCH');
        fd.append('quantity', this.qty);
        fd.append('notes', this.notes || '');
        this.toppingIds.forEach(id => fd.append('toppings[]', id));

        try {
            const res = await fetch('{{ route('cart.update', $cartItem) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrf(),
                },
                body: fd.toString(),
            });

            const data = await res.json();
            if (res.ok && data.success) {
                this.recomputeSubtotal();
                this.syncSelection();
                window.dispatchEvent(new CustomEvent('cart-count-updated', {
                    detail: { cart_count: data.cart_count ?? 0 }
                }));
                window.dispatchEvent(new CustomEvent('cart-item-updated', {
                    detail: { id: this.id, qty: this.qty, subtotal: this.subtotal }
                }));
            } else {
                    alert(data.message || 'Gagal memperbarui quantity.');
                    throw new Error(data.message || 'update failed');
                }
        } catch (e) {
            console.error(e);
            // rollback
            this.qty = {{ $cartItem->quantity }};
            this.recomputeSubtotal();
        } finally {
            this.loadingQty = false;
        }
    },

    qtyDec() {
        if (this.loadingQty || this.qty <= 1) return;
        this.qty--;
        this.updateQtyViaAjax();
    },

    qtyInc() {
        if (this.loadingQty || this.qty >= 99) return;
        this.qty++;
        this.updateQtyViaAjax();
    },

    startEditNotes() {
        this.draftNotes = this.notes;
        this.editingNotes = true;
        this.$nextTick(() => {
            const ta = this.$root.querySelector('[data-notes-edit-ta]');
            if (ta) { ta.focus(); ta.select(); }
        });
    },

    cancelEditNotes() {
        this.editingNotes = false;
        this.draftNotes = '';
    },

    async saveNotes() {
        if (this.loadingNotes) return;
        const trimmed = this.draftNotes.trim();
        if (trimmed === this.notes) {
            this.cancelEditNotes();
            return;
        }
        this.loadingNotes = true;

        const fd = new URLSearchParams();
        fd.append('_method', 'PATCH');
        fd.append('quantity', this.qty);
        fd.append('notes', trimmed);
        this.toppingIds.forEach(id => fd.append('toppings[]', id));

        try {
            const res = await fetch('{{ route('cart.update', $cartItem) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrf(),
                },
                body: fd.toString(),
            });

            const data = await res.json();
            if (res.ok && data.success) {
                this.notes = trimmed;
                this.editingNotes = false;
                this.draftNotes = '';
            } else {
                alert(data.message || 'Gagal memperbarui catatan.');
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan jaringan. Coba lagi.');
        } finally {
            this.loadingNotes = false;
        }
    },

    init() {
        this.$watch('selected', () => this.syncSelection());
        this.$watch('qty', () => { this.recomputeSubtotal(); });
    }
}">

    {{-- ── Right edge dotted accent strip ── --}}
    <div class="card-cart__right-strip"></div>

    {{-- ── Checkmark button (Left side) ── --}}
    <div class="card-cart__check-wrap">
        <button
            type="button"
            class="card-cart__check-btn"
            :class="{ 'is-checked': selected }"
            @click="selected = !selected"
            aria-label="Select item"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </button>
    </div>

    {{-- ── Photo area ── --}}
    <div class="card-cart__photo-wrap">
        <div class="card-cart__photo-frame">
            @if ($menu && $menu->photo)
                <img
                    src="{{ asset('storage/' . $menu->photo) }}"
                    alt="{{ $menu->name }}"
                    class="card-cart__photo"
                    loading="lazy"
                >
            @else
                <div class="card-cart__photo card-cart__photo--placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.2-1.1-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
                    </svg>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Right detail column ── --}}
    <div class="card-cart__detail">

        {{-- ── Header row: Title + Delete button ── --}}
        <div class="card-cart__header">
            <h3 class="card-cart__title">{{ $menu ? strtoupper($menu->name) : 'MENU' }}</h3>

            <form
                method="POST"
                action="{{ route('cart.destroy', $cartItem) }}"
                class="card-cart__delete-form"
                onsubmit="return confirm('Hapus item ini dari keranjang?');"
            >
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="card-cart__delete-btn"
                    aria-label="Remove from cart"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </form>
        </div>

        {{-- ── Topping badges ── --}}
        @if ($toppings && $toppings->isNotEmpty())
            <ul class="card-cart__toppings">
                @foreach ($toppings as $topping)
                    <li class="card-cart__topping-badge">
                        <span class="card-cart__topping-plus">+</span>
                        <span class="card-cart__topping-name">{{ strtoupper($topping->name) }}</span>
                        <span class="card-cart__topping-sep">-</span>
                        <span class="card-cart__topping-price">RP {{ number_format($topping->price, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- ── Notes section ── --}}
        <div class="card-cart__notes">
            <div class="card-cart__notes-header">
                <div class="card-cart__notes-label">CATATAN MISI:</div>
                <button
                    type="button"
                    class="card-cart__notes-edit-btn"
                    x-show="!editingNotes"
                    @click="startEditNotes()"
                    aria-label="Edit notes"
                    title="Edit catatan"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"></path>
                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                    </svg>
                    <span>EDIT</span>
                </button>
            </div>

            {{-- Read-only notes view --}}
            <div class="card-cart__notes-box" x-show="!editingNotes">
                <p class="card-cart__notes-text"
                   x-show="notes"
                   x-text='"\"" + notes + "\"'>@if($cartItem->notes)"{{ $cartItem->notes }}"@endif</p>
                <p class="card-cart__notes-text card-cart__notes-text--empty"
                   x-show="!notes">@if(!$cartItem->notes)— Tidak ada catatan khusus —@endif</p>
            </div>

            {{-- Edit notes view --}}
            <div class="card-cart__notes-edit" x-show="editingNotes" style="display: none;">
                <textarea
                    data-notes-edit-ta
                    class="card-cart__notes-textarea"
                    x-model="draftNotes"
                    placeholder="Tulis catatan untuk penjual..."
                    rows="3"
                    maxlength="500"
                    :disabled="loadingNotes"
                ></textarea>
                <div class="card-cart__notes-actions">
                    <button
                        type="button"
                        class="card-cart__notes-save-btn"
                        @click="saveNotes()"
                        :disabled="loadingNotes"
                    >
                        <span x-show="!loadingNotes">SIMPAN</span>
                        <span x-show="loadingNotes" x-cloak>...</span>
                    </button>
                    <button
                        type="button"
                        class="card-cart__notes-cancel-btn"
                        @click="cancelEditNotes()"
                        :disabled="loadingNotes"
                    >
                        BATAL
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Bottom row: Quantity stepper + Subtotal ── --}}
        <div class="card-cart__bottom">

            {{-- Quantity stepper --}}
            <div class="card-cart__stepper" :class="{ 'is-loading': loadingQty }">
                <button
                    type="button"
                    class="card-cart__stepper-btn"
                    @click="qtyDec()"
                    :disabled="loadingQty || qty <= 1"
                    aria-label="Decrease quantity"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>

                <span class="card-cart__stepper-value" x-text="qty">{{ $cartItem->quantity }}</span>

                <button
                    type="button"
                    class="card-cart__stepper-btn"
                    @click="qtyInc()"
                    :disabled="loadingQty || qty >= 99"
                    aria-label="Increase quantity"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>
            </div>

            {{-- Subtotal --}}
            <div class="card-cart__subtotal">
                <span class="card-cart__subtotal-label">SUBTOTAL</span>
                <span class="card-cart__subtotal-price" x-text="formatRp(subtotal)">
                    Rp {{ number_format($cartItem->subtotal, 0, ',', '.') }}
                </span>
            </div>

        </div>

    </div>

</article>

{{--
    Modal Cart — Tambah Menu ke Keranjang (Neobrutalist Style)
    ──────────────────────────────────────────────────────────
    Usage:
      Place once anywhere in the page layout (singleton).
      Trigger by dispatching a browser event:

        window.dispatchEvent(new CustomEvent('open-cart-modal', {
          detail: {
            menu_id: 1,
            name: 'Tactical Burger',
            description: 'Double beef patty...',
            price: 25000,
            photo: 'storage/menus/xxx.jpg',  // relative path
            canteen_name: 'Kantin Tactical',
            toppings: [
              { id: 1, name: 'Extra Cheese', price: 5000 },
              { id: 2, name: 'Caramelized Onions', price: 3000 },
            ]
          }
        }))
--}}

<div
    x-data="{
        show: false,

        menu: {
            menu_id: null,
            name: '',
            description: '',
            price: 0,
            photo: '',
            canteen_name: '',
        },

        toppings: [],          // array of {id, name, price, qty}
        notes: '',
        qty: 1,

        resetState() {
            this.toppings = [];
            this.notes = '';
            this.qty = 1;
        },

        close() {
            this.show = false;
            document.body.classList.remove('overflow-hidden');
        },

        getToppingTotal() {
            return this.toppings.reduce((sum, t) => sum + (t.price * t.qty), 0);
        },

        getGrandTotal() {
            return (this.menu.price + this.getToppingTotal()) * this.qty;
        },

        formatRp(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        },

        toppingInc(i) { if (this.toppings[i].qty < 1) this.toppings[i].qty++; },
        toppingDec(i) {
            if (this.toppings[i].qty > 0) this.toppings[i].qty--;
        },
        qtyInc() { this.qty++; },
        qtyDec() {
            if (this.qty > 1) this.qty--;
        },

        submitting: false,
        async submitForm() {
            if (this.submitting || !this.menu.menu_id) return;
            this.submitting = true;

            const selectedToppingIds = this.toppings
                .filter(t => t.qty > 0)
                .map(t => t.id);

            const formData = new URLSearchParams();
            formData.append('menu_id', this.menu.menu_id);
            formData.append('quantity', this.qty);
            formData.append('notes', this.notes || '');
            selectedToppingIds.forEach(id => formData.append('toppings[]', id));

            const token = document.querySelector('meta[name=csrf-token]')?.content
                || document.querySelector('input[name=_token]')?.value;

            try {
                const res = await fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                    },
                    body: formData.toString(),
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    window.dispatchEvent(new CustomEvent('cart-count-updated', {
                        detail: { cart_count: data.cart_count || 0 }
                    }));
                    this.close();
                } else {
                    alert(data.message || 'Gagal menambahkan ke keranjang.');
                }
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan jaringan. Coba lagi.');
            } finally {
                this.submitting = false;
            }
        },
    }"

    x-on:open-cart-modal.window="
        resetState();
        menu.menu_id      = $event.detail.menu_id;
        menu.name         = $event.detail.name;
        menu.description  = $event.detail.description || '';
        menu.price        = Number($event.detail.price);
        menu.photo        = $event.detail.photo || '';
        menu.canteen_name = $event.detail.canteen_name || 'Kantin';
        toppings          = ($event.detail.toppings || []).map(t => ({
                                id: t.id,
                                name: t.name,
                                price: Number(t.price),
                                qty: 0
                            }));
        show = true;
        document.body.classList.add('overflow-hidden');
    "

    x-on:keydown.escape.window="close()"

    x-show="show"
    x-cloak
    x-transition:enter="cart-modal-fade-enter"
    x-transition:enter-start="cart-modal-fade-enter-start"
    x-transition:enter-end="cart-modal-fade-enter-end"
    x-transition:leave="cart-modal-fade-leave"
    x-transition:leave-start="cart-modal-fade-leave-start"
    x-transition:leave-end="cart-modal-fade-leave-end"

    class="cart-modal-overlay"
    @click.self="close()"
>
    <div
        class="cart-modal"
        x-on:click.stop
        x-show="show"
        x-transition:enter="cart-modal-pop-enter"
        x-transition:enter-start="cart-modal-pop-enter-start"
        x-transition:enter-end="cart-modal-pop-enter-end"
        x-transition:leave="cart-modal-pop-leave"
        x-transition:leave-start="cart-modal-pop-leave-start"
        x-transition:leave-end="cart-modal-pop-leave-end"
    >
        {{-- ─────────────────── LEFT: PHOTO COLUMN ─────────────────── --}}
        <div class="cart-modal__photo-col">
            <div class="cart-modal__photo-frame">
                <template x-if="menu.photo">
                    <img
                        :src="'/' + menu.photo"
                        :alt="menu.name"
                        class="cart-modal__photo"
                    >
                </template>
                <template x-if="!menu.photo">
                    <div class="cart-modal__photo cart-modal__photo--placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.2-1.1-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
                        </svg>
                    </div>
                </template>
            </div>
        </div>

        {{-- ─────────────────── RIGHT: DETAIL COLUMN ─────────────────── --}}
        <div class="cart-modal__detail-col">

            <form method="POST" action="#" id="cart-modal-form" class="cart-modal__form" @submit.prevent="submitForm">
                @csrf
                <input type="hidden" name="menu_id" x-bind:value="menu.menu_id">
                <input type="hidden" name="quantity" x-bind:value="qty">
                <input type="hidden" name="notes" x-bind:value="notes">

                {{-- ── HEADER ── --}}
                <div class="cart-modal__section cart-modal__header">
                    <button
                        type="button"
                        class="cart-modal__close-btn"
                        @click="close()"
                        aria-label="Close modal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>

                    <span class="cart-modal__canteen-label" x-text="menu.canteen_name"></span>
                    <h2 class="cart-modal__title" x-text="menu.name"></h2>
                    <p class="cart-modal__desc" x-text="menu.description" x-show="menu.description"></p>
                </div>

                {{-- ── ADD-ONS / TOPPINGS ── --}}
                <div class="cart-modal__section cart-modal__toppings" x-show="toppings.length > 0">
                    <h3 class="cart-modal__section-title cart-modal__section-title--accent">
                        ADD-ONS / TOPPINGS
                    </h3>

                    <ul class="cart-modal__topping-list">
                        <template x-for="(topping, idx) in toppings" :key="topping.id">
                            <li class="cart-modal__topping-item">
                                <label class="cart-modal__topping-info">
                                    <span class="cart-modal__topping-name" x-text="topping.name"></span>
                                    <span class="cart-modal__topping-price" x-text="formatRp(topping.price)"></span>
                                </label>

                                <input type="hidden" name="topping_ids[]" x-bind:value="topping.id" x-bind:disabled="topping.qty === 0">
                                <input type="hidden" name="topping_qtys[]" x-bind:value="topping.qty" x-bind:disabled="topping.qty === 0">

                                <div class="cart-modal__stepper">
                                    <button type="button" class="cart-modal__stepper-btn" @click="toppingDec(idx)">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                    <span class="cart-modal__stepper-value" x-text="topping.qty"></span>
                                    <button type="button" class="cart-modal__stepper-btn" @click="toppingInc(idx)">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>

                {{-- ── MISSION NOTES ── --}}
                <div class="cart-modal__section cart-modal__notes">
                    <h3 class="cart-modal__section-title">
                        MISSION NOTES / CATATAN
                    </h3>
                    <textarea
                        name="notes_textarea"
                        x-model="notes"
                        class="cart-modal__textarea"
                        placeholder="e.g. No pickles, extra spicy..."
                        rows="4"
                    ></textarea>
                </div>

                {{-- ── QUANTITY + TOTAL ── --}}
                <div class="cart-modal__section cart-modal__qty">
                    <div class="cart-modal__qty-row">
                        <span class="cart-modal__qty-label">QUANTITY</span>
                        <div class="cart-modal__stepper cart-modal__stepper--lg">
                            <button type="button" class="cart-modal__stepper-btn" @click="qtyDec()">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </button>
                            <span class="cart-modal__stepper-value" x-text="qty"></span>
                            <button type="button" class="cart-modal__stepper-btn" @click="qtyInc()">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="cart-modal__total-row" x-show="menu.price > 0">
                        <span class="cart-modal__total-label">TOTAL</span>
                        <span class="cart-modal__total-price" x-text="formatRp(getGrandTotal())"></span>
                    </div>
                </div>

                {{-- ── SUBMIT CTA ── --}}
                <div class="cart-modal__section cart-modal__cta">
                    <button
                        type="submit"
                        class="cart-modal__submit-btn"
                        x-bind:disabled="submitting"
                    >
                        <span x-show="!submitting">CONFIRM ORDER / TAMBAHKAN KE KERANJANG</span>
                        <span x-show="submitting" x-cloak>MENYIMPAN...</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
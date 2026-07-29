{{--
    Order Card — Status pesanan dengan detail items
    ──────────────────────────────────────────────────
    Props:
      $transaction — Transaction model instance
--}}

@props(['transaction'])

<div class="card-order">
    
    {{-- ── Header (Order Code + Date + Status Badge) ── --}}
    <div class="card-order__header">
        <div class="card-order__code-wrap">
            <span class="card-order__code">{{ $transaction->transaction_code }}</span>
            <span class="card-order__date">{{ $transaction->created_at->format('d M Y • H:i') }}</span>
        </div>
        
        <span class="card-order__status-badge card-order__status--{{ $transaction->status }}">
            @if ($transaction->isPending())
                ⏳ MENUNGGU
            @elseif ($transaction->isAccepted())
                ✓ DITERIMA
            @elseif ($transaction->isPaid())
                💳 DIBAYAR
            @elseif ($transaction->isProcessing())
                👨‍🍳 DIPROSES
            @elseif ($transaction->isReady())
                📦 SIAP
            @elseif ($transaction->isDone())
                ✓ SELESAI
            @elseif ($transaction->isCancelled())
                ✗ DIBATALKAN
            @elseif ($transaction->isRejected())
                ✗ DITOLAK
            @endif
        </span>
    </div>

    {{-- ── Canteen Name ── --}}
    <div class="card-order__canteen">
        <span class="card-order__canteen-label">KANTIN</span>
        <span class="card-order__canteen-name">{{ $transaction->canteen->name ?? '-' }}</span>
    </div>

    {{-- ── Items Summary ── --}}
    <div class="card-order__items-summary">
        @foreach ($transaction->orderItems as $item)
            <div class="card-order__item-line">
                <span class="card-order__item-qty">{{ $item->quantity }}x</span>
                <span class="card-order__item-name">{{ $item->menu_name }}</span>
                @if ($item->toppings->isNotEmpty())
                    <span class="card-order__item-toppings">({{ $item->toppings->pluck('topping_name')->join(', ') }})</span>
                @endif
                <span class="card-order__item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    {{-- ── Total Price ── --}}
    <div class="card-order__footer">
        <span class="card-order__total-label">TOTAL AMUNISI:</span>
        <span class="card-order__total-price">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
    </div>

    {{-- ── Action Buttons ── --}}
    <div class="card-order__actions">
        @if ($transaction->isReviewable())
            <a href="{{ route('buyer.transactions.show', $transaction) }}" class="card-order__action-btn card-order__action-btn--primary">
                REVIEW
            </a>
        @else
            <a href="{{ route('buyer.transactions.show', $transaction) }}" class="card-order__action-btn card-order__action-btn--secondary">
                DETAIL
            </a>
        @endif

        @if ($transaction->isReady())
            <form method="POST" action="{{ route('buyer.transactions.confirm', $transaction) }}" style="display: inline;">
                @csrf
                <button type="submit" class="card-order__action-btn card-order__action-btn--success">
                    PESANAN LAGI
                </button>
            </form>
        @endif
    </div>

</div>

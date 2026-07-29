@extends('pages.landingpage.layout.content')

@section('title', 'Riwayat Transaksi')

@section('content')
<div style="min-height: 100vh; background: var(--color-gray-50);">
    <!-- Header -->
    <div style="background: var(--color-white); border-bottom: 3px solid var(--color-black); padding: 24px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 900; color: var(--color-black); font-family: 'Courier New', monospace;">
                📜 RIWAYAT TRANSAKSI
            </h1>
            <p style="margin: 0; font-size: 13px; color: var(--color-gray-600); font-weight: 600;">
                Semua catatan pesanan yang sudah selesai, dibatalkan, atau ditolak
            </p>
        </div>
    </div>

    <!-- Content -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 24px;">
        <!-- Tabs Filter -->
        <div style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
            <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}"
               class="admin-btn @if(!request('status') || request('status') === '') is-active @endif"
               style="@if(!request('status') || request('status') === '') background: var(--color-teal); color: var(--color-black); @else background: var(--color-gray-200); color: var(--color-black); @endif">
                📋 Semua Riwayat
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'done']) }}"
               class="admin-btn @if(request('status') === 'done') is-active @endif"
               style="@if(request('status') === 'done') background: var(--color-teal); color: var(--color-black); @else background: var(--color-gray-200); color: var(--color-black); @endif">
                ✓ Selesai
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'cancelled']) }}"
               class="admin-btn @if(request('status') === 'cancelled') is-active @endif"
               style="@if(request('status') === 'cancelled') background: var(--color-teal); color: var(--color-black); @else background: var(--color-gray-200); color: var(--color-black); @endif">
                ✕ Dibatalkan
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}"
               class="admin-btn @if(request('status') === 'rejected') is-active @endif"
               style="@if(request('status') === 'rejected') background: var(--color-teal); color: var(--color-black); @else background: var(--color-gray-200); color: var(--color-black); @endif">
                ✕ Ditolak
            </a>
        </div>

        <!-- Transactions List -->
        @if($transactions->isEmpty())
            <div class="admin-card">
                <div class="admin-card-body" style="text-align: center; padding: 40px 20px;">
                    <p style="margin: 0 0 8px 0; font-size: 14px; color: var(--color-gray-500);">
                        Tidak ada riwayat transaksi {{ request('status') ? 'dengan status ' . request('status') : '' }}
                    </p>
                    <p style="margin: 0; font-size: 12px; color: var(--color-gray-400);">
                        Riwayat pesanan Anda yang sudah selesai / dibatalkan / ditolak akan muncul di sini.
                    </p>
                </div>
            </div>
        @else
            <div style="display: grid; gap: 16px;">
                @foreach($transactions as $transaction)
                    <div class="admin-card" data-transaction-id="{{ $transaction->id }}" data-status="{{ $transaction->status }}">
                        <div style="padding: 16px; display: grid; grid-template-columns: 1fr auto; gap: 16px; align-items: center; border-bottom: 2px dashed var(--color-gray-300);">
                            <div>
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px; flex-wrap: wrap;">
                                    <h3 style="margin: 0; font-size: 14px; font-weight: 800; color: var(--color-black);">
                                        {{ $transaction->transaction_code }}
                                    </h3>
                                    <span style="display: inline-block; padding: 4px 8px; background: 
                                        @switch($transaction->status)
                                            @case('done') #E8F5E9; color: var(--color-teal); @break
                                            @case('cancelled') #FFF5F5; color: var(--color-error); @break
                                            @case('rejected') #FFF5F5; color: var(--color-error); @break
                                            @default #F5F5F5; color: var(--color-gray-600); @break
                                        @endswitch
                                    ; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                                        @switch($transaction->status)
                                            @case('done') ✓ Selesai @break
                                            @case('cancelled') ✕ Dibatalkan @break
                                            @case('rejected') ✕ Ditolak @break
                                            @default {{ $transaction->status }} @break
                                        @endswitch
                                    </span>
                                </div>
                                <p style="margin: 0; font-size: 12px; color: var(--color-gray-600);">
                                    <strong>{{ $transaction->canteen?->name ?? '-' }}</strong> | 
                                    {{ $transaction->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 16px; font-weight: 900; color: var(--color-black);">
                                    Rp{{ number_format($transaction->total_price, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div style="padding: 12px 16px; background: var(--color-gray-50); border-bottom: 2px dashed var(--color-gray-300); max-height: 100px; overflow-y: auto;">
                            @foreach($transaction->orderItems as $item)
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; margin-bottom: 6px; padding-bottom: 6px; border-bottom: 1px solid var(--color-gray-300);">
                                    <div>
                                        <strong>{{ $item->menu_name }}</strong>
                                        @if($item->toppings->isNotEmpty())
                                            <div style="font-size: 10px; color: var(--color-gray-500); margin-top: 2px;">
                                                + {{ $item->toppings->pluck('topping_name')->join(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div style="text-align: right;">
                                        {{ $item->quantity }}x @ Rp{{ number_format($item->menu_price, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Review / Rejection info & Actions -->
                        <div style="padding: 12px 16px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                            <div style="flex: 1; min-width: 0;">
                                @if($transaction->isDone() && $transaction->review)
                                    <div style="display:flex; align-items:center; gap:8px; padding:6px 10px; background:#FFF8E1; border-radius:4px; border:2px solid #FFD54F;">
                                        <span style="font-size:11px; font-weight:800; color:#F57C00; text-transform:uppercase;">⭐ Ulasan Anda:</span>
                                        @for($i = 1; $i <= 5; $i++)
                                            <span style="font-size:14px; color:{{ $i <= $transaction->review->rating ? '#FFB300' : '#E0E0E0' }};">★</span>
                                        @endfor
                                        @if($transaction->review->comment)
                                            <span style="font-size:11px; color:#795548; font-style:italic; max-width:300px; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; margin-left:6px;">
                                                "{{ $transaction->review->comment }}"
                                            </span>
                                        @endif
                                    </div>
                                @elseif($transaction->isDone() && !$transaction->review)
                                    <span style="background: #FFF3E0; color: #E65100; padding: 6px 10px; border-radius: 4px; border:2px solid #FFB74D; font-size:11px; font-weight:800; text-transform:uppercase;">
                                        ⭐ Belum Memberikan Ulasan
                                    </span>
                                @elseif($transaction->isRejected())
                                    <span style="background: #FFF5F5; color: var(--color-error); padding: 6px 10px; border-radius: 4px; font-weight: 600; font-size:11px;">
                                        Ditolak: {{ $transaction->rejection_reason }}
                                    </span>
                                @elseif($transaction->isCancelled())
                                    <span style="background: #FFF5F5; color: var(--color-error); padding: 6px 10px; border-radius: 4px; font-weight: 600; font-size:11px;">
                                        Dibatalkan: {{ $transaction->cancellation_reason ?? '—' }}
                                    </span>
                                @endif
                            </div>

                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                @if($transaction->isDone() && !$transaction->review)
                                    <a href="{{ route('buyer.transactions.show', $transaction) }}#review-btn" class="admin-btn admin-btn-sm" style="background: var(--color-teal); color: var(--color-black);">
                                        ⭐ Beri Review →
                                    </a>
                                @endif

                                <a href="{{ route('buyer.transactions.show', $transaction) }}" class="admin-btn admin-btn-sm" style="background: var(--color-gray-200); color: var(--color-black);">
                                    Lihat Detail →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div style="margin-top: 24px;">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

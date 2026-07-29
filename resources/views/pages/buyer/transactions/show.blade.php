@extends('pages.landingpage.layout.content')

@section('title', 'Detail Pesanan - ' . $transaction->transaction_code)

@section('content')
<div style="min-height: 100vh; background: var(--color-gray-50); padding: 24px 0;">
    <!-- Header -->
    <div style="background: var(--color-white); border-bottom: 3px solid var(--color-black); padding: 24px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 24px;">
            <a href="{{ route('buyer.transactions.index') }}" style="display: inline-flex; align-items: center; gap: 6px; color: var(--color-teal); text-decoration: none; font-size: 12px; font-weight: 700; margin-bottom: 12px;">
                ← Kembali ke Riwayat
            </a>
            <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 900; color: var(--color-black); font-family: 'Courier New', monospace;">
                {{ $transaction->transaction_code }}
            </h1>
            <p style="margin: 0; font-size: 13px; color: var(--color-gray-600); font-weight: 600;">
                Pesanan dari {{ $transaction->canteen?->canteen_name ?? '-' }}
            </p>
        </div>
    </div>

    <!-- Content -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 24px;">
        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 20px;">
            <!-- Main Content -->
            <div>
                <!-- Order Info -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">INFORMASI PESANAN</h3>
                        <span style="display: inline-block; padding: 4px 8px; background: 
                            @switch($transaction->status)
                                @case('pending') #FFF8E1; color: #F57C00; @break
                                @case('accepted') #E8F5E9; color: var(--color-teal); @break
                                @case('paid') #E3F2FD; color: #1565C0; @break
                                @case('processing') #F3E5F5; color: #7B1FA2; @break
                                @case('ready') #E0F2F1; color: #00796B; @break
                                @case('done') #E8F5E9; color: var(--color-teal); @break
                                @case('cancelled') #FFF5F5; color: var(--color-error); @break
                                @case('rejected') #FFF5F5; color: var(--color-error); @break
                                @default #F5F5F5; color: var(--color-gray-600); @break
                            @endswitch
                        ; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                            @switch($transaction->status)
                                @case('pending') ⏳ Menunggu @break
                                @case('accepted') ✓ Diterima @break
                                @case('paid') 💳 Dibayar @break
                                @case('processing') ▤ Diproses @break
                                @case('ready') 📦 Siap @break
                                @case('done') ✓ Selesai @break
                                @case('cancelled') ✕ Dibatalkan @break
                                @case('rejected') ✕ Ditolak @break
                                @default {{ $transaction->status }} @break
                            @endswitch
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-gray-600); margin-bottom: 4px;">Kantin</div>
                                <div style="font-size: 14px; font-weight: 700; color: var(--color-black);">
                                    {{ $transaction->canteen?->canteen_name ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-gray-600); margin-bottom: 4px;">Tanggal Pesanan</div>
                                <div style="font-size: 14px; font-weight: 700; color: var(--color-black);">
                                    {{ $transaction->created_at->format('d M Y') }}
                                </div>
                                <div style="font-size: 12px; color: var(--color-gray-600); margin-top: 4px;">
                                    {{ $transaction->created_at->format('H:i') }} WIB
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Info -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">STATUS PESANAN</h3>
                    </div>
                    <div class="admin-card-body">
                        @if($transaction->isPending())
                            <div style="background: #FFF8E1; border: 2px solid #F57C00; border-radius: 4px; padding: 12px; margin-bottom: 12px;">
                                <strong style="color: #F57C00;">⏳ Menunggu Konfirmasi Penjual</strong>
                                <p style="margin: 8px 0 0 0; font-size: 12px; color: #F57C00;">
                                    Pesanan Anda sedang menunggu konfirmasi dari penjual. Anda akan mendapat notifikasi segera setelah pesanan diterima.
                                </p>
                            </div>
                        @elseif($transaction->isRejected())
                            <div style="background: #FFF5F5; border: 2px solid var(--color-error); border-radius: 4px; padding: 12px; margin-bottom: 12px;">
                                <strong style="color: var(--color-error);">✕ Pesanan Ditolak</strong>
                                <p style="margin: 8px 0 0 0; font-size: 12px; color: var(--color-error);">
                                    <strong>Alasan:</strong> {{ $transaction->rejection_reason }}
                                </p>
                                <a href="{{ route('buyer.transactions.index') }}" style="display: inline-block; margin-top: 8px; color: var(--color-error); text-decoration: underline; font-size: 12px;">
                                    Buat pesanan baru →
                                </a>
                            </div>
                        @elseif($transaction->isPaid())
                            <div style="background: #E3F2FD; border: 2px solid #1565C0; border-radius: 4px; padding: 12px; margin-bottom: 12px;">
                                <strong style="color: #1565C0;">💳 Pembayaran Berhasil</strong>
                                <p style="margin: 8px 0 0 0; font-size: 12px; color: #1565C0;">
                                    Pembayaran Anda telah diterima. Penjual sedang menyiapkan pesanan.
                                </p>
                            </div>
                        @elseif($transaction->isAccepted())
                            <div style="background: #E8F5E9; border: 2px solid var(--color-teal); border-radius: 4px; padding: 12px; margin-bottom: 12px;">
                                <strong style="color: var(--color-teal);">✓ Pesanan Diterima - Lanjut ke Pembayaran</strong>
                                <p style="margin: 8px 0 0 0; font-size: 12px; color: var(--color-teal);">
                                    Penjual telah menerima pesanan Anda. Silakan lanjutkan ke pembayaran untuk menyelesaikan transaksi.
                                </p>
                                @if(!$transaction->payment || !$transaction->payment->isPaid())
                                    <button type="button" onclick="initiatePayment({{ $transaction->id }})" class="admin-btn admin-btn-sm" style="background: var(--color-teal); color: var(--color-black); margin-top: 8px;">
                                        💳 Bayar Sekarang
                                    </button>
                                @else
                                    <div style="font-size: 12px; color: var(--color-gray-600); margin-top: 8px;">
                                        ✓ Pembayaran sudah berhasil
                                    </div>
                                @endif
                            </div>
                        @elseif($transaction->isReady())
                            <div style="background: #E0F2F1; border: 2px solid #00796B; border-radius: 4px; padding: 12px; margin-bottom: 12px;">
                                <strong style="color: #00796B;">📦 Pesanan Siap Diambil!</strong>
                                <p style="margin: 8px 0 0 0; font-size: 12px; color: #00796B;">
                                    Pesanan Anda sudah siap di {{ $transaction->canteen?->name ?? 'kantin' }}. Silakan ambil sesuai dengan kode pesanan di atas.
                                </p>
                                <form method="POST" action="{{ route('buyer.transactions.confirm', $transaction) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="admin-btn admin-btn-sm" style="background: var(--color-teal); color: var(--color-black); margin-top: 8px;">
                                        ✓ Konfirmasi Ambil
                                    </button>
                                </form>
                            </div>
                        @elseif($transaction->isDone())
                            <div style="background: #E8F5E9; border: 2px solid var(--color-teal); border-radius: 4px; padding: 12px; margin-bottom: 12px;">
                                <strong style="color: var(--color-teal);">✓ Pesanan Selesai</strong>
                                <p style="margin: 8px 0 0 0; font-size: 12px; color: var(--color-teal);">
                                    Pesanan telah selesai. Terima kasih telah berbelanja!
                                </p>
                                @if($transaction->isReviewable())
                                    <button id="review-btn" type="button" onclick="openReviewModal()" class="admin-btn admin-btn-sm" style="background: var(--color-teal); color: var(--color-black); margin-top: 12px; display: inline-block;">
                                        ⭐ Berikan Review
                                    </button>
                                @elseif($transaction->review)
                                    <div style="margin-top: 12px; padding: 12px; background: var(--color-white); border: 2px solid var(--color-gray-300); border-radius: 4px;">
                                        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: var(--color-gray-600); margin-bottom: 6px;">
                                            Ulasan Anda
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span style="font-size: 18px; color: {{ $i <= $transaction->review->rating ? '#FFB300' : '#E0E0E0' }};">★</span>
                                            @endfor
                                            <span style="font-size: 12px; color: var(--color-gray-600);">
                                                ({{ $transaction->review->created_at->format('d M Y') }})
                                            </span>
                                        </div>
                                        @if($transaction->review->comment)
                                            <p style="margin: 0; font-size: 12px; color: var(--color-black); line-height: 1.5;">
                                                {{ $transaction->review->comment }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Order Items -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">ITEM PESANAN</h3>
                    </div>
                    <div style="padding: 0;">
                        @foreach($transaction->orderItems as $item)
                            <div style="padding: 12px 16px; border-bottom: 1px solid var(--color-gray-300); display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-black); margin-bottom: 4px;">
                                        {{ $item->menu_name }}
                                    </div>
                                    @if($item->toppings->isNotEmpty())
                                        <div style="font-size: 11px; color: var(--color-gray-600); margin-bottom: 4px;">
                                            <strong>Topping:</strong> {{ $item->toppings->pluck('topping_name')->join(', ') }}
                                        </div>
                                    @endif
                                    @if($item->notes)
                                        <div style="font-size: 11px; color: var(--color-gray-600); font-style: italic;">
                                            <strong>Catatan:</strong> {{ $item->notes }}
                                        </div>
                                    @endif
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 12px; color: var(--color-gray-600); margin-bottom: 4px;">
                                        {{ $item->quantity }}x @ Rp{{ number_format($item->menu_price, 0, ',', '.') }}
                                    </div>
                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-black);">
                                        Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Payment Info (jika sudah paid) -->
                @if($transaction->payment)
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">INFORMASI PEMBAYARAN</h3>
                        </div>
                        <div class="admin-card-body">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div>
                                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-gray-600); margin-bottom: 4px;">Metode Pembayaran</div>
                                    <div style="font-size: 14px; font-weight: 700; color: var(--color-black);">
                                        {{ $transaction->payment->payment_method ?? '-' }}
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-gray-600); margin-bottom: 4px;">Tanggal Pembayaran</div>
                                    <div style="font-size: 14px; font-weight: 700; color: var(--color-black);">
                                        {{ $transaction->payment->paid_at?->format('d M Y H:i') ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Summary -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">RINGKASAN</h3>
                    </div>
                    <div class="admin-card-body">
                        @php
                            $subtotal = $transaction->orderItems->sum('subtotal');
                            $serviceFee = 5000;
                            $total = $transaction->total_price;
                        @endphp
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid var(--color-gray-300);">
                            <span style="color: var(--color-gray-600);">Subtotal</span>
                            <span style="font-weight: 700; color: var(--color-black);">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px dashed var(--color-gray-300);">
                            <span style="color: var(--color-gray-600);">Service Fee</span>
                            <span style="font-weight: 700; color: var(--color-black);">Rp{{ number_format($serviceFee, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
                            <span style="font-weight: 700; color: var(--color-black);">TOTAL</span>
                            <span style="font-weight: 900; color: var(--color-teal); font-size: 16px;">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.landingpage.modal.modal-waitconfirmation')
@include('components.landingpage.modal.modal-acceptorder')

<!-- Review Modal -->
<div id="reviewModal" class="review-modal-overlay" onclick="if(event.target.id==='reviewModal') closeReviewModal()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:99999; align-items:center; justify-content:center; padding:20px;">
  <div class="review-modal" onclick="event.stopPropagation()" style="background:var(--color-white); border:3px solid var(--color-black); border-radius:8px; box-shadow:8px 8px 0 var(--color-black); width:100%; max-width:480px;">
    <div style="padding:16px 20px; border-bottom:3px solid var(--color-black); display:flex; align-items:center; justify-content:space-between;">
      <h3 style="margin:0; font-size:18px; font-weight:900; color:var(--color-black); font-family:'Courier New', monospace;">BERIKAN ULASAN</h3>
      <button type="button" onclick="closeReviewModal()" style="background:transparent; border:none; font-size:22px; font-weight:900; cursor:pointer; color:var(--color-gray-600); line-height:1;">✕</button>
    </div>
    <form method="POST" action="{{ route('buyer.transactions.review.store', $transaction) }}" style="margin:0;">
      @csrf
      <div style="padding:20px;">
        <!-- Rating Stars -->
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--color-gray-600); margin-bottom:8px;">Penilaian</label>
          <div id="reviewStarContainer" style="display:flex; gap:8px; align-items:center;">
            @for($i = 1; $i <= 5; $i++)
              <button type="button" class="review-star-btn" data-value="{{ $i }}" onclick="setReviewRating({{ $i }})" style="background:none; border:none; padding:0; font-size:36px; cursor:pointer; color:#E0E0E0; line-height:1; transition:transform 0.1s;">
                ★
              </button>
            @endfor
            <span id="reviewRatingLabel" style="margin-left:12px; font-size:12px; font-weight:700; color:var(--color-gray-500);">Pilih rating</span>
          </div>
          <input type="hidden" name="rating" id="reviewRatingInput" value="">
          @error('rating')
            <div style="font-size:11px; color:var(--color-error); font-weight:600; margin-top:6px;">{{ $message }}</div>
          @enderror
        </div>

        <!-- Comment -->
        <div style="margin-bottom:4px;">
          <label style="display:block; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--color-gray-600); margin-bottom:8px;">Komentar <span style="color:var(--color-gray-400); font-weight:500;">(opsional)</span></label>
          <textarea name="comment" id="reviewCommentInput" rows="4" maxlength="500" placeholder="Ceritakan pengalaman Anda membeli di kantin ini..." style="width:100%; padding:10px 12px; font-size:13px; font-family:inherit; border:2.5px solid var(--color-gray-400); border-radius:4px; background:var(--color-white); color:var(--color-black); resize:vertical; outline:none; box-sizing:border-box;" onfocus="this.style.borderColor='var(--color-teal)'" onblur="this.style.borderColor='var(--color-gray-400)'"></textarea>
          <div style="text-align:right; margin-top:4px; font-size:10px; color:var(--color-gray-500);"><span id="reviewCharCount">0</span>/500</div>
          @error('comment')
            <div style="font-size:11px; color:var(--color-error); font-weight:600; margin-top:4px;">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div style="padding:14px 20px; border-top:2px dashed var(--color-gray-300); display:flex; gap:10px; justify-content:flex-end;">
        <button type="button" onclick="closeReviewModal()" class="admin-btn admin-btn-sm" style="background:var(--color-gray-200); color:var(--color-black);">
          Batal
        </button>
        <button type="submit" id="reviewSubmitBtn" class="admin-btn admin-btn-sm" style="background:var(--color-teal); color:var(--color-black);" disabled>
          ⭐ Kirim Ulasan
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  // ── Review Modal + Rating Logic ─────────────────────────────────────────
  var currentReviewRating = 0;

  function openReviewModal() {
    var modal = document.getElementById('reviewModal');
    if (modal) {
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
  }

  function closeReviewModal() {
    var modal = document.getElementById('reviewModal');
    if (modal) {
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }
  }

  function setReviewRating(value) {
    currentReviewRating = value;
    var label = '';
    switch(value) {
      case 1: label = 'Sangat Buruk'; break;
      case 2: label = 'Buruk'; break;
      case 3: label = 'Cukup'; break;
      case 4: label = 'Baik'; break;
      case 5: label = 'Sangat Baik'; break;
    }
    document.querySelectorAll('.review-star-btn').forEach(function(btn) {
      var v = parseInt(btn.getAttribute('data-value'));
      btn.style.color = (v <= value) ? '#FFB300' : '#E0E0E0';
      btn.style.transform = (v === value) ? 'scale(1.15)' : 'scale(1)';
    });
    document.getElementById('reviewRatingInput').value = value;
    document.getElementById('reviewRatingLabel').textContent = label;
    document.getElementById('reviewRatingLabel').style.color = '#FFB300';
    document.getElementById('reviewRatingLabel').style.fontWeight = '800';
    document.getElementById('reviewSubmitBtn').disabled = false;
  }

  // Comment char counter
  document.addEventListener('DOMContentLoaded', function() {
    var commentInput = document.getElementById('reviewCommentInput');
    var charCount = document.getElementById('reviewCharCount');
    if (commentInput && charCount) {
      commentInput.addEventListener('input', function() {
        charCount.textContent = commentInput.value.length;
      });
    }

    // Auto-open modal jika ada error review (redirect back with errors)
    @if($errors->has('rating') || $errors->has('comment'))
      openReviewModal();
      var prevRating = {{ old('rating') ? old('rating') : 'null' }};
      if (prevRating) setReviewRating(prevRating);
      var prevComment = @json(old('comment') ?? '');
      if (prevComment && commentInput) {
        commentInput.value = prevComment;
        charCount.textContent = prevComment.length;
      }
    @endif
  });
</script>

<!-- Midtrans Snap Script -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
var PAYMENT_POLL_MAX_ATTEMPTS = 150;
var PAYMENT_POLL_INTERVAL = 2000;

document.addEventListener('DOMContentLoaded', function() {
  var transactionId = {{ $transaction->id }};
  var paymentStatus = @json($transaction->payment->status ?? null);
  var transactionStatus = @json($transaction->status);

  if ((paymentStatus === 'pending' || !paymentStatus) && (transactionStatus === 'accepted' || transactionStatus === 'pending')) {
    if ({{ $transaction->payment && $transaction->payment->snap_token ? 'true' : 'false' }}) {
      pollPaymentStatus(transactionId, 0, true);
    }
  }
});

async function initiatePayment(transactionId) {
  try {
    const button = event.target;
    button.disabled = true;
    button.textContent = 'Memproses...';

    const response = await fetch('/buyer/transactions/' + transactionId + '/payment/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    });

    if (!response.ok) {
      const error = await response.json();
      showError(error.message || 'Gagal membuat token pembayaran');
      button.disabled = false;
      button.textContent = '💳 Bayar Sekarang';
      return;
    }

    const data = await response.json();
    const snapToken = data.snap_token;

    snap.pay(snapToken, {
      onSuccess: async function(result) {
        showSuccess('Pembayaran berhasil! Menyesuaikan status...');
        try {
          await fetch('/buyer/transactions/' + transactionId + '/payment/status');
          setTimeout(function() { location.reload(); }, 1500);
        } catch (e) {
          setTimeout(function() { location.reload(); }, 1500);
        }
      },
      onPending: function(result) {
        showInfo('Menunggu pembayaran Anda selesai diproses...');
        pollPaymentStatus(transactionId);
      },
      onError: function(result) {
        showError('Pembayaran gagal. Silakan coba lagi.');
        button.disabled = false;
        button.textContent = '💳 Bayar Sekarang';
      },
      onClose: function() {
        pollPaymentStatus(transactionId);
      }
    });
  } catch (error) {
    showError('Terjadi kesalahan: ' + error.message);
    button.disabled = false;
    button.textContent = '💳 Bayar Sekarang';
  }
}

async function pollPaymentStatus(transactionId, attempts, silent) {
  if (!attempts) attempts = 0;
  var maxAttempts = PAYMENT_POLL_MAX_ATTEMPTS;

  if (attempts >= maxAttempts) {
    if (!silent) {
      showInfo('Status pembayaran masih dalam proses. Silakan refresh halaman nanti.');
    }
    return;
  }

  setTimeout(async function() {
    try {
      const response = await fetch('/buyer/transactions/' + transactionId + '/payment/status');
      const data = await response.json();

      if (data.status === 'paid') {
        showSuccess('Pembayaran Anda telah diterima!');
        setTimeout(function() { location.reload(); }, 1500);
      } else if (data.status === 'failed') {
        showError('Pembayaran gagal atau expired. Silakan coba lagi.');
      } else {
        pollPaymentStatus(transactionId, attempts + 1, silent);
      }
    } catch (error) {
      pollPaymentStatus(transactionId, attempts + 1, silent);
    }
  }, PAYMENT_POLL_INTERVAL);
}

function showSuccess(message) {
  var alert = document.createElement('div');
  alert.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #E8F5E9; border: 2px solid var(--color-teal); color: var(--color-teal); padding: 12px 16px; border-radius: 4px; font-weight: 700; z-index: 9999;';
  alert.textContent = '✓ ' + message;
  document.body.appendChild(alert);
  setTimeout(function() { alert.remove(); }, 4000);
}

function showError(message) {
  var alert = document.createElement('div');
  alert.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #FFF5F5; border: 2px solid var(--color-error); color: var(--color-error); padding: 12px 16px; border-radius: 4px; font-weight: 700; z-index: 9999;';
  alert.textContent = '✕ ' + message;
  document.body.appendChild(alert);
  setTimeout(function() { alert.remove(); }, 4000);
}

function showInfo(message) {
  var alert = document.createElement('div');
  alert.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #E3F2FD; border: 2px solid #1565C0; color: #1565C0; padding: 12px 16px; border-radius: 4px; font-weight: 700; z-index: 9999;';
  alert.textContent = 'ℹ ' + message;
  document.body.appendChild(alert);
  setTimeout(function() { alert.remove(); }, 4000);
}
</script>
@endsection

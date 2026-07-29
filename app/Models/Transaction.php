<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'canteen_id',
        'transaction_code',
        'status',
        'total_price',
        'rejection_reason',
        'cancellation_reason',
        'cancel_requested_at',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_price'         => 'decimal:2',
            'cancel_requested_at' => 'datetime',
            'confirmed_at'        => 'datetime',
        ];
    }

    // ─── Status constants ─────────────────────────────────────────────────────

    const STATUS_PENDING     = 'pending';
    const STATUS_ACCEPTED    = 'accepted';
    const STATUS_PAID        = 'paid';
    const STATUS_PROCESSING  = 'processing';
    const STATUS_READY       = 'ready';
    const STATUS_DONE        = 'done';
    const STATUS_CANCELLED   = 'cancelled';
    const STATUS_REJECTED    = 'rejected';

    // ─── Relations ───────────────────────────────────────────────────────────

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(orderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    // ─── Status helpers ───────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isReady(): bool
    {
        return $this->status === self::STATUS_READY;
    }

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // Apakah pembeli boleh batalin tanpa persetujuan penjual?
    public function isCancellableByBuyerFreely(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Apakah pembatalan butuh persetujuan penjual?
    public function isCancellableWithSellerApproval(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    // Apakah pembeli boleh kasih ulasan?
    public function isReviewable(): bool
    {
        return $this->status === self::STATUS_DONE && $this->review === null;
    }

    // Apakah sudah ada permintaan batal dari pembeli?
    public function hasCancelRequest(): bool
    {
        return $this->cancel_requested_at !== null;
    }

    // ─── Code generator ──────────────────────────────────────────────────────

    
     // Generate kode transaksi unik.
     // Format: BLZ-YYYYMMDD-XXXXX (random 5 digit)
     
    public static function generateCode(): string
    {
        do {
            $code = 'BLZ-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        } while (self::where('transaction_code', $code)->exists());

        return $code;
    }
}
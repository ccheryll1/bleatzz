<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'payment_method',
        'amount',
        'status',
        'snap_token',
        'paid_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'paid_at'     => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    // ─── Status constants ─────────────────────────────────────────────────────

    const STATUS_PENDING  = 'pending';
    const STATUS_PAID     = 'paid';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_FAILED   = 'failed';

    // ─── Relations ───────────────────────────────────────────────────────────

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    // Apakah pembayaran ini bisa di-refund?
    public function isRefundable(): bool
    {
        return $this->status === self::STATUS_PAID
            && $this->midtrans_transaction_id !== null;
    }
}
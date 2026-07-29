<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemTopping extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_item_id',
        'topping_id',
        'topping_name',
        'topping_price',
    ];

    protected function casts(): array
    {
        return [
            'topping_price' => 'decimal:2',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    // Relasi ke topping asli (opsional, untuk referensi)
    public function topping(): BelongsTo
    {
        return $this->belongsTo(Topping::class);
    }
}

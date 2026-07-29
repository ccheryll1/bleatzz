<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class orderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'menu_id',
        'menu_name',
        'menu_price',
        'quantity',
        'notes',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'menu_price' => 'decimal:2',
            'subtotal'   => 'decimal:2',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi ke menu asli (untuk cek ketersediaan, bukan untuk harga)
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    // Snapshot topping yang dipilih saat order 
    public function toppings(): HasMany
    {
        return $this->hasMany(orderItemTopping::class);
    }
}
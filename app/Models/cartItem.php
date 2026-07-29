<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class cartItem extends Model
{
    protected $fillable = [
        'user_id',
        'menu_id',
        'quantity',
        'notes',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    // Topping yang dipilih untuk item keranjang ini
    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(Topping::class, 'cart_item_toppings');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    // Harga subtotal item ini termasuk topping
    public function getSubtotalAttribute(): float
    {
        $toppingTotal = $this->toppings->sum('price');

        return ($this->menu->price + $toppingTotal) * $this->quantity;
    }
}
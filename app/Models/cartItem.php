<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'menu_id',
        'quantity',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(Topping::class, 'cart_item_toppings')
            ->withTimestamps();
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    /**
     * Subtotal = (harga menu + sum harga topping) x quantity
     */
    public function getSubtotalAttribute(): float|int
    {
        $menuPrice = (float) ($this->menu?->price ?? 0);
        $toppingPrice = (float) $this->toppings->sum('price');

        return ($menuPrice + $toppingPrice) * (int) $this->quantity;
    }

    /**
     * Total harga toppings untuk item ini (bisa dipakai untuk tampilan breakdown)
     */
    public function getToppingTotalAttribute(): float|int
    {
        return (float) $this->toppings->sum('price');
    }
}

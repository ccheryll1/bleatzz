<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'canteen_id',
        'name',
        'description',
        'price',
        'photo',
        'category',
        'stock_type',
        'stock_qty',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price'        => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }

    // Topping yang bisa dipilih untuk menu ini
    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(Topping::class, 'menu_toppings');
    }

    // User yang memfavoritkan menu ini
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(orderItem::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Apakah menu ini masih bisa dipesan?
     * Mempertimbangkan is_available dan stok jika stock_type = counted.
     */
    public function isOrderable(): bool
    {
        if (! $this->is_available) {
            return false;
        }

        if ($this->stock_type === 'counted' && $this->stock_qty <= 0) {
            return false;
        }

        return true;
    }
}
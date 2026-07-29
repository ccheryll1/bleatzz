<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topping extends Model
{
    use HasFactory;

    protected $fillable = [
        'canteen_id',
        'name',
        'price',
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

    // Menu yang menggunakan topping ini
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_toppings');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Canteen extends Model
{
    use HasFactory;

    protected $fillable = [
        'canteen_name',
        'description',
        'photo',
        'is_open',
        'estimated_time_min',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    // Penjual yang mengelola kantin ini
    public function sellers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sellers');
    }

    public function seller(): HasOne
    {
        return $this->hasOne(Seller::class);
    }

    public function syncSeller(int $userId): Seller
    {
        $seller = $this->seller()->first();

        if ($seller) {
            $seller->user_id = $userId;
            $seller->save();

            return $seller;
        }

        return $this->seller()->create([
            'user_id' => $userId,
        ]);
    }

    public function clearSeller(): void
    {
        $this->seller()->delete();
    }

    // Jadwal operasional per hari
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    // Menu yang tersedia di kantin ini
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    // Topping yang tersedia di kantin ini
    public function toppings(): HasMany
    {
        return $this->hasMany(Topping::class);
    }

    // Semua transaksi yang masuk ke kantin ini
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Ulasan untuk kantin ini
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    // Rata-rata rating kantin
    public function averageRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Menu yang masih available (is_available = true).
     * Gunakan query builder untuk hasil yang akurat. Jika relasi sudah eager-load,
     * Laravel otomatis gunakan in-memory collection tanpa nembak query lagi.
     */
    public function availableMenus()
    {
        // Jika relasi 'menus' sudah eager-load, Laravel bakal filter dari memory collection
        // tanpa nembak query tambahan. Jika belum, dia nembak query tapi dengan WHERE clause yang tepat.
        return $this->menus()
            ->where('is_available', true)
            ->get();
    }

    /**
     * Kisaran harga (termurah - termahal) dari menu yang available.
     * Return null kalau kantin ini belum punya menu sama sekali.
     *
     * @return array{min: float, max: float}|null
     */
    public function priceRange(): ?array
    {
        $prices = $this->availableMenus()->pluck('price');

        if ($prices->isEmpty()) {
            return null;
        }

        return [
            'min' => (float) $prices->min(),
            'max' => (float) $prices->max(),
        ];
    }

    // Versi siap tampil dari priceRange(), contoh: "Rp5.000 - Rp25.000"
    public function priceRangeLabel(): string
    {
        $range = $this->priceRange();

        if (! $range) {
            return 'Belum ada menu';
        }

        if ($range['min'] === $range['max']) {
            return 'Rp' . number_format($range['min'], 0, ',', '.');
        }

        return 'Rp' . number_format($range['min'], 0, ',', '.')
            . ' - Rp' . number_format($range['max'], 0, ',', '.');
    }

    // Daftar kategori menu yang tersedia di kantin ini (food/drink/snack), tanpa duplikat
    public function availableCategories(): array
    {
        return $this->availableMenus()
            ->pluck('category')
            ->unique()
            ->values()
            ->all();
    }
}
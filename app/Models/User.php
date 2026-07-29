<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPasswordTrait;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'fcm_token',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ─── Role helpers ────────────────────────────────────────────────────────

    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    // ─── Relations (Seller) ──────────────────────────────────────────────────

    // Kantin yang dikelola user ini (sebagai penjual)
    public function canteens(): BelongsToMany
    {
        return $this->belongsToMany(Canteen::class, 'sellers');
    }

    // ─── Relations (Buyer) ───────────────────────────────────────────────────

    // Semua transaksi milik buyer ini
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    // Menu yang difavoritkan
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'favorites')->withTimestamps();
    }

    // Item di keranjang belanja 
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Ulasan yang pernah ditulis
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'buyer_id');
    }
}
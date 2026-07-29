<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Seller extends Model
{
    protected $fillable = [
        'canteen_id',
        'user_id',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $seller): void {
            if ($seller->user_id) {
                $user = User::find($seller->user_id);

                if ($user && ! $user->isSeller()) {
                    throw ValidationException::withMessages([
                        'user_id' => ['User yang dipilih harus memiliki role seller.'],
                    ]);
                }
            }

            if ($seller->canteen_id) {
                $query = self::query()->where('canteen_id', $seller->canteen_id);

                if ($seller->exists) {
                    $query->whereKeyNot($seller->getKey());
                }

                if ($query->exists()) {
                    throw ValidationException::withMessages([
                        'canteen_id' => ['Kantin ini sudah diurus oleh seller lain.'],
                    ]);
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }
}
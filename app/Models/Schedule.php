<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'canteen_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    protected function casts(): array
    {
        return [
            'is_closed' => 'boolean',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────
    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }

    
    // ─── Helpers ─────────────────────────────────────────────────────────────
    public function getDayNameAttribute(): string
    {
        return ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$this->day_of_week];
    }

    
    // Cek apakah kantin seharusnya buka sekarang berdasarkan jadwal ini.
    // Dipanggil oleh scheduler untuk update is_open di tabel canteens.
     
    public function isCurrentlyOpen(): bool
    {
        if ($this->is_closed) {
            return false;
        }

        if (empty($this->open_time) || empty($this->close_time)) {
            return false;
        }

        $now = now()->format('H:i:s');

        return $now >= $this->open_time && $now <= $this->close_time;
    }
}
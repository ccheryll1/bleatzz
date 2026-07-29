<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Transaction;

class OrderReadyNotification extends Notification
{
    use Queueable;

    public function __construct(public Transaction $transaction)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'transaction_id'   => $this->transaction->id,
            'transaction_code' => $this->transaction->transaction_code,
            'canteen_name'     => $this->transaction->canteen?->canteen_name,
            'message'          => 'Pesanan siap diambil! Pesanan Anda sudah siap di ' . ($this->transaction->canteen?->canteen_name ?? 'kantin') . '.',
        ];
    }
}

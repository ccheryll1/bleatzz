<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Transaction;

class CancelRequestedNotification extends Notification
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
            'buyer_name'       => $this->transaction->buyer?->name,
            'reason'           => $this->transaction->cancellation_reason,
            'message'          => 'Buyer meminta pembatalan pesanan: ' . ($this->transaction->cancellation_reason ?? 'Tanpa keterangan'),
        ];
    }
}

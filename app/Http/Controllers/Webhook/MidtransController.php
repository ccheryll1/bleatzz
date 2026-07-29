<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use App\Notifications\PaymentSuccessNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    /**
     * Handle notifikasi pembayaran dari Midtrans.
     * Route ini harus dikecualikan dari CSRF middleware.
     *
     * Midtrans akan POST ke: /webhook/midtrans
     */
    public function handle(Request $request): Response
    {
        $payload = $request->all();
        
        \Log::info('Midtrans webhook received', ['payload' => $payload]);

        // Verifikasi signature key dari Midtrans
        $signatureKey = hash('sha512',
            $payload['order_id'] .
            $payload['status_code'] .
            $payload['gross_amount'] .
            config('services.midtrans.server_key')
        );

        if ($signatureKey !== $payload['signature_key']) {
            \Log::warning('Midtrans webhook: signature tidak valid', [
                'expected' => $signatureKey,
                'received' => $payload['signature_key'] ?? 'none',
                'payload' => $payload
            ]);
            return response('Unauthorized', 401);
        }

        $payment = Payment::where('midtrans_order_id', $payload['order_id'])->first();

        if (! $payment) {
            \Log::warning('Midtrans webhook: payment tidak ditemukan', ['order_id' => $payload['order_id']]);
            return response('Not Found', 404);
        }

        $transactionStatus = $payload['transaction_status'];
        $fraudStatus       = $payload['fraud_status'] ?? null;

        \Log::info('Midtrans webhook processing', [
            'order_id' => $payload['order_id'],
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus
        ]);

        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $this->markAsPaid($payment, $payload);
        } elseif ($transactionStatus === 'settlement') {
            $this->markAsPaid($payment, $payload);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $this->markAsFailed($payment);
        } elseif ($transactionStatus === 'refund') {
            $this->markAsRefunded($payment);
        }

        return response('OK', 200);
    }

    private function markAsPaid(Payment $payment, array $payload): void
    {
        \Log::info('Processing payment as paid', [
            'payment_id' => $payment->id,
            'order_id' => $payload['order_id'],
            'transaction_id' => $payload['transaction_id']
        ]);

        $payment->update([
            'midtrans_transaction_id' => $payload['transaction_id'],
            'payment_method'          => $payload['payment_type'],
            'status'                  => Payment::STATUS_PAID,
            'paid_at'                 => now(),
        ]);

        $payment->transaction->update(['status' => Transaction::STATUS_PAID]);

        \Log::info('Payment and transaction updated', [
            'payment_status' => $payment->status,
            'transaction_status' => $payment->transaction->status
        ]);

        // Notifikasi sync ke penjual bahwa pembayaran sudah masuk
        foreach ($payment->transaction->canteen->sellers as $seller) {
            $seller->notifyNow(new PaymentSuccessNotification($payment->transaction));
        }

        Log::info('Midtrans webhook: pembayaran berhasil', ['order_id' => $payload['order_id']]);
    }

    private function markAsFailed(Payment $payment): void
    {
        $payment->update(['status' => Payment::STATUS_FAILED]);

        // Kembalikan status transaksi ke accepted agar buyer bisa coba bayar lagi
        $payment->transaction->update(['status' => Transaction::STATUS_ACCEPTED]);

        Log::info('Midtrans webhook: pembayaran gagal/expired', ['payment_id' => $payment->id]);
    }

    private function markAsRefunded(Payment $payment): void
    {
        $payment->update([
            'status'      => Payment::STATUS_REFUNDED,
            'refunded_at' => now(),
        ]);

        Log::info('Midtrans webhook: refund berhasil', ['payment_id' => $payment->id]);
    }
}
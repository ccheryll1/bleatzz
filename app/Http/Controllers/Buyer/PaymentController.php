<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    /**
     * Buat Snap Token Midtrans dan simpan ke tabel payments.
     * Dipanggil saat buyer mau bayar setelah penjual acc pesanan.
     */
    public function create(Transaction $transaction): JsonResponse|RedirectResponse
    {
        return $this->_createSnapToken($transaction);
    }

    /**
     * Check status pembayaran dari Midtrans.
     * Dipanggil dari frontend untuk polling status pembayaran.
     * Jika masih pending, akan query Midtrans API langsung.
     */
    public function checkStatus(Transaction $transaction): JsonResponse
    {
        $this->authorize('view', $transaction);

        if (! $transaction->payment) {
            return response()->json(['status' => 'no_payment'], 404);
        }

        $payment = $transaction->payment;

        // Jika masih pending, cek langsung ke Midtrans
        if ($payment->status === Payment::STATUS_PENDING) {
            try {
                \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
                \Midtrans\Config::$isSanitized  = true;

                $midtransStatus = \Midtrans\Transaction::status($payment->midtrans_order_id);

                // Handle berdasarkan transaction status dari Midtrans
                if (isset($midtransStatus->transaction_status)) {
                    $transactionStatus = $midtransStatus->transaction_status;
                    $fraudStatus = $midtransStatus->fraud_status ?? null;

                    if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
                        $this->_markAsPaid($payment, $midtransStatus);
                    } elseif ($transactionStatus === 'settlement') {
                        $this->_markAsPaid($payment, $midtransStatus);
                    } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                        $this->_markAsFailed($payment);
                    }

                    $payment->refresh();
                }
            } catch (\Exception $e) {
                \Log::warning('Midtrans checkStatus error', [
                    'order_id' => $payment->midtrans_order_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'status' => $payment->status,
            'payment_method' => $payment->payment_method,
            'paid_at' => $payment->paid_at,
            'transaction_status' => $transaction->status,
        ]);
    }

    /**
     * Callback handler saat user kembali dari Midtrans Snap.
     * Bisa success atau error dari user perspective.
     */
    public function callback(Transaction $transaction): JsonResponse|RedirectResponse
    {
        $this->authorize('pay', $transaction);

        if (! $transaction->payment) {
            return back()->with('error', 'Pembayaran tidak ditemukan');
        }

        $payment = $transaction->payment;

        // Jika payment sudah berhasil, langsung return success
        if ($payment->isPaid()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil!',
                'transaction_id' => $transaction->id,
            ]);
        }

        // Jika masih pending, cek ke Midtrans dan update status
        if ($payment->status === Payment::STATUS_PENDING) {
            \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
            \Midtrans\Config::$isSanitized  = true;

            try {
                $midtransStatus = \Midtrans\Transaction::status($payment->midtrans_order_id);

                if (isset($midtransStatus->transaction_status)) {
                    $transactionStatus = $midtransStatus->transaction_status;
                    $fraudStatus = $midtransStatus->fraud_status ?? null;

                    if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
                        $this->_markAsPaid($payment, $midtransStatus);
                    } elseif ($transactionStatus === 'settlement') {
                        $this->_markAsPaid($payment, $midtransStatus);
                    } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                        $this->_markAsFailed($payment);
                    }
                }

                $payment->refresh();
            } catch (\Exception $e) {
                \Log::warning('Midtrans callback error', [
                    'order_id' => $payment->midtrans_order_id,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'status' => 'pending',
                    'message' => 'Status masih dalam proses, tunggu sebentar...',
                ], 202);
            }
        }

        return response()->json([
            'status' => $payment->status,
            'message' => match ($payment->status) {
                Payment::STATUS_PAID => 'Pembayaran berhasil!',
                Payment::STATUS_FAILED => 'Pembayaran gagal atau expired. Silakan coba lagi.',
                Payment::STATUS_REFUNDED => 'Pembayaran telah direfund.',
                default => 'Status pembayaran sedang diproses.',
            },
            'transaction_id' => $transaction->id,
        ]);
    }

    /**
     * Private method untuk buat Snap Token.
     */
    private function _createSnapToken(Transaction $transaction): JsonResponse|RedirectResponse
    {
        // Check authorization manually
        if (!auth()->user()->isBuyer() || $transaction->buyer_id !== auth()->user()->id) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Anda tidak berhak mengakses transaksi ini'
            ], 403);
        }

        if (!$transaction->isAccepted()) {
            return response()->json([
                'error' => 'Order not accepted',
                'message' => 'Pesanan belum dikonfirmasi penjual. Status saat ini: ' . $transaction->status
            ], 400);
        }

        // Jika sudah ada snap token yang belum expired, pakai yang lama
        if ($transaction->payment && $transaction->payment->snap_token) {
            return response()->json([
                'snap_token' => $transaction->payment->snap_token,
            ]);
        }

        try {
            // Configure Midtrans
            $serverKey = config('services.midtrans.server_key');
            $clientKey = config('services.midtrans.client_key');
            $isProduction = config('services.midtrans.is_production');
            
            // Trim whitespace
            $serverKey = trim($serverKey);
            $clientKey = trim($clientKey);
            
            // Validate keys exist
            if (empty($serverKey) || empty($clientKey)) {
                throw new \Exception('Midtrans keys tidak ditemukan di .env');
            }
            
            // Base64 encode server key for Authorization header
            // Format: 'ServerKey:' (with colon at the end)
            $authString = base64_encode($serverKey . ':');
            
            \Log::debug('Midtrans Request Details', [
                'server_key_present' => !empty($serverKey),
                'server_key_length' => strlen($serverKey),
                'is_production' => $isProduction,
                'endpoint' => $isProduction 
                    ? 'https://app.midtrans.com/snap/v1/transactions'
                    : 'https://app.sandbox.midtrans.com/snap/v1/transactions',
                'auth_header' => 'Basic ' . $authString,
            ]);

            $midtransOrderId = 'BLZ-PAY-' . $transaction->id . '-' . time();

            $itemDetails = [];
            
            // Load toppings untuk setiap order item
            $transaction->load('orderItems.toppings');
            
            foreach ($transaction->orderItems as $item) {
                // Hitung price per unit (menu + toppings)
                $toppingPrice = $item->toppings->sum('topping_price');
                $unitPrice = (int)$item->menu_price + $toppingPrice;
                
                // Add menu item
                $itemDetails[] = [
                    'id'       => 'item-' . $item->id,
                    'price'    => (int)$item->menu_price,
                    'quantity' => $item->quantity,
                    'name'     => $item->menu_name,
                ];
                
                // Add toppings sebagai separate items jika ada
                foreach ($item->toppings as $topping) {
                    $itemDetails[] = [
                        'id'       => 'topping-' . $topping->id . '-item-' . $item->id,
                        'price'    => (int)$topping->topping_price,
                        'quantity' => $item->quantity,
                        'name'     => $topping->topping_name . ' (' . $item->menu_name . ')',
                    ];
                }
            }
            
            $params = [
                'transaction_details' => [
                    'order_id'     => $midtransOrderId,
                    'gross_amount' => (int) $transaction->total_price,
                ],
                'customer_details' => [
                    'first_name' => $transaction->buyer->name,
                    'email'      => $transaction->buyer->email,
                ],
                'item_details' => $itemDetails,
            ];

            // Manual cURL request dengan explicit base64 Authorization
            $endpoint = $isProduction 
                ? 'https://app.midtrans.com/snap/v1/transactions'
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Basic ' . $authString,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new \Exception('cURL Error: ' . $curlError);
            }

            $responseData = json_decode($response, true);

            \Log::debug('Midtrans Response', [
                'http_code' => $httpCode,
                'response_body' => $response,
            ]);

            // Midtrans returns 200 or 201 on success
            if ($httpCode !== 200 && $httpCode !== 201) {
                $errorMessage = '';
                if (is_array($responseData) && isset($responseData['error_messages'])) {
                    $errorMessage = is_array($responseData['error_messages']) 
                        ? implode(', ', $responseData['error_messages'])
                        : $responseData['error_messages'];
                } elseif (is_array($responseData) && isset($responseData['errors'])) {
                    $errorMessage = is_array($responseData['errors']) 
                        ? implode(', ', (array)$responseData['errors'])
                        : $responseData['errors'];
                } else {
                    $errorMessage = 'Unknown error';
                }
                
                \Log::error('Midtrans API Response Error', [
                    'http_code' => $httpCode,
                    'error_message' => $errorMessage,
                    'full_response' => $responseData,
                ]);
                throw new \Exception('HTTP ' . $httpCode . ': ' . $errorMessage);
            }

            // Check for token in response
            $snapToken = $responseData['token'] ?? null;
            
            if (empty($snapToken)) {
                throw new \Exception('No token in response. Response: ' . json_encode($responseData));
            }

            // Check for token in response
            $snapToken = $responseData['token'] ?? null;
            
            if (empty($snapToken)) {
                throw new \Exception('No token in response. Response: ' . json_encode($responseData));
            }

            // Simpan atau update payment record
            Payment::updateOrCreate(
                ['transaction_id' => $transaction->id],
                [
                    'midtrans_order_id' => $midtransOrderId,
                    'amount'            => $transaction->total_price,
                    'status'            => Payment::STATUS_PENDING,
                    'snap_token'        => $snapToken,
                ]
            );

            return response()->json(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            \Log::error('Midtrans API Error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'error' => 'Midtrans error',
                'message' => 'Gagal membuat token pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark payment as paid dan update transaction status.
     * Notifikasi dikirim synchronous (tanpa queue) agar seller
     * langsung menerima notif begitu pembayaran settlement.
     */
    private function _markAsPaid(Payment $payment, $midtransStatus): void
    {
        if ($payment->status !== Payment::STATUS_PAID) {
            $payment->update([
                'midtrans_transaction_id' => $midtransStatus->transaction_id,
                'payment_method'          => $midtransStatus->payment_type,
                'status'                  => Payment::STATUS_PAID,
                'paid_at'                 => now(),
            ]);

            $payment->transaction->update(['status' => Transaction::STATUS_PAID]);

            // Notifikasi sync ke semua seller kantin
            foreach ($payment->transaction->canteen->sellers as $seller) {
                $seller->notifyNow(new \App\Notifications\PaymentSuccessNotification($payment->transaction));
            }

            \Log::info('Payment marked as paid from checkStatus', ['payment_id' => $payment->id]);
        }
    }

    /**
     * Mark payment as failed.
     */
    private function _markAsFailed(Payment $payment): void
    {
        if ($payment->status !== Payment::STATUS_FAILED) {
            $payment->update(['status' => Payment::STATUS_FAILED]);
            $payment->transaction->update(['status' => Transaction::STATUS_ACCEPTED]);
            
            \Log::warning('Payment marked as failed from checkStatus', ['payment_id' => $payment->id]);
        }
    }
}
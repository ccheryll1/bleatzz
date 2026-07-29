<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestMidtransWebhook extends Command
{
    protected $signature = 'test:midtrans-webhook {order_id}';
    protected $description = 'Test Midtrans webhook by simulating a payment settlement';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        
        // Find the payment
        $payment = Payment::where('midtrans_order_id', $orderId)->first();
        
        if (!$payment) {
            $this->error("Payment with order_id '{$orderId}' not found");
            return 1;
        }

        $this->info("Found payment: " . $payment->id);
        $this->info("Transaction: " . $payment->transaction->transaction_code);
        $this->info("Current status: " . $payment->status);

        // Simulate webhook payload
        $payload = [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => (int)$payment->amount,
            'transaction_status' => 'settlement',
            'transaction_id' => 'test-txn-' . time(),
            'payment_type' => 'credit_card',
            'fraud_status' => 'accept',
            'signature_key' => hash('sha512', 
                $orderId . '200' . (int)$payment->amount . config('services.midtrans.server_key')
            )
        ];

        // Call webhook handler
        $controller = new \App\Http\Controllers\Webhook\MidtransController();
        $response = $controller->handle(
            new \Illuminate\Http\Request($payload)
        );

        $this->info("Webhook response: " . $response->status());
        
        // Refresh and check
        $payment->refresh();
        $this->info("Updated payment status: " . $payment->status);
        $this->info("Updated transaction status: " . $payment->transaction->status);

        return 0;
    }
}

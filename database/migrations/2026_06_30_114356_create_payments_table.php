<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->string('midtrans_order_id')->unique(); //Order ID yang dikirim ke Midtrans
            $table->string('midtrans_transaction_id')->nullable(); //Transaction ID dari response Midtrans, wajib untuk trigger refund
            $table->string('payment_method')->nullable(); //qris, gopay, bank_transfer, credit_card, dst
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->text('snap_token')->nullable(); //Token Midtrans Snap untuk redirect ke halaman bayar
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

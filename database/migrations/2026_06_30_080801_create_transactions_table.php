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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('canteen_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_code')->unique();
            $table->enum('status', [
                'pending',      // user pesen, tp blom ada respon dari penjual
                'accepted',     // pesanan diterima
                'rejected',     // ------- ditolak
                'paid',         // pesenan dah dibayar
                'processing',   // lagi dibuat ges
                'ready',        // shap diambil
                'done',         // slesai
                'cancelled',    // dibatalin sama pembeli
            ])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancel_requested_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

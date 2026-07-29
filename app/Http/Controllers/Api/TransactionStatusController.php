<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionStatusController extends Controller
{
    /**
     * Get transaction status via API (untuk polling)
     */
    public function show(Transaction $transaction): JsonResponse
    {
        // Check authorization
        if ($transaction->buyer_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $transaction->id,
            'transaction_code' => $transaction->transaction_code,
            'status' => $transaction->status,
            'total_price' => $transaction->total_price,
            'buyer_id' => $transaction->buyer_id,
            'canteen_id' => $transaction->canteen_id,
            'canteen_name' => $transaction->canteen?->name,
            'confirmed_at' => $transaction->confirmed_at,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
        ]);
    }

    /**
     * List pending transactions untuk seller (API)
     */
    public function sellerPending(): JsonResponse
    {
        $canteenIds = auth()->user()->canteens()->select('canteens.id')->pluck('canteens.id')->toArray();

        $transactions = Transaction::whereIn('canteen_id', $canteenIds)
            ->where('status', Transaction::STATUS_PENDING)
            ->with(['buyer', 'canteen', 'orderItems'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'transaction_code' => $t->transaction_code,
                    'buyer_name' => $t->buyer?->name,
                    'buyer_id' => $t->buyer_id,
                    'total_price' => $t->total_price,
                    'items_count' => $t->orderItems->count(),
                    'created_at' => $t->created_at,
                    'canteen_id' => $t->canteen_id,
                ];
            });

        return response()->json([
            'count' => $transactions->count(),
            'transactions' => $transactions,
        ]);
    }

    /**
     * Check if seller has pending orders
     */
    public function hasPending(): JsonResponse
    {
        $canteenIds = auth()->user()->canteens()->select('canteens.id')->pluck('canteens.id')->toArray();

        $hasPending = Transaction::whereIn('canteen_id', $canteenIds)
            ->where('status', Transaction::STATUS_PENDING)
            ->exists();

        return response()->json([
            'has_pending' => $hasPending,
            'pending_count' => Transaction::whereIn('canteen_id', $canteenIds)
                ->where('status', Transaction::STATUS_PENDING)
                ->count(),
        ]);
    }
}

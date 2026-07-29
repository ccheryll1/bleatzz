<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('review', $transaction);

        if (! $transaction->isReviewable()) {
            return back()->with('error', 'Pesanan ini tidak dapat diberikan ulasan.');
        }

        $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        Review::create([
            'transaction_id' => $transaction->id,
            'buyer_id'       => $transaction->buyer_id,
            'canteen_id'     => $transaction->canteen_id,
            'rating'         => $request->rating,
            'comment'        => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda telah disimpan.');
    }
}

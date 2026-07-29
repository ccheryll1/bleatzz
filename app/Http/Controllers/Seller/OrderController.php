<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /** List semua pesanan untuk seller */
    public function index(Request $request): View
    {
        // Ambil canteen IDs milik seller yang login
        $user = auth()->user();
        $canteenIds = $user->canteens()->select('canteens.id')->pluck('canteens.id')->toArray();
        
        // Filter berdasarkan status query parameter
        $query = Transaction::whereIn('canteen_id', $canteenIds)
            ->with(['buyer', 'canteen', 'orderItems.toppings'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->paginate(10);

        return view('pages.admin.seller.orders.index', compact('transactions'));
    }

    /** Detail satu pesanan */
    public function show(Transaction $transaction): View
    {
        $this->authorize('view', $transaction);
        
        $transaction->load(['buyer', 'canteen', 'orderItems.toppings', 'payment']);

        return view('pages.admin.seller.orders.show', compact('transaction'));
    }
}

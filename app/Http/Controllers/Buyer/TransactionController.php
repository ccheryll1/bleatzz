<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Transaction;
use App\Models\OrderItem;
use App\Models\OrderItemTopping;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /** Daftar pesanan AKTIF buyer (pending sampai ready) */
    public function index(Request $request): View
    {
        $activeStatuses = [
            Transaction::STATUS_PENDING,
            Transaction::STATUS_ACCEPTED,
            Transaction::STATUS_PAID,
            Transaction::STATUS_PROCESSING,
            Transaction::STATUS_READY,
        ];

        $transactions = auth()->user()
            ->transactions()
            ->with('canteen', 'orderItems.toppings')
            ->whereIn('status', $activeStatuses)
            ->latest()
            ->paginate(10);

        return view('pages.landingpage.order.index', compact('transactions'));
    }

    /** Riwayat pesanan SELESAI buyer (done, cancelled, rejected) */
    public function history(Request $request): View
    {
        $request->validate([
            'status' => ['nullable', 'string', 'in:done,cancelled,rejected'],
        ]);

        $historyStatuses = [
            Transaction::STATUS_DONE,
            Transaction::STATUS_CANCELLED,
            Transaction::STATUS_REJECTED,
        ];

        $query = auth()->user()
            ->transactions()
            ->with('canteen', 'orderItems.toppings', 'review')
            ->whereIn('status', $historyStatuses);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.buyer.transactions.index', compact('transactions'));
    }

    /** Detail satu pesanan */
    public function show(Transaction $transaction): View
    {
        $this->authorize('view', $transaction);

        $transaction->load(['orderItems.toppings', 'canteen', 'payment', 'review']);

        return view('pages.buyer.transactions.show', compact('transaction'));
    }

    /**
     * Checkout: buat transaksi dari keranjang.
     * Cart items digroup per kantin → 1 kantin = 1 transaksi.
     * Bisa checkout item spesifik saja (dari selection di cart view)
     */
    public function store(Request $request)
    {
        $buyer = auth()->user();
        
        // Cek apakah ada selected items dari request (dari cart view selection)
        $selectedItemIds = null;
        if ($request->has('selectedCartItems')) {
            // New flow: selected items dari POST body
            $selectedItemIds = json_decode($request->input('selectedCartItems', '[]'), true);
            
            if (empty($selectedItemIds) || !is_array($selectedItemIds)) {
                $msg = 'Pilih minimal 1 pesanan.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $msg], 422);
                }
                return back()->with('error', $msg);
            }
            
            $query = $buyer->cartItems()
                ->with(['menu', 'toppings'])
                ->whereIn('id', $selectedItemIds);
        } elseif ($request->has('canteen_ids')) {
            // Legacy flow: from form POST
            $request->validate([
                'canteen_ids'   => ['required', 'array'],
                'canteen_ids.*' => ['exists:canteens,id'],
            ]);
            $query = $buyer->cartItems()
                ->with(['menu', 'toppings'])
                ->whereHas('menu', fn($q) => $q->whereIn('canteen_id', $request->canteen_ids));
        } else {
            $msg = 'Pilih minimal 1 pesanan.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $cartItems = $query->get();

        if ($cartItems->isEmpty()) {
            $msg = 'Keranjang kosong atau item sudah dihapus.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // Validasi semua menu masih bisa dipesan
        foreach ($cartItems as $item) {
            if (! $item->menu->isOrderable()) {
                $msg = "Menu \"{$item->menu->name}\" sudah tidak tersedia.";
                if ($request->expectsJson()) {
                    return response()->json(['error' => $msg], 422);
                }
                return back()->with('error', $msg);
            }
        }

        $firstTransaction = null;

        DB::transaction(function () use ($buyer, $cartItems, $selectedItemIds, &$firstTransaction) {
            $grouped = $cartItems->groupBy('menu.canteen_id');

            foreach ($grouped as $canteenId => $items) {
                $totalPrice = $items->sum('subtotal');

                $transaction = Transaction::create([
                    'buyer_id'         => $buyer->id,
                    'canteen_id'       => $canteenId,
                    'transaction_code' => Transaction::generateCode(),
                    'status'           => Transaction::STATUS_PENDING,
                    'total_price'      => $totalPrice,
                ]);

                // Save first transaction untuk response
                if (!$firstTransaction) {
                    $firstTransaction = $transaction;
                }

                foreach ($items as $cartItem) {
                    $orderItem = OrderItem::create([
                        'transaction_id' => $transaction->id,
                        'menu_id'        => $cartItem->menu_id,
                        'menu_name'      => $cartItem->menu->name,
                        'menu_price'     => $cartItem->menu->price,
                        'quantity'       => $cartItem->quantity,
                        'notes'          => $cartItem->notes,
                        'subtotal'       => $cartItem->subtotal,
                    ]);

                    foreach ($cartItem->toppings as $topping) {
                        OrderItemTopping::create([
                            'order_item_id' => $orderItem->id,
                            'topping_id'    => $topping->id,
                            'topping_name'  => $topping->name,
                            'topping_price' => $topping->price,
                        ]);
                    }

                    // Kurangi stok jika stock_type = counted
                    if ($cartItem->menu->stock_type === 'counted') {
                        $cartItem->menu->decrement('stock_qty', $cartItem->quantity);
                    }

                    $cartItem->toppings()->detach();
                    $cartItem->delete();
                }

                // Notifikasi ke penjual kantin
                foreach ($transaction->canteen->sellers as $seller) {
                    $seller->notify(new OrderPlacedNotification($transaction));
                }
            }
        });

        // Return JSON untuk AJAX atau redirect untuk form submit
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'transaction_id' => $firstTransaction->id,
                'transaction_code' => $firstTransaction->transaction_code,
                'message' => 'Pesanan berhasil dibuat! Tunggu konfirmasi penjual.',
            ]);
        }

        return redirect()->route('buyer.transactions.index')
            ->with('success', 'Pesanan berhasil dibuat! Tunggu konfirmasi penjual.');
    }

    /**
     * Pembatalan oleh buyer.
     * - Status pending  → langsung cancel
     * - Status processing → ajukan request cancel, tunggu acc penjual
     */
    public function cancel(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('cancel', $transaction);

        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        if ($transaction->isCancellableByBuyerFreely()) {
            $transaction->update([
                'status'               => Transaction::STATUS_CANCELLED,
                'cancellation_reason'  => $request->reason,
            ]);

            return back()->with('success', 'Pesanan berhasil dibatalkan.');
        }

        if ($transaction->isCancellableWithSellerApproval()) {
            $transaction->update([
                'cancellation_reason'  => $request->reason,
                'cancel_requested_at'  => now(),
            ]);

            // Notifikasi ke penjual ada request cancel
            foreach ($transaction->canteen->sellers as $seller) {
                $seller->notify(new \App\Notifications\CancelRequestedNotification($transaction));
            }

            return back()->with('success', 'Permintaan pembatalan telah dikirim ke penjual.');
        }

        return back()->with('error', 'Pesanan ini tidak bisa dibatalkan.');
    }

    /** Buyer konfirmasi pesanan sudah diambil */
    public function confirm(Transaction $transaction): RedirectResponse
    {
        $this->authorize('confirm', $transaction);

        if (! $transaction->isReady()) {
            return back()->with('error', 'Pesanan belum siap diambil.');
        }

        $transaction->update([
            'status'       => Transaction::STATUS_DONE,
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Pesanan dikonfirmasi selesai. Terima kasih!');
    }

    /** Riwayat pengeluaran buyer per periode */
    public function spending(Request $request): View
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = auth()->user()
            ->transactions()
            ->where('status', Transaction::STATUS_DONE)
            ->with('canteen');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->latest()->get();
        $totalSpending = $transactions->sum('total_price');

        return view('pages.buyer.transactions.spending', compact('transactions', 'totalSpending'));
    }
}
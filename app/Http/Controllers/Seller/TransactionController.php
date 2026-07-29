<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    private function authorizeCanteen(Canteen $canteen): void
    {
        $allowed = auth()->user()->canteens()->select('canteens.id')->pluck('canteens.id')->all();
        abort_unless(in_array($canteen->id, $allowed, true), 403);
    }

    private function matchCanteen(Canteen $canteen, Transaction $transaction): void
    {
        abort_unless((int) $transaction->canteen_id === (int) $canteen->id, 403);
    }

    /** Halaman daftar canteen seller untuk masuk ke keuangan */
    public function chooseCanteen(Request $request): View
    {
        $canteens = auth()->user()->canteens()
            ->withCount(['transactions as total_orders', 'transactions as done_orders' => function ($q) {
                $q->where('status', Transaction::STATUS_DONE);
            }])
            ->withSum(['transactions as total_revenue' => function ($q) {
                $q->where('status', Transaction::STATUS_DONE);
            }], 'total_price')
            ->latest('id')
            ->get();

        return view('pages.admin.seller.finance.choose', compact('canteens'));
    }

    /** Keuangan & daftar pesanan per kantin */
    public function index(Request $request, Canteen $canteen): View
    {
        $this->authorizeCanteen($canteen);

        $validated = $request->validate([
            'status'    => ['nullable', 'string', 'in:pending,accepted,paid,processing,ready,done,cancelled,rejected'],
            'period'    => ['nullable', 'string', 'in:today,week,month,all,custom'],
            'from'      => ['nullable', 'date'],
            'to'        => ['nullable', 'date', 'after_or_equal:from'],
            'search'    => ['nullable', 'string', 'max:100'],
        ]);

        $status = $validated['status'] ?? null;
        $period = $validated['period'] ?? 'all';
        $from   = $validated['from'] ?? null;
        $to     = $validated['to'] ?? null;
        $search = $validated['search'] ?? null;

        $baseQuery = $canteen->transactions()->with(['buyer', 'orderItems']);

        switch ($period) {
            case 'today':
                $baseQuery->whereDate('created_at', today());
                break;
            case 'week':
                $baseQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $baseQuery->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'custom':
                if ($from) {
                    $baseQuery->whereDate('created_at', '>=', $from);
                }
                if ($to) {
                    $baseQuery->whereDate('created_at', '<=', $to);
                }
                break;
        }

        if ($status) {
            $baseQuery->where('status', $status);
        }

        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', '%'.$search.'%')
                  ->orWhereHas('buyer', function ($b) use ($search) {
                      $b->where('name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%');
                  });
            });
        }

        // Clone for stats (only "success/done" for revenue counting)
        $statsQuery = (clone $baseQuery);
        $statsQueryDone = (clone $baseQuery)->where('status', Transaction::STATUS_DONE);

        $totalRevenue  = (float) $statsQueryDone->sum('total_price');
        $totalOrders   = (int)   $statsQuery->count();
        $doneOrders    = (int)   $statsQueryDone->count();
        $avgOrderValue = $doneOrders > 0 ? ($totalRevenue / $doneOrders) : 0;

        $pendingNew = $canteen->transactions()->where('status', Transaction::STATUS_PENDING)->count();
        $processing = $canteen->transactions()->whereIn('status', [Transaction::STATUS_PAID, Transaction::STATUS_PROCESSING])->count();

        $transactions = $baseQuery->latest()->paginate(15)->withQueryString();

        return view('pages.admin.seller.finance.index', compact(
            'canteen',
            'transactions',
            'totalRevenue',
            'totalOrders',
            'doneOrders',
            'avgOrderValue',
            'pendingNew',
            'processing',
            'status',
            'period',
            'from',
            'to',
            'search'
        ));
    }

    /** Detail satu pesanan */
    public function show(Canteen $canteen, Transaction $transaction): View
    {
        $this->authorizeCanteen($canteen);
        $this->matchCanteen($canteen, $transaction);

        $transaction->load(['buyer', 'orderItems.toppings', 'payment']);

        return view('pages.admin.seller.finance.show', compact('canteen', 'transaction'));
    }

    /** Terima pesanan → accepted */
    public function accept(Canteen $canteen, Transaction $transaction): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        $this->matchCanteen($canteen, $transaction);

        if (! $transaction->isPending()) {
            return back()->with('error', 'Pesanan tidak dalam status menunggu.');
        }

        $transaction->update(['status' => Transaction::STATUS_ACCEPTED]);

        if (class_exists(\App\Notifications\OrderAcceptedNotification::class) && $transaction->buyer) {
            try {
                $transaction->buyer->notify(new \App\Notifications\OrderAcceptedNotification($transaction));
            } catch (\Throwable $e) {
                // ignore notification errors
            }
        }

        return back()->with('success', 'Pesanan diterima. Buyer dapat melakukan pembayaran.');
    }

    /** Tolak pesanan → rejected */
    public function reject(Request $request, Canteen $canteen, Transaction $transaction): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        $this->matchCanteen($canteen, $transaction);

        if (! $transaction->isPending()) {
            return back()->with('error', 'Pesanan tidak dalam status menunggu.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ]);

        $transaction->update([
            'status'           => Transaction::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        if (class_exists(\App\Notifications\OrderRejectedNotification::class) && $transaction->buyer) {
            try {
                $transaction->buyer->notify(new \App\Notifications\OrderRejectedNotification($transaction));
            } catch (\Throwable $e) {
            }
        }

        return back()->with('success', 'Pesanan ditolak.');
    }

    /** Proses pesanan → processing */
    public function process(Canteen $canteen, Transaction $transaction): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        $this->matchCanteen($canteen, $transaction);

        if (! $transaction->isPaid()) {
            return back()->with('error', 'Pesanan belum dibayar.');
        }

        $transaction->update(['status' => Transaction::STATUS_PROCESSING]);

        return back()->with('success', 'Pesanan sedang diproses.');
    }

    /** Siap diambil → ready */
    public function ready(Canteen $canteen, Transaction $transaction): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        $this->matchCanteen($canteen, $transaction);

        if (! $transaction->isProcessing()) {
            return back()->with('error', 'Pesanan belum dalam proses.');
        }

        $transaction->update(['status' => Transaction::STATUS_READY]);

        if (class_exists(\App\Notifications\OrderReadyNotification::class) && $transaction->buyer) {
            try {
                $transaction->buyer->notify(new \App\Notifications\OrderReadyNotification($transaction));
            } catch (\Throwable $e) {
            }
        }

        return back()->with('success', 'Pembeli dinotifikasi pesanan siap diambil.');
    }

    /** Selesai diambil → done */
    public function done(Canteen $canteen, Transaction $transaction): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        $this->matchCanteen($canteen, $transaction);

        if (! $transaction->isReady()) {
            return back()->with('error', 'Pesanan belum siap diambil.');
        }

        $transaction->update([
            'status'       => Transaction::STATUS_DONE,
            'confirmed_at' => now(),
        ]);

        if (class_exists(\App\Notifications\OrderDoneNotification::class) && $transaction->buyer) {
            try {
                $transaction->buyer->notify(new \App\Notifications\OrderDoneNotification($transaction));
            } catch (\Throwable $e) {
            }
        }

        return back()->with('success', 'Pesanan selesai.');
    }

    /** Setujui pembatalan (refund) */
    public function approveCancellation(Canteen $canteen, Transaction $transaction): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        $this->matchCanteen($canteen, $transaction);

        if (! $transaction->hasCancelRequest()) {
            return back()->with('error', 'Tidak ada permintaan pembatalan.');
        }

        $transaction->update(['status' => Transaction::STATUS_CANCELLED]);

        if ($transaction->payment && method_exists($transaction->payment, 'isRefundable') && $transaction->payment->isRefundable()) {
            if (class_exists(\App\Jobs\ProcessRefundJob::class)) {
                try {
                    dispatch(new \App\Jobs\ProcessRefundJob($transaction->payment));
                } catch (\Throwable $e) {
                }
            }
        }

        if (class_exists(\App\Notifications\CancelApprovedNotification::class) && $transaction->buyer) {
            try {
                $transaction->buyer->notify(new \App\Notifications\CancelApprovedNotification($transaction));
            } catch (\Throwable $e) {
            }
        }

        return back()->with('success', 'Pembatalan disetujui. Refund diproses.');
    }

    /** Tolak pembatalan */
    public function rejectCancellation(Canteen $canteen, Transaction $transaction): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        $this->matchCanteen($canteen, $transaction);

        if (! $transaction->hasCancelRequest()) {
            return back()->with('error', 'Tidak ada permintaan pembatalan.');
        }

        $transaction->update([
            'cancellation_reason' => null,
            'cancel_requested_at' => null,
        ]);

        if (class_exists(\App\Notifications\CancelRejectedNotification::class) && $transaction->buyer) {
            try {
                $transaction->buyer->notify(new \App\Notifications\CancelRejectedNotification($transaction));
            } catch (\Throwable $e) {
            }
        }

        return back()->with('success', 'Permintaan pembatalan ditolak.');
    }

    /** Export CSV laporan transaksi SELESAI per periode */
    public function export(Request $request, Canteen $canteen): StreamedResponse
    {
        $this->authorizeCanteen($canteen);

        $validated = $request->validate([
            'period' => ['nullable', 'string', 'in:today,week,month,all,custom'],
            'from'   => ['nullable', 'date'],
            'to'     => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $period = $validated['period'] ?? 'all';
        $from   = $validated['from'] ?? null;
        $to     = $validated['to'] ?? null;

        $query = $canteen->transactions()
            ->where('status', Transaction::STATUS_DONE)
            ->with(['buyer', 'orderItems']);

        switch ($period) {
            case 'today':
                $query->whereDate('confirmed_at', today());
                break;
            case 'week':
                $query->whereBetween('confirmed_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('confirmed_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'custom':
                if ($from) {
                    $query->whereDate('confirmed_at', '>=', $from);
                }
                if ($to) {
                    $query->whereDate('confirmed_at', '<=', $to);
                }
                break;
        }

        $transactions = $query->latest('confirmed_at')->get();

        $filename = sprintf(
            'laporan-%s-%s.csv',
            str($canteen->canteen_name)->slug()->toString(),
            now()->format('Ymd-His')
        );

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->stream(function () use ($transactions, $canteen) {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

            fputcsv($handle, ['LAPORAN PENJUALAN - '.strtoupper($canteen->canteen_name)]);
            fputcsv($handle, ['Dicetak tanggal', now()->format('d F Y H:i')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'No',
                'Kode Transaksi',
                'Tanggal Selesai',
                'Pembeli',
                'Total Item',
                'Total Harga',
                'Metode Bayar',
            ]);

            $no = 1;
            $grandTotal = 0;
            foreach ($transactions as $tx) {
                $itemCount = $tx->orderItems->sum('quantity');
                $total = (float) $tx->total_price;
                $grandTotal += $total;

                fputcsv($handle, [
                    $no++,
                    $tx->transaction_code,
                    $tx->confirmed_at?->format('d F Y H:i') ?? $tx->created_at->format('d F Y H:i'),
                    $tx->buyer?->name ?? 'User',
                    $itemCount,
                    $total,
                    $tx->payment?->payment_method ?? '-',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', 'TOTAL PENDAPATAN', $grandTotal, '']);

            fclose($handle);
        }, 200, $headers);
    }
}

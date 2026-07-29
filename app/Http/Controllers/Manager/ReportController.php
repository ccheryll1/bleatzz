<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /** Dashboard monitoring semua kantin */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from'       => ['nullable', 'date'],
            'to'         => ['nullable', 'date', 'after_or_equal:from'],
            'canteen_id' => ['nullable', 'exists:canteens,id'],
            'period'     => ['nullable', 'string', 'in:today,week,month,all,custom'],
        ]);

        $period     = $validated['period'] ?? 'all';
        $from       = $validated['from'] ?? null;
        $to         = $validated['to'] ?? null;
        $canteenId  = $validated['canteen_id'] ?? null;

        $query = Transaction::where('status', Transaction::STATUS_DONE)
            ->with('canteen.seller.user');

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

        if ($canteenId) {
            $query->where('canteen_id', $canteenId);
        }

        $transactions = $query->latest('confirmed_at')->get();

        $summaryPerCanteen = $transactions
            ->groupBy('canteen_id')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'canteen'       => $first->canteen,
                    'seller_name'   => optional(optional(optional($first->canteen)->seller)->user)->name ?? '-',
                    'total_orders'  => $group->count(),
                    'total_revenue' => (float) $group->sum('total_price'),
                    'avg_order'     => $group->count() > 0 ? ((float) $group->sum('total_price') / $group->count()) : 0,
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();

        $grandTotal  = (float) $transactions->sum('total_price');
        $totalOrders = (int)   $transactions->count();
        $avgOrder    = $totalOrders > 0 ? ($grandTotal / $totalOrders) : 0;
        $canteens    = Canteen::orderBy('canteen_name')->get();

        return view('pages.admin.manager.reports.index', compact(
            'transactions',
            'summaryPerCanteen',
            'grandTotal',
            'totalOrders',
            'avgOrder',
            'canteens',
            'period',
            'from',
            'to',
            'canteenId'
        ));
    }

    /** Export global CSV laporan */
    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'from'       => ['nullable', 'date'],
            'to'         => ['nullable', 'date', 'after_or_equal:from'],
            'canteen_id' => ['nullable', 'exists:canteens,id'],
            'period'     => ['nullable', 'string', 'in:today,week,month,all,custom'],
        ]);

        $period    = $validated['period'] ?? 'all';
        $from      = $validated['from'] ?? null;
        $to        = $validated['to'] ?? null;
        $canteenId = $validated['canteen_id'] ?? null;

        $query = Transaction::where('status', Transaction::STATUS_DONE)
            ->with(['canteen', 'buyer', 'orderItems']);

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

        if ($canteenId) {
            $query->where('canteen_id', $canteenId);
        }

        $transactions = $query->latest('confirmed_at')->get();

        $filename = sprintf('laporan-global-bleatz-%s.csv', now()->format('Ymd-His'));
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->stream(function () use ($transactions) {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['LAPORAN PENJUALAN GLOBAL - BLEATZ']);
            fputcsv($handle, ['Dicetak tanggal', now()->format('d F Y H:i')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'No',
                'Kode Transaksi',
                'Tanggal Selesai',
                'Kantin',
                'Pembeli',
                'Total Item',
                'Total Harga',
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
                    $tx->canteen?->canteen_name ?? '-',
                    $tx->buyer?->name ?? 'User',
                    $itemCount,
                    $total,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', '', 'TOTAL PENDAPATAN', $grandTotal]);

            fclose($handle);
        }, 200, $headers);
    }
}

<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use Illuminate\Http\Request;

class CanteenController extends Controller
{
    public function index(Request $request)
    {
        // 'all' dianggap "gak difilter" — biar gampang bandingin di view (active state chip)
        $category = $request->query('category', 'all');
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));

        $canteens = Canteen::query()
            ->with('menus')
            // Dipake buat nampilin rating di list-card (reviews_avg_rating & reviews_count),
            // TANPA nembak query baru per kantin pas di-loop (hindari N+1).
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('canteen_name', 'like', '%' . $search . '%');
            })
            ->when($category !== 'all', function ($query) use ($category) {
                $query->whereHas('menus', function ($menuQuery) use ($category) {
                    $menuQuery->where('category', $category)->where('is_available', true);
                });
            })
            ->when($status === 'open', fn ($query) => $query->where('is_open', true))
            ->when($status === 'closed', fn ($query) => $query->where('is_open', false))
            ->orderByDesc('is_open')
            ->orderBy('canteen_name')
            ->get();

        return view('pages.buyer.canteen.index', [
            'canteens' => $canteens,
            'activeCategory' => $category,
            'activeStatus' => $status,
            'activeSearch' => $search !== '' ? $search : null,
        ]);
    }

    public function show(Request $request, Canteen $canteen)
    {
        // 'all' = gak difilter. Beda sama index(): status di sini soal
        // KETERSEDIAAN MENU (is_available), bukan buka/tutup kantin.
        $category = $request->query('category', 'all');
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));

        $canteen->loadCount('reviews')->loadAvg('reviews', 'rating');

        $menus = $canteen->menus()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%' . $search . '%'))
            ->when($category !== 'all', fn ($query) => $query->where('category', $category))
            ->when($status === 'available', fn ($query) => $query->where('is_available', true))
            ->when($status === 'unavailable', fn ($query) => $query->where('is_available', false))
            ->orderByDesc('is_available')
            ->orderBy('name')
            ->get()
            // Set manual relasi 'canteen' ke instance yang udah ada,
            // biar menu-card gak nembak query baru per kartu buat ambil nama kantin.
            ->each(fn ($menu) => $menu->setRelation('canteen', $canteen));

        $favoritedMenuIds = auth()->check()
            ? auth()->user()->favorites()->pluck('menus.id')->all()
            : [];

        return view('pages.buyer.canteen.info-canteen', [
            'canteen' => $canteen,
            'menus' => $menus,
            'favoritedMenuIds' => $favoritedMenuIds,
            'activeCategory' => $category,
            'activeStatus' => $status,
            'activeSearch' => $search !== '' ? $search : null,
        ]);
    }
}
<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // 'all' dianggap "gak difilter" — biar gampang bandingin di view (active state chip)
        $category = $request->query('category', 'all');
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));

        $menus = Menu::query()
            ->with('canteen')
            // Eager load canteen reviews untuk rating & count
            ->with('canteen.reviews')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($category !== 'all', function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($status === 'available', fn ($query) => $query->where('is_available', true))
            ->when($status === 'unavailable', fn ($query) => $query->where('is_available', false))
            ->orderByDesc('is_available')
            ->orderBy('name')
            ->get();

        $favoritedMenuIds = auth()->check()
            ? auth()->user()->favorites()->pluck('menus.id')->all()
            : [];

        return view('pages.buyer.menu.index', [
            'menus' => $menus,
            'favoritedMenuIds' => $favoritedMenuIds,
            'activeCategory' => $category,
            'activeStatus' => $status,
            'activeSearch' => $search !== '' ? $search : null,
        ]);
    }
}

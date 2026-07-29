<?php

namespace App\Http\Controllers;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Review;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Kantin — eager-load menus agar priceRangeLabel() tidak N+1
        $canteens = Canteen::with('menus')
            ->orderByDesc('is_open')
            ->latest()
            ->limit(6)
            ->get();

        // Menu populer — diurutkan berdasarkan total qty terjual
        $popularMenus = Menu::with('canteen')
            ->where('is_available', true)
            ->withSum('orderItems as sold_qty', 'quantity')
            ->orderByDesc('sold_qty')
            ->limit(3)
            ->get();

        // Ulasan terbaru dengan rating tinggi
        $reviews = Review::with(['buyer', 'canteen'])
            ->where('rating', '>=', 4)
            ->latest()
            ->limit(4)
            ->get();

        return view('pages.landingpage.front.index', compact('canteens', 'popularMenus', 'reviews'));
    }

    public function canteen(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $search = trim((string) $request->query('search', ''));

        $canteens = Canteen::query()
            ->with('menus')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('canteen_name', 'like', '%' . $search . '%')
                      ->orWhereHas('menus', function ($menuQuery) use ($search) {
                          $menuQuery->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->when($filter === 'open', fn ($query) => $query->where('is_open', true))
            ->when($filter === 'closed', fn ($query) => $query->where('is_open', false))
            ->orderByDesc('is_open')
            ->orderBy('canteen_name')
            ->paginate(9)
            ->withQueryString();

        return view('pages.landingpage.canteen.index', compact('canteens'));
    }

    public function canteenDetail(Request $request, Canteen $canteen)
    {
        $search = trim((string) $request->query('search', ''));
        $category = $request->query('category', 'all');
        $sort = $request->query('sort', 'price_asc');

        $menus = Menu::query()
            ->where('canteen_id', $canteen->id)
            ->where('is_available', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->when($category !== 'all', function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($sort === 'price_asc', fn ($query) => $query->orderBy('price', 'asc'))
            ->when($sort === 'price_desc', fn ($query) => $query->orderBy('price', 'desc'))
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        return view('pages.landingpage.canteen.detail', compact('canteen', 'menus'));
    }

    public function menu(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $category = $request->query('category', 'all');
        $sort = $request->query('sort', 'price_asc');

        $menus = Menu::query()
            ->with('canteen')
            ->where('is_available', true)
            ->whereHas('canteen', function ($q) {
                $q->where('is_open', true);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('menus.name', 'like', '%' . $search . '%')
                      ->orWhere('menus.description', 'like', '%' . $search . '%')
                      ->orWhereHas('canteen', function ($cq) use ($search) {
                          $cq->where('canteen_name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->when($category !== 'all', function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($sort === 'price_asc', fn ($query) => $query->orderBy('price', 'asc'))
            ->when($sort === 'price_desc', fn ($query) => $query->orderBy('price', 'desc'))
            ->orderBy('menus.name')
            ->paginate(9)
            ->withQueryString();

        return view('pages.landingpage.menu.index', compact('menus'));
    }
}

<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        // Eager-load menus agar priceRangeLabel()/availableCategories() di model
        // tidak nembak query baru per kantin saat di-loop di view (hindari N+1).
        $canteens = Canteen::with('menus')
            ->orderByDesc('is_open')
            ->latest()
            ->get();

        $popularMenus = $this->getPopularMenus();

        $reviews = $this->getRecentReviews();

        return view('pages.buyer.home.index', compact('canteens', 'popularMenus', 'reviews'));
    }

    private function getPopularMenus(int $perCategory = 4)
    {
        return collect(['food', 'drink', 'snack'])->mapWithKeys(function ($category) use ($perCategory) {
            $menus = Menu::query()
                ->with('canteen')
                ->where('category', $category)
                ->where('is_available', true)
                ->withSum('orderItems as sold_qty', 'quantity')
                ->orderByDesc('sold_qty')
                ->take($perCategory)
                ->get();

            return [$category => $menus];
        });
    }

    private function getRecentReviews(int $limit = 8)
    {
        return Review::query()
            ->with(['buyer', 'canteen'])
            ->where('rating', '>=', 4)   // tampilkan yang positif di landing page
            ->latest()
            ->take($limit)
            ->get();
    }
}
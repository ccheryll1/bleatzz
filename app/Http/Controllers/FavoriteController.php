<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $menus = $user->favorites()
            ->with('canteen')
            ->where('is_available', true)
            ->orderByDesc('favorites.created_at')
            ->paginate(9)
            ->withQueryString();

        return view('pages.landingpage.favorite.index', compact('menus'));
    }

    public function toggle(Request $request, Menu $menu)
    {
        $user = $request->user();

        $isFavorited = $user->favorites()->where('menu_id', $menu->id)->exists();

        if ($isFavorited) {
            $user->favorites()->detach($menu->id);
            $favorited = false;
        } else {
            $user->favorites()->attach($menu->id);
            $favorited = true;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'favorited' => $favorited,
                'menu_id'   => $menu->id,
            ]);
        }

        return back()->with('status', $favorited ? 'Ditambahkan ke favorit' : 'Dihapus dari favorit');
    }
}

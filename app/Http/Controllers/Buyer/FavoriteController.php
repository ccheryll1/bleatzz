<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    /** Daftar menu yang difavoritkan */
    public function index(): View
    {
        $favorites = auth()->user()
            ->favorites()
            ->with('canteen')
            ->get();

        return view('pages.buyer.favorite.index', compact('favorites'));
    }

    /** Toggle favorit: kalau sudah ada → hapus, kalau belum → tambah */
    public function toggle(Menu $menu): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        $exists = $user->favorites()->where('menu_id', $menu->id)->exists();

        if ($exists) {
            $user->favorites()->detach($menu->id);
            $message = 'Menu dihapus dari favorit.';
        } else {
            $user->favorites()->attach($menu->id);
            $message = 'Menu ditambahkan ke favorit.';
        }

        // Return JSON if AJAX request, otherwise redirect
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
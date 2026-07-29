<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Topping;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CanteenController extends Controller
{
    /** Daftar semua kantin */
    public function index(Request $request): View
    {
        $canteens = Canteen::with(['seller.user'])
            ->withCount('menus')
            ->when($request->search, fn($q) => $q->where('canteen_name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $totalCanteens = Canteen::count();
        $totalOpen = Canteen::where('is_open', true)->count();
        $totalWithoutSeller = Canteen::doesntHave('seller')->count();

        return view('pages.admin.manager.canteens.index', compact('canteens', 'totalCanteens', 'totalOpen', 'totalWithoutSeller'));
    }

    /** Form buat kantin baru */
    public function create(): View
    {
        $sellers = User::where('role', 'seller')
            ->whereDoesntHave('canteens')
            ->get();

        return view('pages.admin.manager.canteens.create', compact('sellers'));
    }

    /** Simpan kantin baru */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'canteen_name'       => ['required', 'string', 'max:100'],
            'description'        => ['nullable', 'string', 'max:500'],
            'photo'              => ['nullable', 'image', 'max:2048'],
            'estimated_time_min' => ['nullable', 'integer', 'min:1', 'max:180'],
            'seller_id'          => ['nullable', 'exists:users,id'],
        ]);

        $data = collect($validated)->only(['canteen_name', 'description', 'estimated_time_min'])->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('canteens', 'public');
        }

        $canteen = Canteen::create($data);

        if ($request->filled('seller_id')) {
            $seller = User::where('id', $request->seller_id)->where('role', 'seller')->first();
            if ($seller) {
                $canteen->syncSeller($seller->id);
            }
        }

        return redirect()->route('manager.canteens.index')
            ->with('success', 'Kantin berhasil dibuat.');
    }

    /** Form edit kantin */
    public function edit(Canteen $canteen): View
    {
        $canteen->load('seller.user');

        $currentSellerId = $canteen->seller?->user_id;

        $sellers = User::where('role', 'seller')
            ->where(function ($q) use ($currentSellerId) {
                $q->whereDoesntHave('canteens');
                if ($currentSellerId) {
                    $q->orWhere('id', $currentSellerId);
                }
            })
            ->get();

        return view('pages.admin.manager.canteens.edit', compact('canteen', 'sellers', 'currentSellerId'));
    }

    /** Update kantin */
    public function update(Request $request, Canteen $canteen): RedirectResponse
    {
        $validated = $request->validate([
            'canteen_name'       => ['required', 'string', 'max:100'],
            'description'        => ['nullable', 'string', 'max:500'],
            'photo'              => ['nullable', 'image', 'max:2048'],
            'estimated_time_min' => ['nullable', 'integer', 'min:1', 'max:180'],
            'seller_id'          => ['nullable', 'exists:users,id'],
        ]);

        $data = collect($validated)->only(['canteen_name', 'description', 'estimated_time_min'])->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('canteens', 'public');
        }

        $canteen->update($data);

        if ($request->filled('seller_id')) {
            $seller = User::where('id', $request->seller_id)->where('role', 'seller')->first();
            if ($seller) {
                $canteen->syncSeller($seller->id);
            }
        } else {
            $canteen->clearSeller();
        }

        return back()->with('success', 'Kantin berhasil diperbarui.');
    }

    /** Hapus kantin */
    public function destroy(Canteen $canteen): RedirectResponse
    {
        $canteen->clearSeller();
        $canteen->delete();

        return redirect()->route('manager.canteens.index')
            ->with('success', 'Kantin berhasil dihapus.');
    }

    // ─── Monitoring: semua menu & topping per kantin ─────────────────────────

    /** Monitoring semua menu dari semua kantin */
    public function menus(Request $request): View
    {
        $menus = Menu::with(['canteen.seller.user', 'toppings'])
            ->when($request->canteen, fn($q) => $q->where('canteen_id', $request->canteen))
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $canteens = Canteen::orderBy('canteen_name')->get(['id', 'canteen_name']);

        return view('pages.admin.manager.menus.index', compact('menus', 'canteens'));
    }

    /** Monitoring semua topping dari semua kantin */
    public function toppings(Request $request): View
    {
        $toppings = Topping::with(['canteen.seller.user', 'menus'])
            ->when($request->canteen, fn($q) => $q->where('canteen_id', $request->canteen))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $canteens = Canteen::orderBy('canteen_name')->get(['id', 'canteen_name']);

        return view('pages.admin.manager.toppings.index', compact('toppings', 'canteens'));
    }
}

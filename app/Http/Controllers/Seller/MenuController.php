<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Topping;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    private function authorizeCanteen(Canteen $canteen): void
    {
        $sellerCanteenIds = auth()->user()->canteens()->select('canteens.id')->pluck('canteens.id');
        abort_unless($sellerCanteenIds->contains($canteen->id), 403, 'Anda tidak berhak mengelola kantin ini.');
    }

    /** List menu per kantin */
    public function index(Canteen $canteen, Request $request): View
    {
        $this->authorizeCanteen($canteen);

        $menus = $canteen->menus()
            ->with('toppings')
            ->withCount('orderItems')
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = [
            'food'  => 'Makanan',
            'drink' => 'Minuman',
            'snack' => 'Cemilan',
        ];

        return view('pages.admin.seller.menus.index', compact('canteen', 'menus', 'categories'));
    }

    /** Form tambah menu dengan checkbox topping */
    public function create(Canteen $canteen): View
    {
        $this->authorizeCanteen($canteen);

        $toppings = $canteen->toppings()->orderBy('name')->get();
        $categories = [
            'food'  => 'Makanan',
            'drink' => 'Minuman',
            'snack' => 'Cemilan',
        ];

        return view('pages.admin.seller.menus.create', compact('canteen', 'toppings', 'categories'));
    }

    /** Simpan menu baru + attach topping yang dipilih ke pivot */
    public function store(Request $request, Canteen $canteen): RedirectResponse
    {
        $this->authorizeCanteen($canteen);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string', 'max:500'],
            'price'         => ['required', 'numeric', 'min:0'],
            'photo'         => ['nullable', 'image', 'max:2048'],
            'category'      => ['required', 'in:food,drink,snack'],
            'stock_type'    => ['required', 'in:counted,available'],
            'stock_qty'     => ['required_if:stock_type,counted', 'nullable', 'integer', 'min:0'],
            'is_available'  => ['nullable', 'boolean'],
            'topping_ids'   => ['nullable', 'array'],
            'topping_ids.*' => ['exists:toppings,id'],
        ]);

        $data = collect($validated)->only(['name', 'description', 'price', 'category', 'stock_type', 'stock_qty'])->all();
        $data['canteen_id'] = $canteen->id;
        $data['is_available'] = $request->boolean('is_available', true);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('menus', 'public');
        }

        $menu = Menu::create($data);

        if ($request->filled('topping_ids')) {
            $validIds = Topping::whereIn('id', $request->topping_ids)
                ->where('canteen_id', $canteen->id)
                ->pluck('id');
            $menu->toppings()->sync($validIds);
        }

        return redirect()->route('seller.canteens.menus.index', $canteen)
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    /** Form edit menu */
    public function edit(Canteen $canteen, Menu $menu): View
    {
        $this->authorizeCanteen($canteen);
        abort_if($menu->canteen_id !== $canteen->id, 403);

        $toppings = $canteen->toppings()->orderBy('name')->get();
        $selectedToppingIds = $menu->toppings->pluck('id')->all();
        $categories = [
            'food'  => 'Makanan',
            'drink' => 'Minuman',
            'snack' => 'Cemilan',
        ];

        return view('pages.admin.seller.menus.edit', compact('canteen', 'menu', 'toppings', 'selectedToppingIds', 'categories'));
    }

    /** Update menu + sync topping pivot */
    public function update(Request $request, Canteen $canteen, Menu $menu): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        abort_if($menu->canteen_id !== $canteen->id, 403);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string', 'max:500'],
            'price'         => ['required', 'numeric', 'min:0'],
            'photo'         => ['nullable', 'image', 'max:2048'],
            'category'      => ['required', 'in:food,drink,snack'],
            'stock_type'    => ['required', 'in:counted,available'],
            'stock_qty'     => ['required_if:stock_type,counted', 'nullable', 'integer', 'min:0'],
            'is_available'  => ['nullable', 'boolean'],
            'topping_ids'   => ['nullable', 'array'],
            'topping_ids.*' => ['exists:toppings,id'],
        ]);

        $data = collect($validated)->only(['name', 'description', 'price', 'category', 'stock_type', 'stock_qty'])->all();
        $data['is_available'] = $request->boolean('is_available', true);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('menus', 'public');
        }

        $menu->update($data);

        $validIds = $request->filled('topping_ids')
            ? Topping::whereIn('id', $request->topping_ids)->where('canteen_id', $canteen->id)->pluck('id')
            : collect();

        $menu->toppings()->sync($validIds);

        return back()->with('success', 'Menu berhasil diperbarui.');
    }

    /** Toggle available menu */
    public function toggleAvailability(Canteen $canteen, Menu $menu): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        abort_if($menu->canteen_id !== $canteen->id, 403);

        $menu->update(['is_available' => ! $menu->is_available]);
        $status = $menu->is_available ? 'tersedia' : 'tidak tersedia';

        return back()->with('success', "Menu sekarang {$status}.");
    }

    /** Hapus menu */
    public function destroy(Canteen $canteen, Menu $menu): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        abort_if($menu->canteen_id !== $canteen->id, 403);

        if ($menu->orderItems()->exists()) {
            return back()->with('error', 'Menu tidak bisa dihapus karena sudah pernah dipesan. Nonaktifkan saja dari daftar.');
        }

        $menu->toppings()->detach();
        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Topping;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToppingController extends Controller
{
    private function authorizeCanteen(Canteen $canteen): void
    {
        $sellerCanteenIds = auth()->user()->canteens()->select('canteens.id')->pluck('canteens.id');
        abort_unless($sellerCanteenIds->contains($canteen->id), 403, 'Anda tidak berhak mengelola kantin ini.');
    }

    /** List topping per kantin */
    public function index(Canteen $canteen): View
    {
        $this->authorizeCanteen($canteen);

        $toppings = $canteen->toppings()
            ->withCount('menus')
            ->latest()
            ->paginate(15);

        return view('pages.admin.seller.toppings.index', compact('canteen', 'toppings'));
    }

    /** Form tambah topping */
    public function create(Canteen $canteen): View
    {
        $this->authorizeCanteen($canteen);
        return view('pages.admin.seller.toppings.create', compact('canteen'));
    }

    /** Simpan topping baru */
    public function store(Request $request, Canteen $canteen): RedirectResponse
    {
        $this->authorizeCanteen($canteen);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'price'        => ['required', 'numeric', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $canteen->toppings()->create([
            'name'         => $validated['name'],
            'price'        => $validated['price'],
            'is_available' => $request->boolean('is_available', true),
        ]);

        return redirect()->route('seller.canteens.toppings.index', $canteen)
            ->with('success', 'Topping berhasil ditambahkan.');
    }

    /** Form edit topping */
    public function edit(Canteen $canteen, Topping $topping): View
    {
        $this->authorizeCanteen($canteen);
        abort_if($topping->canteen_id !== $canteen->id, 403);

        return view('pages.admin.seller.toppings.edit', compact('canteen', 'topping'));
    }

    /** Update topping */
    public function update(Request $request, Canteen $canteen, Topping $topping): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        abort_if($topping->canteen_id !== $canteen->id, 403);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'price'        => ['required', 'numeric', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $topping->update([
            'name'         => $validated['name'],
            'price'        => $validated['price'],
            'is_available' => $request->boolean('is_available', true),
        ]);

        return back()->with('success', 'Topping berhasil diperbarui.');
    }

    /** Toggle status available topping */
    public function toggleAvailability(Canteen $canteen, Topping $topping): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        abort_if($topping->canteen_id !== $canteen->id, 403);

        $topping->update(['is_available' => ! $topping->is_available]);
        $status = $topping->is_available ? 'tersedia' : 'tidak tersedia';

        return back()->with('success', "Topping sekarang {$status}.");
    }

    /** Hapus topping (detach pivot menu_toppings dulu) */
    public function destroy(Canteen $canteen, Topping $topping): RedirectResponse
    {
        $this->authorizeCanteen($canteen);
        abort_if($topping->canteen_id !== $canteen->id, 403);

        $topping->menus()->detach();
        $topping->delete();

        return back()->with('success', 'Topping berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Menu;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display cart page dengan list cart items
     */
    public function index()
    {
        $cartItems = CartItem::where('user_id', auth()->id())
            ->with(['menu.canteen', 'toppings'])
            ->get();

        // Kelompokkan per canteen untuk tampilan yang lebih jelas
        $groupedCartItems = $cartItems->groupBy(function ($item) {
            return $item->menu?->canteen_id ?? 0;
        });

        $canteenCount = $cartItems
            ->pluck('menu.canteen_id')
            ->filter()
            ->unique()
            ->count();

        $subtotal = $cartItems->sum(fn($item) => $item->subtotal);
        $serviceFee = $cartItems->isEmpty() ? 0 : 5000;
        $total = $subtotal + $serviceFee;
        $totalItems = $cartItems->sum('quantity');

        return view('pages.landingpage.cart.index', [
            'cartItems'       => $cartItems,
            'groupedCartItems'=> $groupedCartItems,
            'canteenCount'    => $canteenCount,
            'subtotal'        => $subtotal,
            'serviceFee'      => $serviceFee,
            'total'           => $total,
            'totalItems'      => $totalItems,
        ]);
    }

    /**
     * Add item to cart dengan support topping (untuk modal cart)
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'nullable|integer|min:1',
            'toppings' => 'nullable|array',
            'toppings.*' => 'exists:toppings,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $menu = Menu::findOrFail($validated['menu_id']);

        if (! $menu->isOrderable()) {
            return response()->json([
                'success' => false,
                'message' => 'Menu ini sedang tidak tersedia.',
            ], 422);
        }

        // Validasi bahwa toppings yang dipilih adalah dari menu ini
        $allowedToppingIds = $menu->toppings()->pluck('toppings.id')->toArray();
        $selectedToppingIds = $validated['toppings'] ?? [];

        $invalidToppings = array_diff($selectedToppingIds, $allowedToppingIds);
        if (!empty($invalidToppings)) {
            return response()->json([
                'success' => false,
                'message' => 'Topping yang dipilih tidak valid untuk menu ini.',
            ], 422);
        }

        // Find existing cart item with SAME menu + SAME toppings combination
        $cartItem = $this->findCartItemByMenuAndToppings(
            auth()->id(),
            $menu->id,
            $selectedToppingIds
        );

        if (!$cartItem) {
            // Create new cart item jika kombinasi menu+toppings belum ada
            $cartItem = CartItem::create([
                'user_id' => auth()->id(),
                'menu_id' => $menu->id,
                'quantity' => $validated['quantity'] ?? 1,
                'notes' => $validated['notes'] ?? '',
            ]);

            // Sync toppings
            if (!empty($selectedToppingIds)) {
                $cartItem->toppings()->sync($selectedToppingIds);
            }
        } else {
            // Update existing item - increase quantity dan update notes
            $cartItem->quantity += ($validated['quantity'] ?? 1);
            $cartItem->notes = $validated['notes'] ?? $cartItem->notes;
            $cartItem->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil ditambahkan ke keranjang.',
            'cart_count' => CartItem::where('user_id', auth()->id())->sum('quantity'),
        ]);
    }

    /**
     * Find cart item berdasarkan menu_id dan toppings combination
     * Jika ada item dengan menu sama tapi toppings berbeda, return null
     */
    private function findCartItemByMenuAndToppings($userId, $menuId, $selectedToppingIds = [])
    {
        $cartItems = CartItem::where('user_id', $userId)
            ->where('menu_id', $menuId)
            ->with('toppings')
            ->get();

        foreach ($cartItems as $item) {
            $itemToppingIds = $item->toppings->pluck('id')->toArray();
            sort($itemToppingIds);
            sort($selectedToppingIds);

            // Bandingkan apakah toppings sama
            if ($itemToppingIds === $selectedToppingIds) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Update cart item quantity dan toppings
     */
    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
            'toppings' => 'nullable|array',
            'toppings.*' => 'exists:toppings,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $menu = $cartItem->menu;
        
        // Validasi toppings
        $allowedToppingIds = $menu->toppings()->pluck('toppings.id')->toArray();
        $selectedToppingIds = $validated['toppings'] ?? [];

        $invalidToppings = array_diff($selectedToppingIds, $allowedToppingIds);
        if (!empty($invalidToppings)) {
            return response()->json([
                'success' => false,
                'message' => 'Topping yang dipilih tidak valid.',
            ], 422);
        }

        $cartItem->quantity = $validated['quantity'];
        $cartItem->notes = $validated['notes'] ?? '';
        $cartItem->save();

        if (!empty($selectedToppingIds)) {
            $cartItem->toppings()->sync($selectedToppingIds);
        } else {
            $cartItem->toppings()->detach();
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil diperbarui.',
            'cart_count' => CartItem::where('user_id', auth()->id())->sum('quantity'),
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang.',
            'cart_count' => CartItem::where('user_id', auth()->id())->sum('quantity'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $menu = Menu::findOrFail($validated['menu_id']);

        if (! $menu->isOrderable()) {
            return response()->json([
                'message' => 'Menu ini sedang tidak tersedia.',
            ], 422);
        }

        $cartItem = CartItem::firstOrNew([
            'user_id' => auth()->id(),
            'menu_id' => $menu->id,
        ]);

        $cartItem->quantity = ($cartItem->quantity ?? 0) + ($validated['quantity'] ?? 1);
        $cartItem->save();

        return response()->json([
            'message' => 'Berhasil ditambahkan ke keranjang.',
            'cart_count' => CartItem::where('user_id', auth()->id())->sum('quantity'),
        ]);
    }

    /**
     * Get cart count untuk badge di navbar
     */
    public function getCartCount()
    {
        $count = CartItem::where('user_id', auth()->id())->sum('quantity');
        return response()->json(['cart_count' => $count]);
    }
}

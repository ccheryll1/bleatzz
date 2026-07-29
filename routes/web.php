<?php

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/canteen', [LandingPageController::class, 'canteen'])->name('canteen.index');
Route::get('/canteen/{canteen}', [LandingPageController::class, 'canteenDetail'])->name('canteen.detail');
Route::get('/menu', [LandingPageController::class, 'menu'])->name('menu.index');

/* ─── API Routes ─── */
Route::prefix('api')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/transactions/{transaction}/status', [\App\Http\Controllers\Api\TransactionStatusController::class, 'show'])->name('api.transactions.status');
        Route::get('/transactions/pending', [\App\Http\Controllers\Api\TransactionStatusController::class, 'sellerPending'])->name('api.transactions.seller-pending');
        Route::get('/transactions/has-pending', [\App\Http\Controllers\Api\TransactionStatusController::class, 'hasPending'])->name('api.transactions.has-pending');
    });
    
    // Midtrans webhook — tidak perlu auth
    Route::post('/webhook/midtrans', [\App\Http\Controllers\Webhook\MidtransController::class, 'handle'])->name('webhook.midtrans');
});

Route::middleware('auth')->group(function () {
    Route::get('/favorite', [FavoriteController::class, 'index'])->name('favorite.index');
    Route::post('/favorite/{menu}/toggle', [FavoriteController::class, 'toggle'])->name('favorite.toggle');

    Route::get('/cart', [\App\Http\Controllers\Buyer\CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/count', [\App\Http\Controllers\Buyer\CartController::class, 'getCartCount'])->name('cart.count');
    Route::post('/cart/add', [\App\Http\Controllers\Buyer\CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cartItem}', [\App\Http\Controllers\Buyer\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [\App\Http\Controllers\Buyer\CartController::class, 'destroy'])->name('cart.destroy');

    // Buyer Routes
        Route::prefix('buyer')->name('buyer.')->group(function () {
            // Orders (Riwayat Pesanan)
            Route::get('/orders', [\App\Http\Controllers\Buyer\TransactionController::class, 'index'])->name('orders.index');

            // Transactions
            Route::get('/transactions', [\App\Http\Controllers\Buyer\TransactionController::class, 'index'])->name('transactions.index');
            Route::get('/transactions/history', [\App\Http\Controllers\Buyer\TransactionController::class, 'history'])->name('transactions.history');
            Route::get('/transactions/{transaction}', [\App\Http\Controllers\Buyer\TransactionController::class, 'show'])->name('transactions.show');
            Route::post('/transactions', [\App\Http\Controllers\Buyer\TransactionController::class, 'store'])->name('transactions.store');
            Route::post('/transactions/{transaction}/cancel', [\App\Http\Controllers\Buyer\TransactionController::class, 'cancel'])->name('transactions.cancel');
            Route::post('/transactions/{transaction}/confirm', [\App\Http\Controllers\Buyer\TransactionController::class, 'confirm'])->name('transactions.confirm');
            Route::post('/transactions/{transaction}/review', [\App\Http\Controllers\Buyer\ReviewController::class, 'store'])->name('transactions.review.store');
            Route::get('/transactions/{transaction}/spending', [\App\Http\Controllers\Buyer\TransactionController::class, 'spending'])->name('transactions.spending');

        // Payment routes
        Route::post('/transactions/{transaction}/payment/create', [\App\Http\Controllers\Buyer\PaymentController::class, 'create'])->name('transactions.payment.create');
        Route::get('/transactions/{transaction}/payment/status', [\App\Http\Controllers\Buyer\PaymentController::class, 'checkStatus'])->name('transactions.payment.status');
        Route::get('/transactions/{transaction}/payment/callback', [\App\Http\Controllers\Buyer\PaymentController::class, 'callback'])->name('transactions.payment.callback');
    });
});

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* ─── Manager Dashboard Routes ─── */
Route::prefix('manager')->name('manager.')->middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('manager.dashboard');
    });
    Route::get('/dashboard', function () {
        return view('pages.admin.manager.dashboard');
    })->name('dashboard');

    Route::resource('users', \App\Http\Controllers\Manager\UserController::class)->except(['show', 'destroy']);
    Route::post('/users/{user}/toggle-active', [\App\Http\Controllers\Manager\UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/reset-password', [\App\Http\Controllers\Manager\UserController::class, 'resetPassword'])->name('users.reset-password');

    Route::resource('canteens', \App\Http\Controllers\Manager\CanteenController::class);
    Route::get('/menus', [\App\Http\Controllers\Manager\CanteenController::class, 'menus'])->name('menus.index');
    Route::get('/toppings', [\App\Http\Controllers\Manager\CanteenController::class, 'toppings'])->name('toppings.index');
    Route::get('/reports', [\App\Http\Controllers\Manager\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Manager\ReportController::class, 'export'])->name('reports.export');
});

/* ─── Seller Dashboard Routes ─── */
Route::prefix('seller')->name('seller.')->middleware(['auth', 'verified', 'role:seller'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('seller.dashboard');
    });
    Route::get('/dashboard', function () {
        return view('pages.admin.seller.dashboard');
    })->name('dashboard');

    // Order Management
    Route::get('/orders', [\App\Http\Controllers\Seller\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{transaction}', [\App\Http\Controllers\Seller\OrderController::class, 'show'])->name('orders.show');

    // Keuangan — halaman pilih kantin (route finance.index)
    Route::get('/finance', [\App\Http\Controllers\Seller\TransactionController::class, 'chooseCanteen'])->name('finance.index');

    Route::resource('canteens', \App\Http\Controllers\Seller\CanteenController::class)->except(['create', 'store', 'destroy']);
    Route::post('/canteens/{canteen}/schedule', [\App\Http\Controllers\Seller\CanteenController::class, 'updateSchedule'])
        ->name('canteens.schedule.update');

    // Nested menus, toppings, dan keuangan per canteen
    Route::prefix('/canteens/{canteen}')->name('canteens.')->group(function () {
        Route::resource('menus', \App\Http\Controllers\Seller\MenuController::class);
        Route::post('/menus/{menu}/toggle', [\App\Http\Controllers\Seller\MenuController::class, 'toggleAvailability'])
            ->name('menus.toggle');

        Route::resource('toppings', \App\Http\Controllers\Seller\ToppingController::class);
        Route::post('/toppings/{topping}/toggle', [\App\Http\Controllers\Seller\ToppingController::class, 'toggleAvailability'])
            ->name('toppings.toggle');

        // ─── Keuangan / Transaksi ───
        Route::get('/finance', [\App\Http\Controllers\Seller\TransactionController::class, 'index'])->name('finance.index');
        Route::get('/finance/export', [\App\Http\Controllers\Seller\TransactionController::class, 'export'])->name('finance.export');

        Route::get('/transactions/{transaction}', [\App\Http\Controllers\Seller\TransactionController::class, 'show'])
            ->name('transactions.show');
        Route::post('/transactions/{transaction}/accept',   [\App\Http\Controllers\Seller\TransactionController::class, 'accept'])
            ->name('transactions.accept');
        Route::post('/transactions/{transaction}/reject',   [\App\Http\Controllers\Seller\TransactionController::class, 'reject'])
            ->name('transactions.reject');
        Route::post('/transactions/{transaction}/process',  [\App\Http\Controllers\Seller\TransactionController::class, 'process'])
            ->name('transactions.process');
        Route::post('/transactions/{transaction}/ready',    [\App\Http\Controllers\Seller\TransactionController::class, 'ready'])
            ->name('transactions.ready');
        Route::post('/transactions/{transaction}/done',     [\App\Http\Controllers\Seller\TransactionController::class, 'done'])
            ->name('transactions.done');
        Route::post('/transactions/{transaction}/cancel-approve', [\App\Http\Controllers\Seller\TransactionController::class, 'approveCancellation'])
            ->name('transactions.cancel-approve');
        Route::post('/transactions/{transaction}/cancel-reject',  [\App\Http\Controllers\Seller\TransactionController::class, 'rejectCancellation'])
            ->name('transactions.cancel-reject');
    });
});

require __DIR__.'/auth.php';

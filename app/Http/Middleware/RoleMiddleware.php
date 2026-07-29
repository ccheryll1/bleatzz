<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (! $user) {
            return $request->expectsJson()
                ? abort(401)
                : redirect()->guest(route('login'));
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.',
            ]);
        }

        if (! in_array($user->role, $roles, true)) {
            $fallback = match (true) {
                $user->isManager() => redirect()->route('manager.dashboard'),
                $user->isSeller()  => redirect()->route('seller.dashboard'),
                default            => redirect()->route('home'),
            };

            if ($request->expectsJson()) {
                abort(403, 'Akses ditolak.');
            }

            /** @var RedirectResponse $fallback */
            return $fallback->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }

        return $next($request);
    }
}
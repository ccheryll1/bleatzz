<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('login.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'min:5', 'max:32', 'unique:'.User::class,
                'alpha_dash:ascii', 'regex:/^\S*$/u',
            ],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->letters()->mixedCase()->numbers()],
            'agree'    => ['required', 'accepted'],
        ], [
            'username.alpha_dash' => 'Username hanya boleh mengandung huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.regex'      => 'Username tidak boleh mengandung spasi.',
            'username.min'        => 'Username minimal :min karakter.',
            'username.max'        => 'Username maksimal :max karakter.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'buyer',
        ]);

        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with('status', 'PENDAFTARAN BERHASIL! Silakan login dengan akun baru Anda.');
    }
}

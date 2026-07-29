<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /** Daftar semua user (buyer & seller) */
    public function index(Request $request): View
    {
        $users = User::whereIn('role', ['buyer', 'seller'])
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->search, fn($q) => $q->where(function ($subq) use ($request) {
                $subq->where('name', 'like', "%{$request->search}%")
                    ->orWhere('username', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalBuyers = User::where('role', 'buyer')->count();
        $totalSellers = User::where('role', 'seller')->count();
        $totalActive = User::whereIn('role', ['buyer', 'seller'])->where('is_active', true)->count();

        return view('pages.admin.manager.users.index', compact('users', 'totalBuyers', 'totalSellers', 'totalActive'));
    }

    /** Form buat akun seller baru (buyer bisa register sendiri) */
    public function create(): View
    {
        return view('pages.admin.manager.users.create');
    }

    /** Simpan akun seller baru */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'seller',
            'is_active' => true,
        ]);

        return redirect()->route('manager.users.index')
            ->with('success', 'Akun seller berhasil dibuat.');
    }

    /** Form edit user: ubah role buyer -> seller / seller -> buyer */
    public function edit(User $user): View
    {
        abort_if($user->isManager(), 403);

        return view('pages.admin.manager.users.edit', compact('user'));
    }

    /** Update user: ubah role buyer <-> seller */
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isManager(), 403);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => ['required', 'in:buyer,seller'],
        ]);

        $user->update($validated);

        return redirect()->route('manager.users.index')
            ->with('success', "Data user {$user->name} berhasil diperbarui.");
    }

    /** Nonaktifkan / aktifkan akun user */
    public function toggleActive(User $user): RedirectResponse
    {
        abort_if($user->isManager(), 403);

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    /** Reset password user */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isManager(), 403);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil direset.');
    }
}

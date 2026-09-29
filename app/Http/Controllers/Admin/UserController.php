<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = auth()->user()->isAdmin() ? User::latest()->get() : collect();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:'.User::class],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
            'role'     => ['required', 'in:admin,pic_jpp,super_vc,staff'],
        ]);

        User::create([
            'name'     => $request->name,
            'username' => strtolower($request->username),
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->back()->with('success', 'Akun berhasil dibuat!');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Proteksi: Cegah admin menghapus akunnya sendiri yang sedang login
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
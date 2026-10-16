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

        $totalUsers = $users->count();
        $twoFactorCount = $users->filter(fn($u) => !empty($u->google2fa_secret))->count();
        $twoFactorPercent = $totalUsers > 0 ? round(($twoFactorCount / $totalUsers) * 100) : 0;

        return view('admin.users.index', compact('users', 'totalUsers', 'twoFactorCount', 'twoFactorPercent'));
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

        $newUser = User::create([
            'name'     => $request->name,
            'username' => strtolower($request->username),
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        \App\Models\SystemLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'CREATE USER',
            'description' => "Menambahkan akun baru: {$newUser->name} ({$newUser->email}) sebagai {$newUser->role}.",
            'ip_address'  => request()->ip()
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

        $deletedName = $user->name;
        $deletedEmail = $user->email;
        
        $user->delete();

        \App\Models\SystemLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'DELETE USER',
            'description' => "Menghapus akun: {$deletedName} ({$deletedEmail}).",
            'ip_address'  => request()->ip()
        ]);

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    public function reset2FA(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if (empty($user->google2fa_secret)) {
            return back()->with('warning', "Akun {$user->name} belum mengaktifkan 2FA.");
        }

        $userName = $user->name;
        $userEmail = $user->email;

        $user->google2fa_secret = null;
        $user->save();

        if (auth()->id() === $user->id) {
            request()->session()->forget('2fa_passed');
        }

        \App\Models\SystemLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'RESET 2FA USER',
            'description' => "Mereset kunci Two-Factor Authentication (2FA) untuk akun: {$userName} ({$userEmail}).",
            'ip_address'  => request()->ip()
        ]);

        return back()->with('success', "Autentikasi 2FA untuk akun {$userName} berhasil direset.");
    }
}
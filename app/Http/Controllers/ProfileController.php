<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        \App\Models\SystemLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'UPDATE PROFILE',
            'description' => "Memperbarui informasi profil.",
            'ip_address'  => request()->ip()
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $deletedName = $user->name;
        $deletedEmail = $user->email;
        $deletedId = $user->id;

        Auth::logout();

        $user->delete();

        \App\Models\SystemLog::create([
            'user_id'     => null, // Akun sudah dihapus, atau bisa simpan ID lama
            'action'      => 'DELETE ACCOUNT',
            'description' => "Pengguna menghapus akunnya sendiri: {$deletedName} ({$deletedEmail}).",
            'ip_address'  => request()->ip()
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    // Halaman untuk memasukkan kode OTP saat login
    public function showVerifyForm()
    {
        return view('auth.2fa_verify');
    }

    // Proses validasi OTP saat login
    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);

        $user = Auth::user();
        $google2fa = new Google2FA();
        
        // Cek toleransi waktu (window = 1 artinya toleransi 30 detik sebelum dan sesudah)
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->otp, 1);

        if ($valid) {
            $request->session()->put('2fa_passed', true);
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['otp' => 'Kode OTP salah atau kedaluwarsa.']);
    }

    // Halaman setup 2FA untuk admin
    public function setup(Request $request)
    {
        $user = Auth::user();
        $google2fa = new Google2FA();
        
        $secret = $user->google2fa_secret;
        
        if (!$secret) {
            if (!$request->session()->has('2fa_setup_secret')) {
                $secret = $google2fa->generateSecretKey();
                $request->session()->put('2fa_setup_secret', $secret);
            } else {
                $secret = $request->session()->get('2fa_setup_secret');
            }
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(250),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('admin.2fa_setup', [
            'qrCodeSvg' => $qrCodeSvg,
            'secret' => $secret,
            'isEnabled' => !empty($user->google2fa_secret)
        ]);
    }

    // Proses menyimpan secret key saat setup
    public function enable(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);
        
        $user = Auth::user();
        $secret = $request->session()->get('2fa_setup_secret');
        
        if (!$secret) {
            return back()->with('error', 'Sesi setup habis, silakan muat ulang halaman.');
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($secret, $request->otp, 1);

        if ($valid) {
            $user->google2fa_secret = $secret;
            $user->save();
            
            $request->session()->forget('2fa_setup_secret');
            $request->session()->put('2fa_passed', true);
            
            return back()->with('success', 'Two-Factor Authentication berhasil diaktifkan!');
        }

        return back()->withErrors(['otp' => 'Kode OTP salah.']);
    }

    // Menonaktifkan 2FA
    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->google2fa_secret = null;
        $user->save();
        $request->session()->forget('2fa_passed');

        return back()->with('success', 'Two-Factor Authentication telah dinonaktifkan.');
    }
}

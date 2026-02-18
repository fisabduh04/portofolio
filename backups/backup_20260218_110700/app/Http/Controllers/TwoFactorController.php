<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TwoFactorController extends Controller
{
    public function showSetup(Request $request)
    {
        $user = $request->user();

        // keamanan tambahan (walau sudah dipagari route)
        if (!in_array($user->role, ['admin', 'operator', 'kepala'], true)) {
            abort(403);
        }

        $google2fa = app('pragmarx.google2fa');

        // generate secret baru jika belum ada
        if (!$user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            session(['2fa_secret_temp' => $secret]);
        } else {
            $secret = Crypt::decryptString($user->two_factor_secret);
            session(['2fa_secret_temp' => $secret]);
        }

        $qrImage = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('login.2fa-setup', compact('qrImage'));
    }

    public function enable(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'operator', 'kepala'], true)) {
            abort(403);
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $secret = session('2fa_secret_temp');
        if (!$secret) {
            return back()->withErrors(['code' => 'Secret 2FA tidak ditemukan. Ulangi proses.']);
        }

        $google2fa = app('pragmarx.google2fa');

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Kode 2FA salah.']);
        }

        // simpan secret terenkripsi
        $user->two_factor_secret = Crypt::encryptString($secret);
        $user->two_factor_confirmed_at = now();

        // buat recovery codes
        $codes = collect(range(1, 8))
            ->map(fn () => bin2hex(random_bytes(4)))
            ->toArray();

        $user->two_factor_recovery_codes = Crypt::encryptString(json_encode($codes));
        $user->save();

        session()->forget('2fa_secret_temp');

        return redirect()->route('home')
            ->with('success', 'Two-Factor Authentication berhasil diaktifkan.');
    }

    public function disable(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'operator', 'kepala'], true)) {
            abort(403);
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        $request->session()->forget('2fa_passed');

        return back()->with('success', '2FA berhasil dinonaktifkan.');
    }
}

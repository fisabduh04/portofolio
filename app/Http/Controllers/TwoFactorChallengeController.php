<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TwoFactorChallengeController extends Controller
{
    public function show()
    {
        return view('login.2fa-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required'],
        ]);

        $user = $request->user();

        $google2fa = app('pragmarx.google2fa');
        $secret = $user->two_factor_secret
            ? Crypt::decryptString($user->two_factor_secret)
            : null;

        if (!$secret) {
            return redirect()->route('2fa.setup');
        }

        $input = trim($request->code);

        // 6 digit TOTP
        if (ctype_digit($input) && strlen($input) === 6) {
            if (!$google2fa->verifyKey($secret, $input)) {
                return back()->withErrors(['code' => 'Kode 2FA salah.']);
            }
        } else {
            // recovery code
            $codes = json_decode(
                Crypt::decryptString($user->two_factor_recovery_codes),
                true
            ) ?? [];

            if (!in_array($input, $codes, true)) {
                return back()->withErrors(['code' => 'Kode recovery tidak valid.']);
            }

            // hapus recovery code yang dipakai
            $codes = array_values(array_diff($codes, [$input]));
            $user->two_factor_recovery_codes = Crypt::encryptString(json_encode($codes));
            $user->save();
        }

        // tandai 2FA lulus untuk session ini
        $request->session()->put('2fa_passed', true);

        return redirect()->route('home');
    }
}

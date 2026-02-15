<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // 1. Custom Check: User Active?
        $user = User::where('email', $request->email)->first();

        // If user exists AND is inactive (0)
        if ($user && (int) $user->is_active === 0) {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda belum aktif. Tidak dapat mereset kata sandi.'],
            ]);
        }

        // 2. Standard Fortify Logic (Send Reset Link)
        // We use the Password Broker to send the link
        $status = Password::broker(config('fortify.passwords'))->sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}

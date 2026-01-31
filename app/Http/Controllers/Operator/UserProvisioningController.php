<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;


class UserProvisioningController extends Controller
{
    private const ROLES = ['admin', 'operator', 'guru', 'siswa', 'kepala'];

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $pegawais = Pegawai::query()
            ->with('user')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('nuptk', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('operator.users.index', compact('pegawais', 'q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pegawai_id' => ['required', 'integer', 'exists:pegawais,id'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:' . implode(',', self::ROLES)],
        ]);

        $pegawai = Pegawai::findOrFail($data['pegawai_id']);

        if (User::where('pegawai_id', $pegawai->id)->exists()) {
            return back()
                ->withErrors(['pegawai_id' => 'Pegawai ini sudah memiliki akun user.'])
                ->withInput();
        }

        $user = User::create([
            'pegawai_id' => $pegawai->id,
            'name' => $pegawai->name ?? 'Pegawai',
            'email' => strtolower(trim($data['email'])),
            'password' => bcrypt(Str::random(40)),
            'role' => $data['role'],
            'is_active' => 0, // ✅ dibuat nonaktif dulu
        ]);

        return redirect()
            ->route('operator.users.index')
            ->with('success', "Akun {$user->role} dibuat (nonaktif). Aktifkan untuk mengirim link set password.");
    }


    public function resendReset(Request $request)
    {
    $request->validate([
        'email' => ['required', 'email', 'exists:users,email'],
    ]);

    $user = User::where('email', $request->email)->firstOrFail();

    if (! $user->is_active) {
        return back()->with('warning', 'Akun masih nonaktif. Aktifkan akun dulu sebelum kirim reset.');
    }

    $status = Password::sendResetLink(['email' => $user->email]);

    if ($status !== Password::RESET_LINK_SENT) {
        return back()->with('warning', 'Gagal mengirim reset. Cek konfigurasi email.');
    }

    return back()->with('success', 'Link reset password berhasil dikirim ulang.');
    }


    public function toggleActive(User $user)
    {
        // 1) Cegah nonaktifkan diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('warning', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        // 2) Jika yang login OPERATOR, jangan boleh nonaktifkan ADMIN
        if (auth()->user()->role === 'operator' && $user->role === 'admin') {
            return back()->with('warning', 'Operator tidak boleh menonaktifkan akun admin.');
        }

        // ✅ cek apakah sebelumnya nonaktif
        $wasInactive = (int) $user->is_active === 0;

        // toggle
        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        // ✅ hanya saat BARU DI-AKTIFKAN: kirim reset link
        if ($wasInactive && (int) $user->is_active === 1) {
            $status = Password::sendResetLink(['email' => $user->email]);

            if ($status !== Password::RESET_LINK_SENT) {
                return back()->with('warning', 'Akun aktif, tapi email set password gagal terkirim. Cek MAIL.');
            }

            return back()->with('success', 'Akun berhasil diaktifkan. Link set password sudah dikirim ke email.');
        }

        return back()->with('success', 'Status akun berhasil diperbarui.');
    }


    
    public function updateRole(Request $request, User $user)
{
    $data = $request->validate([
        'role' => ['required', 'in:admin,operator,guru,siswa,kepala'],
    ]);

    // 1) Cegah ubah role diri sendiri
    if ($user->id === auth()->id()) {
        return back()->with('warning', 'Anda tidak dapat mengubah role akun sendiri.');
    }

    // 2) Jika yang login OPERATOR, jangan boleh mengubah role ADMIN
    if (auth()->user()->role === 'operator' && $user->role === 'admin') {
        return back()->with('warning', 'Operator tidak boleh mengubah role admin.');
    }

    $user->update([
        'role' => $data['role'],
    ]);

    return back()->with('success', 'Role user berhasil diperbarui.');
}



}

<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Policies\UserPolicy; 

class UserProvisioningController extends Controller
{
    private const ROLES = ['admin', 'operator', 'guru', 'siswa', 'kepala', 'staff'];

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
            ->when($request->filter_role, function ($query) use ($request) {
                 $query->whereHas('user', function ($q) use ($request) {
                     $q->where('role', $request->filter_role);
                 });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('operator.users.index', compact('pegawais', 'q'));
    }

    /**
     * Menyimpan Data User Baru (Create Account).
     *
     * Alur Logika:
     * 1. Validasi input (email unik, role valid).
     * 2. Cek Otorisasi: Apakah user yang login punya wewenang membuat role tersebut?
     *    (Misal: Operator tidak boleh membuat akun Admin).
     * 3. Buat user dengan status default Non-Aktif (0).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'pegawai_id' => ['required', 'integer', 'exists:pegawais,id'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'       => ['required', 'in:' . implode(',', self::ROLES)],
        ]);

        // CEK WEWENANG (Authorization)
        // Kita hitung rank role yang mau dibuat vs rank user yang login.
        $targetRank = $this->getRank($data['role']);
        $myRank     = $this->getRank($request->user()->role);

        // Jika Rank saya lebih kecil dari Rank yang mau dibuat, TOLAK.
        // Kecuali saya adalah 'kepala'.
        if ($myRank < $targetRank && $request->user()->role !== 'kepala') {
             return back()->with('error', 'Anda tidak memiliki wewenang untuk membuat user dengan role lebih tinggi dari Anda.');
        }

        // Cek apakah pegawai ini sudah punya akun sebelumnya?
        $pegawai = Pegawai::findOrFail($data['pegawai_id']);

        if (User::where('pegawai_id', $pegawai->id)->exists()) {
            return back()
                ->withErrors(['pegawai_id' => 'Pegawai ini sudah memiliki akun user.'])
                ->withInput();
        }

        // Buat User Baru
        $user = User::create([
            'pegawai_id' => $pegawai->id,
            'name'       => $pegawai->name ?? 'Pegawai',
            'email'      => strtolower(trim($data['email'])),
            'password'   => bcrypt(Str::random(40)), // Password acak karena nanti akan di-reset user
            'role'       => $data['role'],
            'is_active'  => 0, // Default inactive (Harus diaktifkan manual agar kirim email)
        ]);

        return redirect()
            ->route('operator.users.index')
            ->with('success', "Akun {$user->role} berhasil dibuat (Status: Nonaktif). Silakan aktifkan untuk mengirim email password.");
    }

    /**
     * Mengubah Status Aktif/Nonaktif (Switch ON/OFF).
     *
     * Fitur Penting:
     * - Jika akun diubah dari Non-Aktif menjadi Aktif, sistem OTOMATIS mengirim email reset password.
     */
    public function toggleActive(User $user)
    {
        // 1. Cek Policy: Apakah saya boleh mengelola user ini?
        if (Gate::denies('toggleStatus', $user)) {
             return back()->with('error', 'Tindakan Ditolak: Anda tidak memiliki akses untuk mengubah status pengguna ini.');
        }

        // Simpan status lama untuk pengecekan
        $wasInactive = (int) $user->is_active === 0;

        // Update status kebalikan (Toggle)
        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        // JIKA status berubah jadi AKTIF, kirim email reset password
        if ($wasInactive && (int) $user->is_active === 1) {
            try {
                $status = Password::sendResetLink(['email' => $user->email]);
                
                if ($status !== Password::RESET_LINK_SENT) {
                    return back()->with('success', 'Akun aktif, namun gagal mengirim email reset password. Silakan coba fitur "Resend Reset".');
                }
                return back()->with('success', 'Akun diaktifkan! Link atur password telah dikirim ke email pegawai.');
            } catch (\Exception $e) {
                // Tangkap error jika konfigurasi email di server (Hostinger) salah/belum diatur
                \Illuminate\Support\Facades\Log::error('Aktivasi User - Gagal mengirim email: ' . $e->getMessage());
                return back()->with('warning', 'Akun berhasil diaktifkan, namun sistem gagal mengirim email reset password. Pastikan konfigurasi SMTP email (.env) sudah benar.');
            }
        }

        return back()->with('success', 'Status akun berhasil diperbarui.');
    }

    /**
     * Mengubah Role (Jabatan) User.
     * Contoh: Mengangkat Guru menjadi Operator.
     */
    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:' . implode(',', self::ROLES)],
        ]);

        // 1. Cek Policy Dasar: Boleh gak saya edit user ini?
        if (Gate::denies('updateRole', $user)) {
             return back()->with('error', 'Tindakan Ditolak: Anda tidak bisa mengubah role pengguna ini.');
        }

        // 2. Cek Policy Lanjutan: Boleh gak saya mengangkat dia ke role baru ini?
        // (Saya tidak boleh mengangkat orang jadi atasan saya).
        $newRoleRank = $this->getRank($data['role']);
        $myRank      = $this->getRank($request->user()->role);

        if ($request->user()->role !== 'kepala' && $myRank < $newRoleRank) {
             return back()->with('error', 'Tindakan Ditolak: Anda tidak bisa menaikkan role ke tingkat yang lebih tinggi dari Anda.');
        }

        $user->update(['role' => $data['role']]);

        return back()->with('success', 'Role pengguna berhasil diperbarui.');
    }

    /**
     * Mengirim Ulang Link Reset Password.
     * Digunakan jika email pertama tidak sampai atau kadaluarsa.
     */
    public function resendReset(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        // Security Check
        if (Gate::denies('manage', $user)) {
             return back()->with('error', 'Anda tidak berwenang mengelola pengguna ini.');
        }

        // Pastikan akun aktif dulu
        if (! $user->is_active) {
            return back()->with('warning', 'Akun masih nonaktif. Harap aktifkan terlebih dahulu.');
        }

        // Kirim Email via Laravel Fortify/Password Broker
        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            return back()->with('error', 'Gagal mengirim link reset. Periksa konfigurasi email.');
        }

        return back()->with('success', 'Link reset password berhasil dikirim ulang.');
    }

    // Helper sederhana untuk mengubah Role string (teks) menjadi angka (rank)
    private function getRank(string $role): int
    {
        return match ($role) {
            'kepala'   => 4,
            'admin'    => 3,
            'operator' => 2,
            'guru'     => 1,
            'staff'    => 1,
            'siswa'    => 1,
            default    => 0,
        };
    }
}

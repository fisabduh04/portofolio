<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SekolahController extends Controller
{
    /**
     * Menampilkan halaman identitas sekolah.
     * Karena sekolah hanya satu entitas, kita ambil data pertama atau buat baru jika kosong.
     */
    public function index()
    {
        // 1. Otorisasi: Hanya Admin, Kepala Sekolah, dan Operator yang boleh akses halaman ini
        if (!in_array(auth()->user()->role, ['admin', 'kepala', 'operator'])) {
            abort(403, 'Anda tidak memiliki akses untuk melihat konfigurasi sekolah.');
        }

        $sekolah = Sekolah::first();

        // Jika belum ada data, kita kirim object kosong/baru
        if (!$sekolah) {
            $sekolah = new Sekolah();
        }

        return view('sekolah.index', compact('sekolah'));
    }

    /**
     * Menyimpan atau Memperbarui data sekolah
     */
    public function store(Request $request)
    {
        // 1. Otorisasi: Hanya Admin, Kepala Sekolah, dan Operator yang boleh mengubah data
        if (!in_array(auth()->user()->role, ['admin', 'kepala', 'operator'])) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data sekolah.');
        }

        // 2. Validasi Ketat (MIME Types & Sanitasi)
        // Kita gunakan strip_tags untuk membersihkan input text dari potensi XSS
        $input = $request->all();
        array_walk_recursive($input, function (&$input) {
            $input = strip_tags($input);
        });
        $request->merge($input);

        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'required|numeric|digits:8', 
            'status_sekolah' => 'required|in:Negeri,Swasta',
            'bentuk_pendidikan' => 'nullable|string',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email',
            'no_telp' => 'nullable|string',
            'lintang' => 'nullable|numeric',
            'bujur' => 'nullable|numeric',
            // File uploads dengan validasi MIME yang ketat
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
            'kop_surat' => 'nullable|image|mimes:jpeg,png,jpg,png|max:2048',
        ]);

        // 3. Database Transaction untuk Data Integrity
        try {
            DB::transaction(function () use ($request, $validated) {
                // Cek data lama
                $sekolah = Sekolah::first();
                if (!$sekolah) {
                    $sekolah = new Sekolah();
                }
        
                // Handle File Upload Logo
                if ($request->hasFile('logo')) {
                    if ($sekolah->logo) {
                        Storage::delete($sekolah->logo);
                    }
                    $validated['logo'] = $request->file('logo')->store('sekolah-images', 'public');
                }
        
                // Handle File Upload Kop Surat
                if ($request->hasFile('kop_surat')) {
                     if ($sekolah->kop_surat) {
                        Storage::delete($sekolah->kop_surat);
                    }
                    $validated['kop_surat'] = $request->file('kop_surat')->store('sekolah-images', 'public');
                }
        
                // Update / Create
                $data = $request->except(['logo', 'kop_surat', '_token', '_method']);
                
                // Perbaikan: Konversi string kosong menjadi NULL agar tidak error di Database (terutama kolom decimal/numeric)
                $data = array_map(function($value) {
                    return $value === '' ? null : $value;
                }, $data);

                // Merge file paths
                if (isset($validated['logo'])) $data['logo'] = $validated['logo'];
                if (isset($validated['kop_surat'])) $data['kop_surat'] = $validated['kop_surat'];
        
                // --- LANGKAH DETIL SINKRONISASI CACHE ---
                
                // 1. Simpan perubahan ke Database
                if ($sekolah->exists) {
                    $sekolah->update($data);
                } else {
                    $sekolah = Sekolah::create($data);
                }
        
                // 2. Hapus Cache (Reset)
                Cache::forget('global_sekolah_data');
            });

            return redirect()->route('sekolah.index')->with('success', 'Data Sekolah berhasil diperbarui dan diamankan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }
}

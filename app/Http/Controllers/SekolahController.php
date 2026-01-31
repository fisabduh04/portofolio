<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SekolahController extends Controller
{
    /**
     * Menampilkan halaman identitas sekolah.
     * Karena sekolah hanya satu entitas, kita ambil data pertama atau buat baru jika kosong.
     */
    public function index()
    {
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
        // Validasi input sesuai standar Dapodik
        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'required|numeric|digits:8', // Dapodik: 8 digit angka
            'status_sekolah' => 'required|in:Negeri,Swasta',
            'bentuk_pendidikan' => 'nullable|string',
            'alamat' => 'required|string',
            'email' => 'required|email',
            'no_telp' => 'nullable|string',
            'lintang' => 'nullable|numeric',
            'bujur' => 'nullable|numeric',
            // File uploads
            'logo' => 'nullable|image|max:2048', // Max 2MB
            'kop_surat' => 'nullable|image|max:2048',
        ]);

        // Cek data lama
        $sekolah = Sekolah::first();
        if (!$sekolah) {
            $sekolah = new Sekolah();
        }

        // Handle File Upload Logo
        if ($request->hasFile('logo')) {
            // Hapus lama jika ada
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
        // Kita gunakan fill untuk mass assignment ke semua field yang dikirim (selain file yang sudah dihandle manual)
        // Pastikan field lain di request yang tidak tervalidasi tapi ada di database masuk ke $validated atau request->all()
        // Untuk aman, gunakan $request->except(['logo', 'kop_surat', '_token']) + path file
        
        $data = $request->except(['logo', 'kop_surat', '_token', '_method']);
        // Merge file paths
        if (isset($validated['logo'])) $data['logo'] = $validated['logo'];
        if (isset($validated['kop_surat'])) $data['kop_surat'] = $validated['kop_surat'];

        // Simpan
        if ($sekolah->exists) {
            $sekolah->update($data);
        } else {
            $sekolah = Sekolah::create($data);
        }

        // Hapus cache agar data global terupdate otomatis
        \Illuminate\Support\Facades\Cache::forget('sekolah_data');

        return redirect()->route('sekolah.index')->with('success', 'Data Sekolah berhasil diperbarui.');
    }
}

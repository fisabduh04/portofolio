<?php

namespace App\Livewire\Tahun;

use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\JadwalPiket;
use App\Models\KelasSiswa;
use App\Models\PegawaiRuleAllocation;
use App\Models\PegawaiWajibHadir;
use App\Models\Tahun;
use App\Models\WaliKelas;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Data extends Component
{
    public $form = false;

    public $tanggalmulai;

    public $tanggalakhir;

    public $tahun;

    public $semester;

    public $tahunID;

    public $tombol_simpan = 'Simpan';

    public $isActive = 0;

    public $tahunlist;

    public function mount()
    {
        $this->loadTahunList();
    }

    private function loadTahunList()
    {
        $this->tahunlist = Tahun::all();
    }

    public function toggleIsActive($id)
    {
        $tahun = Tahun::find($id);
        if ($tahun) {
            $tahun->isActive = ! $tahun->isActive;
            $tahun->save();
        }
        session()->flash('warning', 'Status keaktifan berhasil diubah');
        $this->loadTahunList(); // Refresh the list
    }

    public function render()
    {
        return view('livewire.tahun.data', [
            'tahunlist' => $this->tahunlist,
        ]);
    }

    public function tambah()
    {
        $this->form = true;
    }

    public function edit($id)
    {
        // dd($id);
        $tahun = Tahun::find($id);
        $this->tahunID = $tahun->id;
        $this->tanggalmulai = $tahun->tanggalmulai;
        $this->tanggalakhir = $tahun->tanggalakhir;
        $this->tahun = $tahun->tahun;
        $this->semester = $tahun->semester;
        $this->tombol_simpan = 'Update';
        $this->isActive = $tahun->isActive;
        $this->form = true;
    }

    public function close()
    {
        $this->form = false;
    }

    public function store()
    {
        $this->validate([
            'tahun' => 'required|string',
            'semester' => 'required|string',
            'tanggalmulai' => 'nullable|date',
            'tanggalakhir' => 'nullable|date|after_or_equal:tanggalmulai',
            'isActive' => 'required|boolean|in:0,1',
        ], [
            'tahun.required' => 'Tahun ajaran harus diisi.',
            'semester.required' => 'Semester harus dipilih.',
            'isActive.required' => 'Status keaktifan harus ditentukan.',
            'tanggalmulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggalakhir.date' => 'Format tanggal akhir tidak valid.',
        ]);

        try {
            Tahun::updateOrCreate([
                'id' => $this->tahunID,
            ], [
                'tahun' => $this->tahun,
                'semester' => $this->semester,
                'tanggalmulai' => $this->tanggalmulai,
                'tanggalakhir' => $this->tanggalakhir,
                'isActive' => $this->isActive,
            ]);
            session()->flash('success', 'Data berhasil diinput');

            $this->reset(['tanggalmulai', 'tanggalakhir', 'tahun', 'semester', 'isActive']);
            $this->loadTahunList(); // Refresh the list
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1062) { // Cek kode error MySQL untuk duplikasi
                Session::flash('error', 'Data tahun dengan tahun dan semester tersebut sudah ada.');
                // Opsi tambahan:
                $this->addError('tahun', 'Data tahun dengan tahun dan semester tersebut sudah ada.'); // Menampilkan error di bawah input field
            } else {
                // Tangani error lain jika ada
                Session::flash('error', 'Terjadi kesalahan saat menyimpan data: '.$e->getMessage());
            }
        }
        $this->form = false;
    }

    public function del(int $id): void
    {
        Gate::authorize('manage-data-master');
        abort_unless(auth()->user()->is_active, 403);

        try {
            $result = DB::transaction(function () use ($id): Tahun|string {
                $tahun = Tahun::whereKey($id)->lockForUpdate()->first();

                if (! $tahun) {
                    return 'Tahun ajaran tidak ditemukan atau sudah dihapus.';
                }

                if ($tahun->isActive) {
                    return 'Tahun ajaran aktif tidak dapat dihapus. Pindahkan tahun aktif terlebih dahulu.';
                }

                $dependencies = [
                    'jadwal mengajar' => Jadwal::class,
                    'penempatan siswa' => KelasSiswa::class,
                    'jadwal piket' => JadwalPiket::class,
                    'hari libur' => HariLibur::class,
                    'pengaturan wajib hadir' => PegawaiWajibHadir::class,
                    'penetapan aturan absensi' => PegawaiRuleAllocation::class,
                    'penugasan wali kelas' => WaliKelas::class,
                ];
                $usage = [];

                foreach ($dependencies as $label => $model) {
                    $count = $model::where('tahun_id', $tahun->id)->count();

                    if ($count > 0) {
                        $usage[] = $count.' '.$label;
                    }
                }

                if ($usage !== []) {
                    return 'Tahun ajaran tidak dapat dihapus karena masih digunakan oleh '.implode(', ', $usage).'.';
                }

                $tahun->delete();

                return $tahun;
            });
        } catch (QueryException $exception) {
            report($exception);
            $this->dispatch('showToast', message: 'Tahun ajaran gagal dihapus. Silakan muat ulang halaman; jika masih gagal, hubungi administrator.', type: 'error');

            return;
        }

        if (is_string($result)) {
            $this->dispatch('showToast', message: $result, type: 'warning');
            $this->loadTahunList();

            return;
        }

        Log::info('Tahun ajaran dihapus', [
            'user_id' => auth()->id(),
            'tahun_id' => $result->id,
            'tahun' => $result->tahun,
            'semester' => $result->semester,
        ]);

        $this->dispatch('showToast', message: 'Tahun ajaran berhasil dihapus.', type: 'success');
        $this->loadTahunList();
    }
}

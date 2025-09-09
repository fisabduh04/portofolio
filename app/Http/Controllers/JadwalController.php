<?php

namespace App\Http\Controllers;

use App\Models\jadwal;
use App\Models\Kelas;
use App\Models\tahun;
use App\Models\mapel;
use App\Models\Pegawai;
use App\Models\pegawai as ModelsPegawai;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
{
    $sort = $request->input('sort', 'hari');
    $direction = $request->input('direction', 'desc');
    $perpage = $request->input('per_Page',10);
    $search = $request->input('search', '');
    $filter_tahun = $request->input('filter_tahun', null);
    $filter_kelas = $request->input('filter_kelas', null);

    // Data pendukung untuk filter
    $tahun   = Tahun::aktif()->get();
    $kelas   = Kelas::all();
    $mapel   = Mapel::all();
    $pegawai = Pegawai::select('id', 'name')->get();
    $hari    = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

    // Query utama
    $query = Jadwal::with(['kelas', 'mapel', 'pegawai']);

    // Pencarian
    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->whereHas('kelas', function($sub) use ($search) {
                $sub->where('kelas', 'like', "%{$search}%");
            })
            ->orWhereHas('mapel', function($sub) use ($search) {
                $sub->where('mapel', 'like', "%{$search}%");
            })
            ->orWhereHas('pegawai', function($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%");
            });
        });
    }
    // Filter berdasarkan tahun
    if (!empty($filter_tahun) && $filter_tahun !== 'all') {
        $query->where('tahun_id', $filter_tahun);
    }
    // Filter berdasarkan kelas
    if (!empty($filter_kelas) && $filter_kelas !== 'all') {
        $query->where('kelas_id', $filter_kelas);
    }
    
    // Sorting kolom relasi
    if ($sort === 'kelas') {
        $query->join('kelas', 'kelas.id', '=', 'jadwals.kelas_id')
              ->orderBy('kelas.kelas', $direction)
              ->select('jadwals.*');
    } elseif ($sort === 'pegawai') {
        $query->join('pegawais', 'pegawais.id', '=', 'jadwals.pegawai_id')
              ->orderBy('pegawais.name', $direction)
              ->select('jadwals.*');
    } elseif ($sort === 'mapel') {
        $query->join('mapels', 'mapels.id', '=', 'jadwals.mapel_id')
              ->orderBy('mapels.mapel', $direction)
              ->select('jadwals.*');
    } 
    else {
        $query->orderBy($sort, $direction);
    }

    $jadwals = $query->paginate($perpage);

    return view('jadwal.index', compact('tahun','kelas','filter_kelas','filter_tahun','mapel','hari','pegawai','jadwals', 'sort', 'direction', 'perpage', 'search'));
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    // dd($request->all());
    $validated = $request->validate([
        'tahun_id'   => 'required',
        'kelas_id'=> 'required',
        'mapel_id'   => 'required',
        'pegawai_id' => 'required',
        'hari'       => 'required',
        'jam'        => 'required',
        'mulai'      => 'required',
        'akhir'      => 'required',
        'ket'        => 'nullable',
    ]);
    // Cek apakah jadwal sudah ada untuk kelas, mapel, dan hari yang sama
    $existingJadwal = Jadwal::where('tahun_id', $validated['tahun_id'])
        ->where('pegawai_id', $validated['pegawai_id'])
        ->where('hari', $validated['hari'])
        ->first(); 
    if ($existingJadwal) {
        // dd('jadwal sudah ada');
        return redirect()->back()
            ->with('type', 'error')
            ->with('message', 'Jadwal untuk kelas, mapel, dan hari ini sudah ada');            
    }     

    $jadwal = Jadwal::create($validated);

    return redirect()->route('jadwal.index')
        ->with('type', 'success')
        ->with('message', 'Jadwal berhasil ditambahkan');   
}

    

    /**
     * Display the specified resource.
     */
    public function show(jadwal $jadwal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(jadwal $jadwal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
{
    try {
        $jadwal = Jadwal::findOrFail($id);

        $validated = $request->validate([
            'tahun_id'   => 'required',
            'kelas_id'   => 'required',
            'mapel_id'   => 'required',
            'pegawai_id' => 'required',
            'hari'       => 'required',
            'jam'        => 'required',
            'mulai'      => 'required',
            'akhir'      => 'required',
            'ket'        => 'nullable',
        ]);

        $jadwal->update($validated);

        return redirect()->route('jadwal.index',[
            'filter_kelas'=>$request->filter_kelas,
            'filer_tahun'=>$request->filter_tahun,
            'sort'=>$request->sort,
            'direction'=>$request->direction
        ])
            ->with('type', 'success')
            ->with('message', 'Jadwal berhasil diperbarui');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('type', 'error')
            ->with('message', 'Gagal memperbarui jadwal: ' . $e->getMessage());
    }
}

    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // dd($id);
        $jadwal = Jadwal::findOrFail($id);
        // dd($jadwal);
        $jadwal->delete();  
        return redirect()->route('jadwal.index',[
            'filter_tahun' => request('filter_tahun'),
            'filter_kelas' => request('filter_kelas')            
        ])->with('type','success')->with('message', 'Jadwal berhasil dihapus');
        
    }

    public function getJadwalJson()
{
    try {
        $jadwal = Jadwal::with(['mapel','kelas','pegawai'])->get();
        return response()->json(['data' => $jadwal], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function bulkDelete(Request $request)
{
    $ids = $request->input('ids');

    if (empty($ids)) {
        return response()->json(['message' => 'No IDs provided'], 400);
    }

    try {
        Jadwal::whereIn('id', $ids)->delete();
        return response()->json(['message' => 'Jadwal deleted successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }}

    public function updateAll(Request $request)
    {
        // dd($request->all());
            $ids         = $request->input('id', []); 
            $kelas_ids   = $request->input('kelas_id', []);
            $haris       = $request->input('hari', []);
            $mapel_ids   = $request->input('mapel_id', []);
            $pegawai_ids = $request->input('pegawai_id', []);
            $jams        = $request->input('jam', []);
            $mulais      = $request->input('mulai', []);
            $akhirs      = $request->input('akhir', []);
            $kets        = $request->input('ket', []);
        // array keterangan

        foreach ($ids as $index => $id) {
            $data = [
                'kelas_id'   => $kelas_ids[$index],
                'hari'       => $haris[$index],
                'mapel_id'   => $mapel_ids[$index],
                'pegawai_id' => $pegawai_ids[$index],
                'jam'        => $jams[$index],
                'mulai'      => $mulais[$index],
                'akhir'      => $akhirs[$index],
                'ket'        => $kets[$index],
            ];

            if (!empty($id)) {
                // Update data lama
                Jadwal::where('id', $id)->update($data);
            } else {
                // Insert data baru
                Jadwal::create($data);
            }
        }

        return redirect()->back()->with('type','success')->with('message', 'Data jadwal berhasil diperbarui.');
    }



}

<?php

namespace App\Http\Controllers;

use App\Exports\MapelExport;
use App\Models\mapel;
use App\Imports\mapelImport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;

class MapelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mapel=mapel::all();
        return view('mapel.index',compact('mapel'));
    }

    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);
        $file=$request->file('file')->store('public/import');
        Excel::import(new mapelImport, $file);
        return redirect('/mapel')->with('success','Data berhasil di import');

    }
     public function export(){
        return Excel::download(new MapelExport, 'mapel.xlsx');

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(mapel $mapel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(mapel $mapel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, mapel $mapel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        mapel::destroy($id);
        return redirect('mapel')->with('success','Data Berhasil Dihapus');
    }
}

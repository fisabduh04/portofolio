<?php

namespace App\Http\Controllers;

use App\Models\SpecialEvent;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpecialEventController extends Controller
{
    public function index()
    {
        $events = SpecialEvent::withCount('participants')->latest()->paginate(10);
        return view('attendance.events.index', compact('events'));
    }

    public function create()
    {
        $pegawais = Pegawai::where('aktif', 'Aktif')->orderBy('name')->get();
        return view('attendance.events.create', compact('pegawais'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'bantuan_hadir' => 'required|numeric|min:0',
            'is_mandatory' => 'boolean',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:pegawais,id',
        ]);

        DB::beginTransaction();
        try {
            $event = SpecialEvent::create([
                'name' => $request->name,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_mandatory' => $request->has('is_mandatory'),
                'bantuan_hadir' => $request->bantuan_hadir,
                'description' => $request->description,
            ]);

            $event->participants()->attach($request->participants);
            
            DB::commit();
            return redirect()->route('attendance.events.index')
                ->with('type', 'success')
                ->with('message', 'Event khusus berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('type', 'error')->with('message', 'Gagal membuat event: ' . $e->getMessage());
        }
    }

    public function destroy(SpecialEvent $specialEvent)
    {
        $specialEvent->delete();
        return redirect()->route('attendance.events.index')
            ->with('type', 'success')
            ->with('message', 'Event berhasil dihapus.');
    }
}

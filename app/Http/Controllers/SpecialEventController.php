<?php

namespace App\Http\Controllers;

use App\Models\SpecialEvent;
use App\Models\Pegawai;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpecialEventController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

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
            
            // Re-calculate attendance for all participants on the event date
            $this->recomputeParticipants($request->participants, $request->date);

            DB::commit();
            return redirect()->route('attendance.events.index')
                ->with('success', 'Event khusus berhasil dibuat dan absensi peserta diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal membuat Special Event: " . $e->getMessage());
            return back()->with('error', 'Gagal membuat event: ' . $e->getMessage());
        }
    }

    public function destroy(SpecialEvent $event)
    {
        DB::beginTransaction();
        try {
            $date = $event->date;
            // Handle as array if $date is Carbon object or string
            $dateString = is_object($date) ? $date->format('Y-m-d') : $date;
            $participantIds = $event->participants->pluck('id')->toArray();

            $event->delete();

            // After delete, recalculate to remove event status from attendance
            $this->recomputeParticipants($participantIds, $dateString);

            DB::commit();
            return redirect()->route('attendance.events.index')
                ->with('success', 'Event berhasil dihapus dan absensi peserta dihitung ulang.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menghapus Special Event: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus event: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk memicu hitung ulang absensi masal
     */
    private function recomputeParticipants(array $participantIds, $date)
    {
        $pegawais = Pegawai::whereIn('id', $participantIds)->get();
        foreach ($pegawais as $pegawai) {
            $this->attendanceService->calculateDailyAttendance($pegawai, $date);
        }
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\FingerprintMachine;
use Illuminate\Http\Request;

class FingerprintMachineController extends Controller
{
    public function index()
    {
        $machines = FingerprintMachine::latest()->paginate(10);
        return view('attendance.fingerprint.index', compact('machines'));
    }

    public function create()
    {
        return view('attendance.fingerprint.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:fingerprint_machines',
            'port' => 'required|integer',
            'comkey' => 'required|integer',
            'location' => 'nullable|string',
        ]);

        $validated['status'] = $request->has('status');

        FingerprintMachine::create($validated);

        return redirect()->route('attendance.fingerprint.index')
            ->with('type', 'success')
            ->with('message', 'Mesin fingerprint berhasil ditambahkan.');
    }

    public function edit(FingerprintMachine $fingerprintMachine)
    {
        return view('attendance.fingerprint.edit', compact('fingerprintMachine'));
    }

    public function update(Request $request, FingerprintMachine $fingerprintMachine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:fingerprint_machines,ip_address,' . $fingerprintMachine->id,
            'port' => 'required|integer',
            'comkey' => 'required|integer',
            'location' => 'nullable|string',
        ]);

        $validated['status'] = $request->has('status');

        $fingerprintMachine->update($validated);

        return redirect()->route('attendance.fingerprint.index')
            ->with('type', 'success')
            ->with('message', 'Data mesin fingerprint berhasil diperbarui.');
    }

    public function destroy(FingerprintMachine $fingerprintMachine)
    {
        $fingerprintMachine->delete();

        return redirect()->route('attendance.fingerprint.index')
            ->with('type', 'success')
            ->with('message', 'Mesin fingerprint berhasil dihapus.');
    }
}

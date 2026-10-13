<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalPeriksaController extends Controller
{
    public function index()
    {
        $jadwalPeriksas = JadwalPeriksa::where('id_dokter', Auth::user()->id)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('dokter.jadwal-periksa.index', compact('jadwalPeriksas'));
    }

    public function create()
    {
        return view('dokter.jadwal-periksa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        JadwalPeriksa::create([
            'id_dokter' => Auth::user()->id,
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
        ]);

        return redirect()->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil ditambahkan')
            ->with('type', 'success');
    }

    public function edit(string $id)
    {
        $jadwalPeriksa = JadwalPeriksa::where('id_dokter', Auth::user()->id)
            ->findOrFail($id);

        return view('dokter.jadwal-periksa.edit', compact('jadwalPeriksa'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwalPeriksa = JadwalPeriksa::where('id_dokter', Auth::user()->id)
            ->findOrFail($id);

        $jadwalPeriksa->update($validated);

        return redirect()->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil diperbarui')
            ->with('type', 'success');
    }

    public function destroy(string $id)
    {
        $jadwalPeriksa = JadwalPeriksa::where('id_dokter', Auth::user()->id)
            ->findOrFail($id);

        if ($jadwalPeriksa->daftarPolis()->exists()) {
            return redirect()->route('jadwal-periksa.index')
                ->with('error', 'Jadwal tidak dapat dihapus karena sudah dipakai pada data pendaftaran');
        }

        $jadwalPeriksa->delete();

        return redirect()->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil dihapus')
            ->with('type', 'success');
    }
}

<?php

namespace App\Http\Controllers\Api\Petugas;

use App\Http\Controllers\Controller;
use App\Models\{JadwalOperasional, TransaksiPenyetoran};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JadwalController extends Controller
{
    /**
     * Menampilkan jadwal operasional bank sampah (diurutkan berdasarkan hari).
     * GET /api/petugas/jadwal
     */
    public function index(Request $request): JsonResponse
    {
        $petugas = $request->user()->petugas;

        if (!$petugas) {
            return response()->json(['status' => false, 'message' => 'Petugas tidak terdaftar.'], 403);
        }

        // Mengurutkan hari secara logis (Senin - Minggu) menggunakan MySQL FIELD
        $jadwal = JadwalOperasional::where('id_bank_sampah', $petugas->id_bank_sampah)
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $jadwal
        ]);
    }

    /**
     * Memperbarui jam operasional pada hari tertentu.
     * PUT /api/petugas/jadwal/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'jam_buka'  => 'required|date_format:H:i',
            'jam_tutup' => 'required|date_format:H:i|after:jam_buka',
        ]);

        $jadwal = JadwalOperasional::findOrFail($id);

        // Pastikan petugas hanya bisa mengubah jadwal bank sampahnya sendiri
        $petugas = $request->user()->petugas;
        if ($jadwal->id_bank_sampah !== $petugas->id_bank_sampah) {
            return response()->json(['status' => false, 'message' => 'Anda tidak memiliki akses ke jadwal ini.'], 403);
        }

        $jadwal->update($request->only('jam_buka', 'jam_tutup'));

        return response()->json([
            'status'  => true,
            'message' => 'Jadwal operasional berhasil diperbarui.',
            'data'    => $jadwal
        ]);
    }

    /**
     * Menampilkan rekapitulasi laporan harian untuk bank sampah tersebut.
     * GET /api/petugas/laporan
     */
    public function laporan(Request $request): JsonResponse
    {
        $petugas = $request->user()->petugas;

        if (!$petugas) {
            return response()->json(['status' => false, 'message' => 'Data petugas tidak ditemukan.'], 403);
        }

        // Agregasi data harian berdasarkan tanggal setor
        $laporanHarian = TransaksiPenyetoran::where('id_bank_sampah', $petugas->id_bank_sampah)
            ->where('status', 'selesai')
            ->selectRaw('
                DATE(created_at) as tanggal, 
                COUNT(*) as jumlah_transaksi, 
                SUM(total_berat_kg) as total_berat, 
                SUM(total_koin) as total_koin
            ')
            ->groupBy('tanggal')
            ->orderByDesc('tanggal')
            ->paginate(30);

        return response()->json([
            'status' => true,
            'data'   => $laporanHarian
        ]);
    }
}
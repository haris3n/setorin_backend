<?php

namespace App\Http\Controllers\Api\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\{Notifikasi, KontenEdukasi};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfilController extends Controller
{
    /**
     * Tampilkan Profil Nasabah (termasuk Saldo, Bank Sampah, dan Koin)
     * GET /api/nasabah/profil
     */
    public function show(Request $request): JsonResponse
    {
        // Menggunakan eager loading agar query lebih efisien
        $user = $request->user()->load(['saldo', 'nasabah.bankSampah']);
        
        // Hitung total koin langsung dari relasi
        $totalKoin = (int) $user->koin()->sum('jumlah_koin');

        // Menggabungkan data user dengan total_koin tanpa merusak struktur array asli
        $userData = $user->toArray();
        $userData['total_koin'] = $totalKoin;

        return response()->json([
            'status' => true,
            'data'   => $userData
        ]);
    }

    /**
     * Update Data Profil
     * POST/PUT /api/nasabah/profil/update
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'nama'       => 'sometimes|string|max:255',
            'no_telepon' => 'sometimes|string|unique:pengguna,no_telepon,' . $user->id,
            'alamat'     => 'sometimes|string',
        ]);

        $user->update($request->only('nama', 'no_telepon', 'alamat'));

        return response()->json([
            'status'  => true,
            'message' => 'Profil berhasil diperbarui.',
            'data'    => $user->only('nama', 'no_telepon', 'alamat')
        ]);
    }

    /**
     * List Notifikasi Nasabah
     * GET /api/nasabah/notifikasi
     */
    public function notifikasi(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $notifications = Notifikasi::where('id_pengguna', $userId)
            ->orderByDesc('created_at')
            ->paginate(15);

        // Tandai yang belum dibaca secara otomatis saat halaman dibuka
        Notifikasi::where('id_pengguna', $userId)
            ->where('status_notifikasi', 'belum_dibaca')
            ->update(['status_notifikasi' => 'dibaca']);

        return response()->json([
            'status' => true,
            'data'   => $notifications
        ]);
    }

    /**
     * List Konten Edukasi untuk Nasabah
     * GET /api/nasabah/edukasi
     */
    public function edukasi(): JsonResponse
    {
        $konten = KontenEdukasi::where('status', 'published')
            ->orderByDesc('tgl_publikasi')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data'   => $konten
        ]);
    }
}
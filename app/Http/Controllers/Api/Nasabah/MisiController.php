<?php

namespace App\Http\Controllers\Api\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\{Misi, KlaimMisi, Koin, Notifikasi};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MisiController extends Controller
{
    /**
     * Menampilkan daftar misi yang sedang aktif saat ini.
     * GET /api/nasabah/misi
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $today = Carbon::today();

        // Ambil misi yang statusnya aktif dan berada dalam rentang tanggal yang benar
        $misi = Misi::where('status_misi', 'aktif')
            ->where('tgl_mulai', '<=', $today)
            ->where('tgl_selesai', '>=', $today)
            ->get();

        // Menambahkan atribut 'sudah_klaim' secara dinamis untuk UI di Flutter
        $misi->each(function ($item) use ($userId) {
            $item->sudah_klaim = KlaimMisi::where('id_pengguna', $userId)
                ->where('id_misi', $item->id)
                ->where('status_klaim', 'valid')
                ->exists();
        });

        return response()->json([
            'status' => true,
            'data'   => $misi
        ]);
    }

    /**
     * Melakukan klaim reward dari misi tertentu.
     * POST /api/nasabah/misi/{id}/klaim
     */
    public function klaim(Request $request, $id): JsonResponse
    {
        $misi = Misi::findOrFail($id);
        $userId = $request->user()->id;

        // 1. Validasi: Apakah misi masih aktif?
        if ($misi->status_misi !== 'aktif' || Carbon::now() > $misi->tgl_selesai) {
            return response()->json([
                'status'  => false,
                'message' => 'Misi ini sudah tidak aktif atau telah berakhir.'
            ], 422);
        }

        // 2. Validasi: Apakah sudah pernah klaim sebelumnya?
        $cekKlaim = KlaimMisi::where('id_pengguna', $userId)
            ->where('id_misi', $id)
            ->where('status_klaim', 'valid')
            ->exists();

        if ($cekKlaim) {
            return response()->json([
                'status'  => false,
                'message' => 'Anda sudah mengklaim reward untuk misi ini.'
            ], 422);
        }

        // 3. Proses Klaim menggunakan Transaction
        return DB::transaction(function () use ($misi, $userId) {
            
            // Catat di tabel Klaim Misi
            KlaimMisi::create([
                'id_pengguna'   => $userId,
                'id_misi'       => $misi->id,
                'status_klaim'  => 'valid',
                'koin_diterima' => $misi->reward_koin,
                'tgl_klaim'     => now(),
            ]);

            // Tambahkan ke buku besar Koin
            Koin::create([
                'id_pengguna' => $userId,
                'jumlah_koin' => $misi->reward_koin,
                'sumber'      => 'misi'
            ]);

            // Kirim Notifikasi ke Nasabah
            Notifikasi::create([
                'id_pengguna'       => $userId,
                'judul'             => 'Hadiah Misi Diterima!',
                'pesan'             => "Selamat! Kamu mendapatkan {$misi->reward_koin} koin dari misi: {$misi->nama_misi}",
                'status_notifikasi' => 'belum_dibaca'
            ]);

            return response()->json([
                'status'  => true,
                'message' => "Berhasil mengklaim {$misi->reward_koin} koin!"
            ]);
        });
    }
}
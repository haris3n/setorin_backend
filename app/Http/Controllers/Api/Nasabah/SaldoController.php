<?php

namespace App\Http\Controllers\Api\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\{Saldo, Koin, PenarikanSaldo, Notifikasi};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SaldoController extends Controller
{
    /**
     * Menampilkan informasi saldo, total koin, dan riwayat penarikan terbaru.
     * GET /api/nasabah/saldo
     */
    public function show(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $saldo = Saldo::where('id_pengguna', $userId)->first();
        
        // Hitung total koin (pastikan casting ke integer)
        $totalKoin = (int) Koin::where('id_pengguna', $userId)->sum('jumlah_koin');

        // Ambil 5 riwayat penarikan terakhir
        $riwayatPenarikan = PenarikanSaldo::where('id_pengguna', $userId)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => [
                'saldo'            => $saldo,
                'total_koin'       => $totalKoin,
                'riwayat_penarikan' => $riwayatPenarikan
            ]
        ]);
    }

    /**
     * Menukarkan koin menjadi saldo (1 koin = Rp 10).
     * POST /api/nasabah/tukar-koin
     */
    public function tukarKoin(Request $request): JsonResponse
    {
        $request->validate([
            'jumlah_koin' => 'required|integer|min:1'
        ]);

        $userId = $request->user()->id;
        $totalKoin = Koin::where('id_pengguna', $userId)->sum('jumlah_koin');

        if ($totalKoin < $request->jumlah_koin) {
            return response()->json([
                'status'  => false,
                'message' => 'Koin tidak mencukupi untuk penukaran ini.'
            ], 422);
        }

        $nilaiSaldo = $request->jumlah_koin * 10; // Konfigurasi: 1 koin = Rp 10

        return DB::transaction(function () use ($request, $userId, $nilaiSaldo) {
            // 1. Kurangi koin (catat sebagai transaksi keluar/negatif)
            Koin::create([
                'id_pengguna' => $userId,
                'jumlah_koin' => -$request->jumlah_koin,
                'sumber'      => 'tukar_saldo' // Label sumber lebih spesifik
            ]);

            // 2. Tambah saldo
            $saldo = Saldo::where('id_pengguna', $userId)->first();
            $saldo->increment('jumlah_saldo', $nilaiSaldo);
            $saldo->update(['tgl_update' => now()]);

            return response()->json([
                'status'  => true,
                'message' => "Berhasil menukar {$request->jumlah_koin} koin menjadi Rp " . number_format($nilaiSaldo, 0, ',', '.')
            ]);
        });
    }

    /**
     * Mengajukan permintaan penarikan saldo ke rekening/e-wallet.
     * POST /api/nasabah/tarik-saldo
     */
    public function ajukanPenarikan(Request $request): JsonResponse
    {
        $request->validate([
            'jumlah_tarik' => 'required|numeric|min:10000',
            'metode_bayar' => 'required|string', // Contoh: Dana, OVO, Transfer Bank
            'no_rekening'  => 'required|string',
        ]);

        $userId = $request->user()->id;
        $saldo  = Saldo::where('id_pengguna', $userId)->first();

        if (!$saldo || $saldo->jumlah_saldo < $request->jumlah_tarik) {
            return response()->json([
                'status'  => false,
                'message' => 'Saldo Anda tidak mencukupi untuk penarikan ini.'
            ], 422);
        }

        // Simpan pengajuan penarikan
        $penarikan = PenarikanSaldo::create([
            'id_pengguna'  => $userId,
            'id_saldo'     => $saldo->id,
            'jumlah_tarik' => $request->jumlah_tarik,
            'metode_bayar' => $request->metode_bayar,
            'no_rekening'  => $request->no_rekening,
            'status'       => 'pending',
            'tgl_pengajuan' => now()
        ]);

        // Opsional: Buat notifikasi otomatis untuk user
        Notifikasi::create([
            'id_pengguna'       => $userId,
            'judul'             => 'Penarikan Diajukan',
            'pesan'             => 'Permintaan penarikan Rp ' . number_format($request->jumlah_tarik) . ' sedang diproses.',
            'status_notifikasi' => 'belum_dibaca'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Permintaan penarikan berhasil diajukan. Mohon tunggu verifikasi admin.'
        ]);
    }
}
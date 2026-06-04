<?php

namespace App\Http\Controllers\Api\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\{Saldo, Koin, PenarikanSaldo, Notifikasi, HargaCoin};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
                'saldo'             => $saldo,
                'total_koin'        => $totalKoin,
                'saldo_tertahan'    => $saldo ? (double) $saldo->saldo_tertahan : 0,
                'has_pin'           => !empty($request->user()->pin),
                'riwayat_penarikan' => $riwayatPenarikan
            ]
        ]);
    }

    /**
     * Set PIN transaksi nasabah (6 digit).
     * POST /api/nasabah/saldo/set-pin
     */
    public function setPin(Request $request): JsonResponse
    {
        $request->validate([
            'pin' => 'required|digits:6',
        ]);

        $user = $request->user();

        // Jika sudah punya PIN, harus verifikasi PIN lama dulu
        if (!empty($user->pin)) {
            $request->validate([
                'pin_lama' => 'required|digits:6',
            ]);

            if (!Hash::check($request->pin_lama, $user->pin)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'PIN lama tidak sesuai.'
                ], 422);
            }
        }

        $user->update([
            'pin' => Hash::make($request->pin),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'PIN berhasil disimpan.'
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

        // Ambil harga koin yang aktif dari database, jika tidak ada default ke 100
        $hargaCoinRecord = HargaCoin::where('status', 'aktif')->first();
        $hargaPerKoin = $hargaCoinRecord ? (int) $hargaCoinRecord->harga_per_coin : 100;

        $nilaiSaldo = $request->jumlah_koin * $hargaPerKoin; // Konversi dinamis dari admin

        return DB::transaction(function () use ($request, $userId, $nilaiSaldo) {
            // 1. Kurangi koin (catat sebagai transaksi keluar/negatif)
            Koin::create([
                'id_pengguna'  => $userId,
                'jumlah_koin'  => -$request->jumlah_koin,
                'tgl_diperoleh' => now(),
                'sumber'       => 'transaksi' // Harus sesuai ENUM di database ('transaksi' atau 'misi')
            ]);

            // 2. Tambah saldo (buat record jika belum ada)
            $saldo = Saldo::firstOrCreate(
                ['id_pengguna' => $userId],
                ['jumlah_saldo' => 0, 'saldo_tertahan' => 0, 'tgl_update' => now()]
            );
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
     * POST /api/nasabah/saldo/tarik
     * 
     * Menggunakan sistem Hold Balance:
     * - Saldo aktif langsung dikurangi
     * - Nominal dipindahkan ke saldo_tertahan
     * - Admin menyetujui: saldo_tertahan dikurangi (dana keluar)
     * - Admin menolak: saldo_tertahan dikembalikan ke saldo aktif
     */
    public function ajukanPenarikan(Request $request): JsonResponse
    {
        $request->validate([
            'jumlah_tarik' => 'required|numeric|min:10000',
            'metode_bayar' => 'required|string',
            'no_rekening'  => 'required|string',
            'pin'          => 'required|digits:6',
        ]);

        $user   = $request->user();
        $userId = $user->id;

        // Verifikasi PIN
        if (empty($user->pin)) {
            return response()->json([
                'status'  => false,
                'message' => 'Anda belum mengatur PIN transaksi. Silakan buat PIN terlebih dahulu.'
            ], 422);
        }

        if (!Hash::check($request->pin, $user->pin)) {
            return response()->json([
                'status'  => false,
                'message' => 'PIN yang Anda masukkan salah.'
            ], 422);
        }

        $saldo = Saldo::where('id_pengguna', $userId)->first();

        if (!$saldo || $saldo->jumlah_saldo < $request->jumlah_tarik) {
            return response()->json([
                'status'  => false,
                'message' => 'Saldo Anda tidak mencukupi untuk penarikan ini.'
            ], 422);
        }

        return DB::transaction(function () use ($request, $userId, $saldo) {
            // 1. Hold Balance: kurangi saldo aktif, pindahkan ke saldo_tertahan
            $saldo->decrement('jumlah_saldo', $request->jumlah_tarik);
            $saldo->increment('saldo_tertahan', $request->jumlah_tarik);
            $saldo->update(['tgl_update' => now()]);

            // 2. Simpan pengajuan penarikan
            PenarikanSaldo::create([
                'id_pengguna'   => $userId,
                'id_saldo'      => $saldo->id,
                'jumlah_tarik'  => $request->jumlah_tarik,
                'metode_bayar'  => $request->metode_bayar,
                'no_rekening'   => $request->no_rekening,
                'status'        => 'pending',
                'tgl_pengajuan' => now()
            ]);

            // 3. Buat notifikasi otomatis
            Notifikasi::create([
                'id_pengguna'       => $userId,
                'judul'             => 'Penarikan Diajukan',
                'pesan'             => 'Permintaan penarikan Rp ' . number_format($request->jumlah_tarik) . ' sedang diproses.',
                'tipe'              => 'saldo',
                'status_notifikasi' => 'belum_dibaca'
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Permintaan penarikan berhasil diajukan. Mohon tunggu verifikasi admin.'
            ]);
        });
    }
}

<?php

namespace App\Http\Controllers\Api\Petugas;

use App\Http\Controllers\Controller;
use App\Models\{TransaksiPenyetoran, DetailTransaksiSampah, HargaSampah, Nasabah, Koin, Saldo, Notifikasi};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TransaksiPetugasController extends Controller
{
    /**
     * Menampilkan daftar transaksi khusus di Bank Sampah tempat petugas bertugas.
     * GET /api/petugas/transaksi
     */
    public function index(Request $request): JsonResponse
    {
        $petugas = $request->user()->petugas;

        if (!$petugas) {
            return response()->json(['status' => false, 'message' => 'Akses ditolak. Anda bukan petugas.'], 403);
        }

        $transaksi = TransaksiPenyetoran::where('id_bank_sampah', $petugas->id_bank_sampah)
            ->with(['nasabah.pengguna', 'detail.hargaSampah', 'petugas.pengguna'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data'   => $transaksi
        ]);
    }

    /**
     * Mencatat transaksi penyetoran baru dari nasabah.
     * POST /api/petugas/transaksi
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'id_nasabah'               => 'required|exists:nasabah,id',
            'detail'                    => 'required|array|min:1',
            'detail.*.id_harga_sampah' => 'required|exists:harga_sampah,id',
            'detail.*.berat_kg'        => 'required|numeric|min:0.1',
            'catatan'                  => 'nullable|string',
        ]);

        $petugas = $request->user()->petugas;

        if (!$petugas) {
            return response()->json(['status' => false, 'message' => 'Petugas tidak teridentifikasi.'], 403);
        }

        return DB::transaction(function () use ($request, $petugas) {
            $totalBerat = 0;
            $totalKoin  = 0;

            // 1. Buat Header Transaksi (Status diproses dulu)
            $transaksi = TransaksiPenyetoran::create([
                'id_nasabah'     => $request->id_nasabah,
                'id_bank_sampah' => $petugas->id_bank_sampah,
                'id_petugas'     => $petugas->id,
                'status'         => 'pending',
                'catatan'        => $request->catatan,
            ]);

            // 2. Loop Detail Sampah
            foreach ($request->detail as $d) {
                $harga    = HargaSampah::findOrFail($d['id_harga_sampah']);
                $subtotal = $harga->harga_per_kg * $d['berat_kg'];
                
                // Rumus: Rp 100 = 1 Koin (Bisa disesuaikan di masa depan)
                $koin = (int)($subtotal / 100); 

                DetailTransaksiSampah::create([
                    'id_transaksi'    => $transaksi->id,
                    'id_harga_sampah' => $d['id_harga_sampah'],
                    'berat_kg'        => $d['berat_kg'],
                    'subtotal'        => $subtotal,
                ]);

                $totalBerat += $d['berat_kg'];
                $totalKoin  += $koin;
            }

            // 3. Update header — menunggu konfirmasi nasabah (koin belum masuk)
            $transaksi->update([
                'total_berat_kg' => $totalBerat,
                'total_koin'     => $totalKoin,
                'status'         => 'pending',
            ]);

            $nasabah = Nasabah::find($request->id_nasabah);

            Notifikasi::create([
                'id_pengguna'           => $nasabah->id_pengguna,
                'id_transaksi'          => $transaksi->id,
                'judul'                 => 'Konfirmasi Setoran Sampah',
                'pesan'                 => "Petugas mencatat setoran {$totalBerat} kg (estimasi {$totalKoin} koin). Buka aplikasi dan tekan Konfirmasi jika data sudah sesuai.",
                'tipe'                  => 'transaksi',
                'status_notifikasi'     => 'belum_dibaca',
                'memerlukan_konfirmasi' => true,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Transaksi tercatat. Menunggu konfirmasi nasabah di aplikasi.',
                'data'    => $transaksi->load('detail')
            ], 201);
        });
    }

    /**
     * Mengonfirmasi transaksi secara manual (jika diperlukan).
     * PATCH /api/petugas/transaksi/{id}/konfirmasi
     */
    public function konfirmasi(Request $request, $id): JsonResponse
    {
        $transaksi = TransaksiPenyetoran::findOrFail($id);
        
        if ($transaksi->status !== 'diproses') {
            return response()->json(['status' => false, 'message' => 'Transaksi belum dikonfirmasi nasabah atau sudah selesai.'], 422);
        }

        return DB::transaction(function () use ($transaksi, $request) {
            $transaksi->update(['status' => 'selesai']);

            $totalKoin = (int) $transaksi->total_koin;
            $totalBerat = (float) $transaksi->total_berat_kg;

            // Tambahkan koin ke nasabah
            Koin::create([
                'id_pengguna' => $transaksi->nasabah->id_pengguna,
                'jumlah_koin' => $totalKoin,
                'sumber'      => 'transaksi',
            ]);

            // Update notifikasi lama (hapus flag memerlukan konfirmasi)
            Notifikasi::where('id_pengguna', $transaksi->nasabah->id_pengguna)
                ->where('id_transaksi', $transaksi->id)
                ->where('memerlukan_konfirmasi', true)
                ->update([
                    'memerlukan_konfirmasi' => false,
                    'status_notifikasi'     => 'dibaca',
                ]);

            // Buat notifikasi baru bahwa setoran berhasil
            Notifikasi::create([
                'id_pengguna'           => $transaksi->nasabah->id_pengguna,
                'id_transaksi'          => $transaksi->id,
                'judul'                 => 'Setoran Berhasil Dikonfirmasi (oleh Petugas)',
                'pesan'                 => "Anda mendapatkan {$totalKoin} koin dari setoran seberat {$totalBerat} kg yang dikonfirmasi petugas.",
                'tipe'                  => 'transaksi',
                'status_notifikasi'     => 'belum_dibaca',
                'memerlukan_konfirmasi' => false,
            ]);

            return response()->json([
                'status'  => true,
                'message' => "Status transaksi berhasil diperbarui menjadi selesai. {$totalKoin} koin ditambahkan ke nasabah."
            ]);
        });
    }
}


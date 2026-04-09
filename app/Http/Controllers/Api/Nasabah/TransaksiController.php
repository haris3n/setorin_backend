<?php

namespace App\Http\Controllers\Api\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\{BankSampah, Nasabah, TransaksiPenyetoran};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar Bank Sampah yang aktif beserta jadwal dan harganya.
     * GET /api/nasabah/bank-sampah
     */
    public function listBankSampah(): JsonResponse
    {
        $bankSampah = BankSampah::where('status', 'aktif')
            ->with([
                'jadwalOperasional', 
                'hargaSampah' => function ($query) {
                    $query->where('status', 'aktif');
                }
            ])
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $bankSampah
        ]);
    }

    /**
     * Nasabah memilih Bank Sampah sebagai tempat penyetoran utama.
     * POST /api/nasabah/bank-sampah/pilih
     */
    public function pilihBankSampah(Request $request): JsonResponse
    {
        $request->validate([
            'id_bank_sampah' => 'required|exists:bank_sampah,id'
        ]);

        $nasabah = $request->user()->nasabah;

        if (!$nasabah) {
            return response()->json([
                'status'  => false,
                'message' => 'Profil nasabah tidak ditemukan.'
            ], 404);
        }

        $nasabah->update([
            'id_bank_sampah' => $request->id_bank_sampah
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Bank sampah berhasil dipilih sebagai mitra.'
        ]);
    }

    /**
     * Melihat riwayat transaksi penyetoran nasabah.
     * GET /api/nasabah/transaksi
     */
    public function riwayat(Request $request): JsonResponse
    {
        $nasabah = $request->user()->nasabah;

        if (!$nasabah) {
            return response()->json([
                'status'  => false,
                'message' => 'Data nasabah tidak tersedia.'
            ], 404);
        }

        $transaksi = TransaksiPenyetoran::where('id_nasabah', $nasabah->id)
            ->with([
                'detail.hargaSampah', 
                'bankSampah', 
                'petugas.pengguna' => function($q) {
                    $q->select('id', 'nama'); // Hanya ambil nama petugas untuk keamanan
                }
            ])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'status' => true,
            'data'   => $transaksi
        ]);
    }

    /**
     * Nasabah melaporkan rencana penyetoran (Pre-transaction).
     * POST /api/nasabah/laporan-sampah
     */
    public function laporkanSampah(Request $request): JsonResponse
    {
        $request->validate([
            'jenis_sampah' => 'required|string',
            'jumlah_item'  => 'required|integer|min:1',
            'catatan'      => 'nullable|string',
        ]);

        // Catatan: Sesuai arsitektur Setor.in, laporan ini bersifat informatif.
        // Transaksi finansial (saldo/koin) baru akan digenerate oleh Petugas 
        // melalui TransaksiPetugasController saat verifikasi fisik sampah.

        return response()->json([
            'status'  => true,
            'message' => 'Laporan rencana setoran berhasil dikirim. Silakan datang ke Bank Sampah terkait.'
        ]);
    }
}
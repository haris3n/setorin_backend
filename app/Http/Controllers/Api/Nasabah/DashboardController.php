<?php

namespace App\Http\Controllers\Api\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\{Saldo, Koin, TransaksiPenyetoran, PenarikanSaldo, KlaimMisi};

class DashboardController extends Controller
{
    /**
     * Menampilkan data lengkap untuk dashboard utama aplikasi mobile.
     * GET /api/nasabah/dashboard
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Ambil profil nasabah (jika user punya relasi nasabah)
        $nasabah = $user->nasabah;

        // 1. Data Saldo & Koin
        $saldoRecord = Saldo::where('id_pengguna', $user->id)->first();
        $jumlahSaldo = $saldoRecord ? (double)$saldoRecord->jumlah_saldo : 0;
        
        $totalKoin = (int) Koin::where('id_pengguna', $user->id)->sum('jumlah_koin');

        // 2. Data Ringkasan (Transaksi)
        $totalSetor = 0;
        $beratTotal = 0;
        $aktivitas = [];

        if ($nasabah) {
            // Hitung total transaksi dan berat
            $totalSetor = TransaksiPenyetoran::where('id_nasabah', $nasabah->id)
                            ->where('status', 'selesai')
                            ->count();
                            
            $beratTotal = (double) TransaksiPenyetoran::where('id_nasabah', $nasabah->id)
                            ->where('status', 'selesai')
                            ->sum('total_berat_kg');

            // Ambil 3 aktivitas terbaru (gabungan setor sampah + tukar koin + penarikan)
            $aktivitas = $this->buildAktivitasGabungan($user->id, $nasabah?->id, 3);
        }

        // Return semua data ke UI
        return response()->json([
            'status' => true,
            'data'   => [
                'user' => [
                    'nama'       => $user->nama,
                    'email'      => $user->email,
                    'no_telepon' => $user->no_telepon,
                ],
                'keuangan' => [
                    'saldo' => $jumlahSaldo,
                    'koin'  => $totalKoin,
                ],
                'ringkasan' => [
                    'total_setor' => $totalSetor,
                    'berat_total' => $beratTotal,
                    'co2_tersimpan' => '2.4' // Dummy data sesuai permintaan user
                ],
                'aktivitas' => $aktivitas
            ]
        ]);
    }

    /**
     * Menampilkan riwayat semua aktivitas nasabah (gabungan).
     * GET /api/nasabah/aktivitas
     */
    public function aktivitas(Request $request): JsonResponse
    {
        $user = $request->user();
        $nasabah = $user->nasabah;

        $aktivitas = $this->buildAktivitasGabungan($user->id, $nasabah?->id, 50);

        return response()->json([
            'status' => true,
            'data'   => $aktivitas
        ]);
    }

    /**
     * Helper: Gabungkan semua jenis aktivitas menjadi satu timeline.
     */
    private function buildAktivitasGabungan(int $userId, ?int $nasabahId, int $limit): array
    {
        $items = collect();

        // 1. Setor Sampah
        if ($nasabahId) {
            $setoran = TransaksiPenyetoran::where('id_nasabah', $nasabahId)
                ->with(['detail.hargaSampah', 'bankSampah'])
                ->orderByDesc('created_at')
                ->take($limit)
                ->get()
                ->map(function ($trx) {
                    $jenisSampah = 'Sampah Campur';
                    if ($trx->detail->isNotEmpty() && $trx->detail->first()->hargaSampah) {
                        $jenisSampah = $trx->detail->first()->hargaSampah->jenis_sampah;
                    }
                    $bankNama = $trx->bankSampah?->nama ?? '-';

                    return [
                        'type'     => 'setor',
                        'title'    => 'Setor ' . $jenisSampah,
                        'subtitle' => $trx->total_berat_kg . ' kg • +' . $trx->total_koin . ' koin',
                        'detail'   => 'Bank Sampah: ' . $bankNama,
                        'date'     => $trx->created_at->format('d M Y'),
                        'time'     => $trx->created_at->format('H:i'),
                        'status'   => $trx->status,
                        'created_at' => $trx->created_at,
                    ];
                });

            $items = $items->merge($setoran);
        }

        // 2. Klaim Misi
        $klaimMisi = KlaimMisi::where('id_pengguna', $userId)
            ->with('misi')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get()
            ->map(function ($klaim) {
                $namaMisi = $klaim->misi?->nama_misi ?? 'Misi Spesial';
                return [
                    'type'     => 'misi',
                    'title'    => 'Klaim Misi: ' . $namaMisi,
                    'subtitle' => '+' . $klaim->koin_diterima . ' koin',
                    'detail'   => 'Misi berhasil diselesaikan',
                    'date'     => $klaim->created_at->format('d M Y'),
                    'time'     => $klaim->created_at->format('H:i'),
                    'status'   => $klaim->status_klaim ?? 'selesai',
                    'created_at' => $klaim->created_at,
                ];
            });

        $items = $items->merge($klaimMisi);

        // Urutkan berdasarkan waktu terbaru, lalu ambil sesuai limit
        return $items->sortByDesc('created_at')
            ->take($limit)
            ->values()
            ->toArray();
    }
}

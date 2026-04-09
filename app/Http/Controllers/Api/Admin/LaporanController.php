<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, BankSampah, TransaksiPenyetoran, Koin, DetailTransaksiSampah};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * GET /api/admin/laporan
     * Ambil data laporan
     */
    public function index(Request $request): JsonResponse
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // Menghitung statistik global
        $stats = [
            'total_nasabah'      => User::where('role', 'nasabah')->count(),
            'total_petugas'      => User::where('role', 'petugas')->count(),
            'total_bank_aktif'   => BankSampah::where('status', 'aktif')->count(),
            'total_transaksi'    => TransaksiPenyetoran::where('status', 'selesai')->count(),
            'total_berat_kg'     => (float) TransaksiPenyetoran::where('status', 'selesai')->sum('total_berat_kg'),
            'total_koin_beredar' => (int) Koin::sum('jumlah_koin'),

            // Statistik spesifik bulan terpilih
            'monthly_stats' => [
                'jumlah_transaksi' => TransaksiPenyetoran::whereMonth('created_at', $bulan)
                                        ->whereYear('created_at', $tahun)
                                        ->where('status', 'selesai')->count(),
                'total_berat'      => (float) TransaksiPenyetoran::whereMonth('created_at', $bulan)
                                        ->whereYear('created_at', $tahun)
                                        ->where('status', 'selesai')->sum('total_berat_kg'),
            ],

            // Performa per Bank Sampah
            'per_bank' => BankSampah::select('id', 'nama_bank')
                ->withCount(['transaksi as total_transaksi' => function($q) {
                    $q->where('status', 'selesai');
                }])
                ->withSum(['transaksi as total_kg' => function($q) {
                    $q->where('status', 'selesai');
                }], 'total_berat_kg')
                ->get(),
        ];

        return response()->json([
            'status' => true,
            'data'   => $stats
        ]);
    }

    /**
     * GET /api/admin/laporan/export
     * Export data laporan ke CSV
     */
    public function export(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // Ambil data transaksi untuk bulan yang dipilih
        $transaksi = TransaksiPenyetoran::with(['nasabah.pengguna', 'bankSampah', 'petugas.pengguna'])
            ->whereMonth('tgl_setor', $bulan)
            ->whereYear('tgl_setor', $tahun)
            ->where('status', 'selesai')
            ->orderBy('tgl_setor', 'desc')
            ->get();

        // Buat filename
        $filename = 'laporan_transaksi_' . $bulan . '_' . $tahun . '.csv';

        // Header untuk download CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transaksi) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'ID Transaksi',
                'Tanggal',
                'Nama Nasabah',
                'Bank Sampah',
                'Petugas',
                'Total Berat (kg)',
                'Total Koin',
                'Status',
                'Catatan'
            ]);

            // Data rows
            foreach ($transaksi as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->tgl_setor->format('Y-m-d H:i:s'),
                    $t->nasabah?->pengguna?->nama ?? '-',
                    $t->bankSampah?->nama_bank ?? '-',
                    $t->petugas?->pengguna?->nama ?? '-',
                    number_format($t->total_berat_kg, 2, '.', ''),
                    $t->total_koin,
                    $t->status,
                    $t->catatan ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * GET /api/admin/laporan/export-detail
     * Export detail transaksi ke CSV
     */
    public function exportDetail(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // Ambil detail transaksi
        $details = DB::table('detail_transaksi_sampah as d')
            ->join('transaksi_penyetoran as t', 'd.id_transaksi', '=', 't.id')
            ->join('harga_sampah as h', 'd.id_harga_sampah', '=', 'h.id')
            ->join('pengguna as p', 't.id_nasabah', '=', 'p.id')
            ->select(
                'd.id_transaksi',
                't.tgl_setor',
                'p.nama as nama_nasabah',
                'h.jenis_sampah',
                'd.berat_kg',
                'h.harga_per_kg',
                DB::raw('d.berat_kg * h.harga_per_kg as total_harga')
            )
            ->whereMonth('t.tgl_setor', $bulan)
            ->whereYear('t.tgl_setor', $tahun)
            ->where('t.status', 'selesai')
            ->orderBy('t.tgl_setor', 'desc')
            ->get();

        $filename = 'laporan_detail_' . $bulan . '_' . $tahun . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($details) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID Transaksi',
                'Tanggal',
                'Nama Nasabah',
                'Jenis Sampah',
                'Berat (kg)',
                'Harga/kg',
                'Total'
            ]);

            foreach ($details as $d) {
                fputcsv($file, [
                    $d->id_transaksi,
                    $d->tgl_setor,
                    $d->nama_nasabah,
                    $d->jenis_sampah,
                    number_format($d->berat_kg, 2, '.', ''),
                    number_format($d->harga_per_kg, 0, '.', ''),
                    number_format($d->total_harga, 0, '.', '')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
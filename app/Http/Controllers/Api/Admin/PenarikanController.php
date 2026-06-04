<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\{PenarikanSaldo, Saldo, Notifikasi};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PenarikanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PenarikanSaldo::with('pengguna');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $data = $query->orderByDesc('created_at')->paginate(20);

        return response()->json(['status' => true, 'data' => $data]);
    }

    /**
     * Setujui penarikan saldo.
     * Saldo aktif sudah dipotong sejak pengajuan (hold balance).
     * Di sini kita hanya mengurangi saldo_tertahan.
     */
    public function setujui($id): JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $p = PenarikanSaldo::findOrFail($id);

            if ($p->status !== 'pending') {
                return response()->json(['status' => false, 'message' => 'Transaksi ini sudah diproses sebelumnya.'], 422);
            }

            // 1. Update status penarikan
            $p->update(['status' => 'disetujui']);

            // 2. Kurangi saldo_tertahan (dana keluar secara permanen)
            $saldo = Saldo::findOrFail($p->id_saldo);
            $saldo->saldo_tertahan = (double) $saldo->saldo_tertahan - (double) $p->jumlah_tarik;
            $saldo->tgl_update = now();
            $saldo->save();

            // 3. Beri Notifikasi
            Notifikasi::create([
                'id_pengguna' => $p->id_pengguna,
                'judul'       => 'Penarikan Disetujui',
                'pesan'       => 'Dana sebesar Rp ' . number_format($p->jumlah_tarik, 0, ',', '.') . ' telah dikirim ke rekening Anda.',
                'tipe'        => 'saldo',
            ]);

            return response()->json(['status' => true, 'message' => 'Penarikan berhasil disetujui dan dana telah dikirim.']);
        });
    }

    /**
     * Tolak penarikan saldo.
     * Kembalikan dana dari saldo_tertahan ke saldo aktif (refund).
     */
    public function tolak(Request $request, $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            $p = PenarikanSaldo::findOrFail($id);

            if ($p->status !== 'pending') {
                return response()->json(['status' => false, 'message' => 'Transaksi sudah tidak bisa diubah.'], 422);
            }

            // 1. Update status
            $p->update(['status' => 'ditolak']);

            // 2. Refund: kembalikan saldo_tertahan ke saldo aktif
            $saldo = Saldo::findOrFail($p->id_saldo);
            $saldo->saldo_tertahan = (double) $saldo->saldo_tertahan - (double) $p->jumlah_tarik;
            $saldo->jumlah_saldo = (double) $saldo->jumlah_saldo + (double) $p->jumlah_tarik;
            $saldo->tgl_update = now();
            $saldo->save();

            // 3. Notifikasi
            Notifikasi::create([
                'id_pengguna' => $p->id_pengguna,
                'judul'       => 'Penarikan Ditolak',
                'pesan'       => 'Mohon maaf, penarikan Rp ' . number_format($p->jumlah_tarik, 0, ',', '.') . ' ditolak. Alasan: ' . ($request->alasan ?? 'Data tidak valid.') . ' Dana telah dikembalikan ke saldo Anda.',
                'tipe'        => 'saldo',
            ]);

            return response()->json(['status' => true, 'message' => 'Permintaan penarikan telah ditolak dan saldo dikembalikan.']);
        });
    }
}
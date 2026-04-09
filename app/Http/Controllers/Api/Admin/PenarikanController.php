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

    public function setujui($id): JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $p = PenarikanSaldo::findOrFail($id);

            if ($p->status !== 'pending') {
                return response()->json(['status' => false, 'message' => 'Transaksi ini sudah diproses sebelumnya.'], 422);
            }

            // 1. Update status penarikan
            $p->update(['status' => 'disetujui']);

            // 2. Potong saldo nasabah secara permanen
            $saldo = Saldo::findOrFail($p->id_saldo);
            $saldo->decrement('jumlah_saldo', $p->jumlah_tarik);
            $saldo->update(['tgl_update' => now()]);

            // 3. Beri Notifikasi
            Notifikasi::create([
                'id_pengguna' => $p->id_pengguna,
                'judul'       => 'Penarikan Disetujui',
                'pesan'       => 'Dana sebesar Rp ' . number_format($p->jumlah_tarik, 0, ',', '.') . ' telah dikirim ke rekening Anda.',
                'tipe'        => 'saldo',
            ]);

            return response()->json(['status' => true, 'message' => 'Penarikan berhasil disetujui dan saldo telah dipotong.']);
        });
    }

    public function tolak(Request $request, $id): JsonResponse
    {
        $p = PenarikanSaldo::findOrFail($id);

        if ($p->status !== 'pending') {
            return response()->json(['status' => false, 'message' => 'Transaksi sudah tidak bisa diubah.'], 422);
        }

        $p->update(['status' => 'ditolak']);

        Notifikasi::create([
            'id_pengguna' => $p->id_pengguna,
            'judul'       => 'Penarikan Ditolak',
            'pesan'       => 'Mohon maaf, penarikan Anda ditolak. Alasan: ' . ($request->alasan ?? 'Data tidak valid.'),
            'tipe'        => 'saldo',
        ]);

        return response()->json(['status' => true, 'message' => 'Permintaan penarikan telah ditolak.']);
    }
}
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\HargaSampah;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HargaSampahController extends Controller
{
    /**
     * Menampilkan daftar harga (bisa difilter berdasarkan bank sampah).
     */
    public function index(Request $request): JsonResponse
    {
        $query = HargaSampah::with('bankSampah');

        if ($request->id_bank_sampah) {
            $query->where('id_bank_sampah', $request->id_bank_sampah);
        }

        $harga = $query->orderBy('jenis_sampah', 'asc')->get();

        return response()->json([
            'status' => true,
            'data'   => $harga
        ]);
    }

    /**
     * Menambahkan jenis sampah & harga baru ke bank tertentu.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'id_bank_sampah' => 'required|exists:bank_sampah,id',
            'jenis_sampah'   => 'required|string|max:100',
            'harga_per_kg'   => 'required|numeric|min:0',
        ]);

        $harga = HargaSampah::create($request->only('id_bank_sampah', 'jenis_sampah', 'harga_per_kg'));

        return response()->json([
            'status'  => true,
            'message' => 'Jenis sampah & harga berhasil didaftarkan.',
            'data'    => $harga
        ], 201);
    }

    /**
     * Update harga atau nama jenis sampah.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $harga = HargaSampah::findOrFail($id);

        $request->validate([
            'harga_per_kg' => 'sometimes|numeric|min:0',
            'status'       => 'sometimes|in:aktif,nonaktif',
        ]);

        $harga->update($request->only('jenis_sampah', 'harga_per_kg', 'status'));

        return response()->json([
            'status'  => true,
            'message' => 'Informasi harga sampah berhasil diperbarui.'
        ]);
    }

    /**
     * Menonaktifkan jenis sampah agar tidak bisa dipilih saat transaksi.
     */
    public function destroy($id): JsonResponse
    {
        $harga = HargaSampah::findOrFail($id);
        $harga->update(['status' => 'nonaktif']);

        return response()->json([
            'status'  => true,
            'message' => 'Jenis sampah tersebut telah dinonaktifkan.'
        ]);
    }
}
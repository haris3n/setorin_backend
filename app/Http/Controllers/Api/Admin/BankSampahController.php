<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankSampah;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BankSampahController extends Controller
{
    /**
     * Menampilkan semua bank sampah beserta info harga & jadwalnya.
     */
    public function index(): JsonResponse
    {
        $banks = BankSampah::with(['hargaSampah', 'jadwalOperasional'])
            ->orderBy('nama_bank', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $banks
        ]);
    }

    /**
     * Menambahkan lokasi bank sampah baru.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama_bank'  => 'required|string|max:255',
            'alamat'     => 'required|string',
            'no_telepon' => 'nullable|string|max:15',
        ]);

        $bank = BankSampah::create($request->only('nama_bank', 'alamat', 'no_telepon'));

        return response()->json([
            'status'  => true,
            'message' => 'Bank sampah berhasil ditambahkan.',
            'data'    => $bank
        ], 201);
    }

    /**
     * Detail lengkap satu bank sampah (termasuk daftar petugasnya).
     */
    public function show($id): JsonResponse
    {
        $bank = BankSampah::with(['hargaSampah', 'jadwalOperasional', 'petugas.pengguna'])
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $bank
        ]);
    }

    /**
     * Update data bank sampah.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $bank = BankSampah::findOrFail($id);

        $request->validate([
            'nama_bank' => 'sometimes|string|max:255',
            'status'    => 'sometimes|in:aktif,nonaktif',
        ]);

        $bank->update($request->only('nama_bank', 'alamat', 'no_telepon', 'status'));

        return response()->json([
            'status'  => true,
            'message' => 'Data bank sampah berhasil diperbarui.'
        ]);
    }

    /**
     * Menonaktifkan bank sampah (Soft delete manual via status).
     */
    public function destroy($id): JsonResponse
    {
        $bank = BankSampah::findOrFail($id);
        $bank->update(['status' => 'nonaktif']);

        return response()->json([
            'status'  => true,
            'message' => 'Bank sampah telah dinonaktifkan.'
        ]);
    }
}
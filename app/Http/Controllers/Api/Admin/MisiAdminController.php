<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Misi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MisiAdminController extends Controller
{
    public function index(): JsonResponse
    {
        // withCount('klaim') membantu admin melihat seberapa populer misi tersebut
        $misi = Misi::withCount('klaim')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['status' => true, 'data' => $misi]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama_misi'   => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'reward_koin' => 'required|integer|min:1',
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        $misi = Misi::create($request->all());

        return response()->json([
            'status'  => true, 
            'message' => 'Misi baru berhasil diterbitkan.', 
            'data'    => $misi
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $misi = Misi::withCount('klaim')->findOrFail($id);
        return response()->json(['status' => true, 'data' => $misi]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $misi = Misi::findOrFail($id);
        
        $request->validate([
            'reward_koin' => 'sometimes|integer|min:1',
            'status_misi' => 'sometimes|in:aktif,nonaktif',
        ]);

        $misi->update($request->all());

        return response()->json(['status' => true, 'message' => 'Data misi berhasil diperbarui.']);
    }

    public function destroy($id): JsonResponse
    {
        $misi = Misi::findOrFail($id);
        // Soft delete manual dengan mengubah status
        $misi->update(['status_misi' => 'nonaktif']);

        return response()->json(['status' => true, 'message' => 'Misi telah dinonaktifkan.']);
    }
}
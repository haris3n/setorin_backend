<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Saldo, Nasabah, Petugas};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PenggunaController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna dengan filter role dan pencarian.
     * GET /api/admin/pengguna
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Filter berdasarkan Role
        if ($request->role) {
            $query->where('role', $request->role);
        }

        // Filter Pencarian (Nama, Email, atau No Telepon)
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('no_telepon', 'like', "%{$request->search}%");
            });
        }

        $users = $query->with('saldo')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data'   => $users
        ]);
    }

    /**
     * Admin menambahkan pengguna baru secara manual.
     * POST /api/admin/pengguna
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama'       => 'required|string|max:255',
            'email'      => 'required|email|unique:pengguna,email',
            'no_telepon' => 'required|string|unique:pengguna,no_telepon',
            'password'   => 'required|string|min:6',
            'role'       => 'required|in:nasabah,petugas,admin',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Buat User Utama
            $user = User::create([
                'nama'        => $request->nama,
                'email'       => $request->email,
                'no_telepon'  => $request->no_telepon,
                'password'    => Hash::make($request->password),
                'role'        => $request->role,
                'status_akun' => 'aktif',
            ]);

            // 2. Assign Role (Spatie Permission)
            $user->assignRole($request->role);

            // 3. Buat Wallet (Saldo) Otomatis
            Saldo::create([
                'id_pengguna'  => $user->id,
                'jumlah_saldo' => 0,
                'tgl_update'   => now()
            ]);

            // 4. Inisialisasi Tabel Pendukung sesuai Role
            if ($request->role === 'nasabah') {
                Nasabah::create(['id_pengguna' => $user->id]);
            } elseif ($request->role === 'petugas') {
                Petugas::create(['id_pengguna' => $user->id]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Pengguna berhasil ditambahkan.',
                'data'    => $user->load('saldo')
            ], 201);
        });
    }

    /**
     * Detail lengkap pengguna beserta relasinya.
     * GET /api/admin/pengguna/{id}
     */
    public function show($id): JsonResponse
    {
        $user = User::with(['saldo', 'nasabah.bankSampah', 'petugas.bankSampah'])
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $user
        ]);
    }

    /**
     * Update data pengguna oleh Admin.
     * PUT/PATCH /api/admin/pengguna/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'        => 'sometimes|string|max:255',
            'status_akun' => 'sometimes|in:aktif,nonaktif',
            'role'        => 'sometimes|in:nasabah,petugas,admin',
            'password'    => 'nullable|string|min:6'
        ]);

        $dataUpdate = $request->only('nama', 'status_akun', 'role');
        
        // Update password jika diisi
        if ($request->filled('password')) {
            $dataUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataUpdate);

        // Jika role berubah, sinkronkan di Spatie
        if ($request->role) {
            $user->syncRoles([$request->role]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Data pengguna berhasil diperbarui.'
        ]);
    }

    /**
     * Hapus pengguna (Soft Delete disarankan, tapi ini mengikuti CRUD dasar).
     * DELETE /api/admin/pengguna/{id}
     */
    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);
        
        // Proteksi: Admin tidak bisa menghapus dirinya sendiri melalui API ini
        if ($user->id === auth()->id()) {
            return response()->json(['status' => false, 'message' => 'Anda tidak bisa menghapus akun sendiri.'], 422);
        }

        $user->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Pengguna berhasil dihapus dari sistem.'
        ]);
    }
}
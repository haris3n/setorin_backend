<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontenEdukasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KontenEdukasiController extends Controller
{
    // GET /api/admin/konten-edukasi
    public function index(Request $request)
    {
        $query = KontenEdukasi::with('pengguna:id,nama');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->search) {
            $query->where('judul', 'like', "%{$request->search}%");
        }

        return response()->json([
            'status' => true,
            'data'   => $query->orderByDesc('tgl_publikasi')->paginate(10),
        ]);
    }

    // GET /api/admin/konten-edukasi/{id}
    public function show($id)
    {
        $konten = KontenEdukasi::with('pengguna:id,nama')->findOrFail($id);
        return response()->json(['status' => true, 'data' => $konten]);
    }

    // POST /api/admin/konten-edukasi
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'judul'         => 'required|string|max:255',
            'isi'           => 'required|string',
            'kategori'      => 'nullable|string|max:100',
            'tgl_publikasi' => 'nullable|date',
            'status'        => 'sometimes|in:draft,published,archived',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => false, 'errors' => $v->errors()], 422);
        }

        $konten = KontenEdukasi::create([
            'id_pengguna'   => $request->user()->id,
            'judul'         => $request->judul,
            'isi'           => $request->isi,
            'kategori'      => $request->kategori,
            'tgl_publikasi' => $request->tgl_publikasi ?? now()->toDateString(),
            'status'        => $request->status ?? 'draft',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Konten edukasi berhasil dibuat',
            'data'    => $konten,
        ], 201);
    }

    // PUT /api/admin/konten-edukasi/{id}
    public function update(Request $request, $id)
    {
        $konten = KontenEdukasi::findOrFail($id);

        $request->validate([
            'judul'         => 'sometimes|string|max:255',
            'isi'           => 'sometimes|string',
            'kategori'      => 'nullable|string|max:100',
            'tgl_publikasi' => 'nullable|date',
            'status'        => 'sometimes|in:draft,published,archived',
        ]);

        $konten->update($request->only('judul', 'isi', 'kategori', 'tgl_publikasi', 'status'));

        return response()->json(['status' => true, 'message' => 'Konten edukasi berhasil diperbarui']);
    }

    // PUT /api/admin/konten-edukasi/{id}/publish
    public function publish($id)
    {
        $konten = KontenEdukasi::findOrFail($id);
        $konten->update([
            'status'        => 'published',
            'tgl_publikasi' => now()->toDateString(),
        ]);

        return response()->json(['status' => true, 'message' => 'Konten berhasil dipublikasikan']);
    }

    // PUT /api/admin/konten-edukasi/{id}/archive
    public function archive($id)
    {
        KontenEdukasi::findOrFail($id)->update(['status' => 'archived']);
        return response()->json(['status' => true, 'message' => 'Konten berhasil diarsipkan']);
    }

    // DELETE /api/admin/konten-edukasi/{id}
    public function destroy($id)
    {
        KontenEdukasi::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Konten edukasi berhasil dihapus']);
    }
}
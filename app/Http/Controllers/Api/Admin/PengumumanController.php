<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(Pengumuman::STATUS))],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Pengumuman::with('pengguna:id,nama');
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $query->where('judul', 'like', '%'.$filters['search'].'%');
        }

        return response()->json(['status' => true, 'data' => $query->latest('id')->paginate(10)]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['id_pengguna'] = $request->user()->id;
        $pengumuman = Pengumuman::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Pengumuman berhasil dibuat',
            'data' => $pengumuman,
        ], 201);
    }

    public function show(Pengumuman $pengumuman)
    {
        return response()->json(['status' => true, 'data' => $pengumuman->load('pengguna:id,nama')]);
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $pengumuman->update($this->validatedData($request, $pengumuman));

        return response()->json([
            'status' => true,
            'message' => 'Pengumuman berhasil diperbarui',
            'data' => $pengumuman,
        ]);
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return response()->json(['status' => true, 'message' => 'Pengumuman berhasil dihapus']);
    }

    private function validatedData(Request $request, ?Pengumuman $pengumuman = null): array
    {
        // Validate the complete period even when PATCH changes only one date.
        $existing = $pengumuman ? [
            'judul' => $pengumuman->judul,
            'isi' => $pengumuman->isi,
            'status' => $pengumuman->status,
            'tanggal_mulai' => $pengumuman->tanggal_mulai->toDateString(),
            'tanggal_selesai' => $pengumuman->tanggal_selesai?->toDateString(),
        ] : ['status' => 'draft'];

        return Validator::make(array_merge($existing, $request->only([
            'judul', 'isi', 'status', 'tanggal_mulai', 'tanggal_selesai',
        ])), [
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'status' => ['required', Rule::in(array_keys(Pengumuman::STATUS))],
            'tanggal_mulai' => ['required', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
        ])->validate();
    }
}

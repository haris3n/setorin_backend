<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;

class PengumumanController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Pengumuman::aktif()
                ->select('id', 'judul', 'isi', 'tanggal_mulai', 'tanggal_selesai')
                ->orderByDesc('tanggal_mulai')->orderByDesc('id')->paginate(10),
        ]);
    }
}

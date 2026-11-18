<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    // Jumlah buku per kategori
    public function bukuPerKategori()
    {
        $data = DB::table('bukus')
            ->join('kategoris', 'kategoris.id', '=', 'bukus.kategori_id')
            ->select('kategoris.nama as kategori', DB::raw('COUNT(*) as jumlah_buku'))
            ->groupBy('kategoris.nama')
            ->get();

        return response()->json(['data' => $data], 200);
    }

    // Total peminjaman per buku
    public function peminjamanPerBuku()
    {
        $data = Buku::withCount('peminjamans')
            ->orderByDesc('peminjamans_count')
            ->get(['id', 'judul']);

        return response()->json(['data' => $data], 200);
    }
}
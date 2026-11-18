<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function cari(Request $request)
    {
        $judul = $request->query('judul');
    
        $hasil = DB::select(
            'SELECT id, kategori_id, judul, penulis, tahun_terbit FROM bukus WHERE judul = ?',
            [$judul]
        );
    
        return response()->json($hasil);
    }


    // Halaman /buku: kerangka saja, data buku diambil JavaScript dari API
    public function index()
    {
        return view('databuku.index', [
            'kategoris' => Kategori::orderBy('nama')->get(),
        ]);
    }

    // Halaman detail tetap seperti Modul 1-3
    public function show($id_buku)
    {
        $buku = Buku::with('kategori')->find($id_buku);

        return view('databuku.show', compact('buku'));
    }

    // Halaman kelola buku (admin dan petugas)
    public function kelola()
    {
        $bukus = Buku::with('kategori')->orderBy('judul')->get();
        return view('databuku.kelola', compact('bukus'));
    }

    public function create()
    {
        return view('databuku.form', [
            'buku'      => new Buku(),
            'kategoris' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Buku::create($this->validasi($request));
        return redirect()->route('buku.kelola')->with('status', 'Buku berhasil ditambahkan.');
    }

    public function edit($id_buku)
    {
        return view('databuku.form', [
            'buku'      => Buku::findOrFail($id_buku),
            'kategoris' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, $id_buku)
    {
        Buku::findOrFail($id_buku)->update($this->validasi($request));
        return redirect()->route('buku.kelola')->with('status', 'Buku berhasil diperbarui.');
    }

    public function destroy($id_buku)
    {
        Buku::findOrFail($id_buku)->delete();
        return redirect()->route('buku.kelola')->with('status', 'Buku berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'kategori_id'  => ['required', 'exists:kategoris,id'],
            'judul'        => ['required', 'string', 'max:255'],
            'penulis'      => ['required', 'string', 'max:255'],
            'tahun_terbit' => ['required', 'integer', 'min:1000', 'max:' . date('Y')],
        ]);
    }
}
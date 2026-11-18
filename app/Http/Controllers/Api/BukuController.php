<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BukuResource;
use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $buku = Buku::with('kategori')
            ->withCount('peminjamans')
            ->when($request->filled('cari'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('judul', 'like', '%' . $request->cari . '%')
                      ->orWhere('penulis', 'like', '%' . $request->cari . '%');
                });
            })
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori_id', $request->kategori))
            ->when($request->filled('tahun'), fn ($q) => $q->where('tahun_terbit', $request->tahun))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return BukuResource::collection($buku);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(), $this->pesan(), $this->atribut());

        $buku = Buku::create($data);

        return (new BukuResource($buku->load('kategori')))
            ->additional(['message' => 'Buku berhasil ditambahkan'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Buku $buku)
    {
        return new BukuResource($buku->load(['kategori', 'peminjamans']));
    }

    public function update(Request $request, Buku $buku)
    {
        $data = $request->validate($this->aturan(true), $this->pesan(), $this->atribut());

        $buku->update($data);

        return (new BukuResource($buku->load('kategori')))
            ->additional(['message' => 'Buku berhasil diperbarui']);
    }

    public function destroy(Buku $buku)
    {
        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus'], 200);
    }

    private function aturan(bool $update = false): array
    {
        $rules = [
            'kategori_id'  => ['required', 'exists:kategoris,id'],
            'judul'        => ['required', 'string', 'min:3', 'max:150'],
            'penulis'      => ['required', 'string', 'min:3', 'max:100'],
            'tahun_terbit' => ['required', 'integer', 'between:1900,' . date('Y')],
        ];

        // Saat update, field hanya diperiksa kalau dikirim (update sebagian tetap 200)
        if ($update) {
            $rules = array_map(fn ($r) => array_merge(['sometimes'], $r), $rules);
        }

        return $rules;
    }

    private function pesan(): array
    {
        return [
            'required'             => ':attribute wajib diisi.',
            'judul.string'         => 'Judul buku harus berupa teks.',
            'judul.min'            => 'Judul buku minimal :min karakter.',
            'judul.max'            => 'Judul buku maksimal :max karakter.',
            'penulis.string'       => 'Penulis harus berupa teks.',
            'penulis.min'          => 'Nama penulis minimal :min karakter.',
            'penulis.max'          => 'Nama penulis maksimal :max karakter.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka bulat.',
            'tahun_terbit.between' => 'Tahun terbit harus antara :min sampai :max.',
            'kategori_id.exists'   => 'Kategori yang dipilih tidak valid.',
        ];
    }

    private function atribut(): array
    {
        return [
            'kategori_id'  => 'kategori',
            'tahun_terbit' => 'tahun terbit',
        ];
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    // Semua peminjaman (admin dan petugas)
    public function index()
    {
        $data = Peminjaman::with(['anggota', 'buku'])->latest()->get();
        return view('peminjaman.index', compact('data'));
    }

    // Riwayat milik sendiri
    public function riwayat()
    {
        $anggota = auth()->user()->anggota;

        $data = $anggota
            ? Peminjaman::with('buku')->where('anggota_id', $anggota->id)->latest()->get()
            : collect();

        return view('peminjaman.riwayat', compact('data'));
    }

    // Riwayat anggota tertentu: hanya pemilik, atau staf yang berhak
    public function lihatRiwayat(Anggota $anggota)
    {
        Gate::authorize('view', $anggota);

        $data = Peminjaman::with('buku')->where('anggota_id', $anggota->id)->latest()->get();

        return view('peminjaman.riwayat', compact('data'));
    }

    // Siswa mengajukan peminjaman
    public function ajukan(Request $request, $id_buku)
    {
        $anggota = auth()->user()->anggota;
        abort_unless($anggota, 403, 'Akun belum terhubung ke data anggota.');

        \App\Models\Buku::findOrFail($id_buku);

        Peminjaman::create([
            'anggota_id'      => $anggota->id,
            'buku_id'         => $id_buku,

            'tanggal_pinjam'  => now()->toDateString(),
            'status'          => 'diajukan',
        ]);

        return redirect()->route('riwayat.index')->with('status', 'Pengajuan peminjaman terkirim.');
    }
    // Petugas/admin menyetujui
    public function setujui(Peminjaman $peminjaman)
    {
        abort_unless($peminjaman->status === 'diajukan', 422);
        $peminjaman->update(['status' => 'dipinjam', 'tanggal_pinjam' => now()->toDateString()]);

        return back()->with('status', 'Peminjaman disetujui.');
    }

    // Petugas/admin mencatat pengembalian
    public function kembalikan(Peminjaman $peminjaman)
    {
        abort_unless($peminjaman->status === 'dipinjam', 422);
        $peminjaman->update(['status' => 'dikembalikan', 'tanggal_kembali' => now()->toDateString()]);

        return back()->with('status', 'Pengembalian dicatat.');
    }

    // A4 AMAN: kepemilikan diperiksa oleh Policy
    public function show($id)
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku'])->findOrFail($id);

        Gate::authorize('view', $peminjaman);

        return view('peminjaman.show', compact('peminjaman'));
    }
}
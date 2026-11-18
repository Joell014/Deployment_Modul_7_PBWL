@extends('layouts.perpustakaan')

@section('title', 'Detail Buku')

@section('content')

    @if ($buku)

        <div class="detail-card">

            <div class="detail-icon">
                📖
            </div>

            <div class="detail-info">

                <span class="category">
                    {{ $buku->kategori->nama }}
                </span>

                <h2>
                    {{ $buku->judul }}
                </h2>

                <p>
                    <strong>ID Buku:</strong>
                    {{ $buku->id }}
                </p>

                <p>
                    <strong>Penulis:</strong>
                    {{ $buku->penulis }}
                </p>

                <p>
                    <strong>Tahun Terbit:</strong>
                    {{ $buku->tahun_terbit }}
                </p>

                <p>
                    <strong>Kategori:</strong>
                    {{ $buku->kategori->nama }}
                </p>


                @auth
                    @can('mengajukan peminjaman buku')
                    <form method="POST" action="{{ route('peminjaman.ajukan', $buku->id) }}" style="margin-bottom: 12px;">
                        @csrf
                        <button type="submit" class="btn">Ajukan Peminjaman</button>
                    </form>
                    @endcan
                @endauth
                <a href="{{ route('buku.index') }}" class="btn">
                    ← Kembali ke Daftar Buku
                </a>

            </div>

        </div>

    @else

        <div class="not-found">

            <h2>📕 Buku Tidak Ditemukan</h2>

            <p>
                Maaf, buku yang Anda cari tidak tersedia.
            </p>

            <a href="{{ route('buku.index') }}" class="btn">
                Kembali ke Daftar Buku
            </a>

        </div>

    @endif

@endsection
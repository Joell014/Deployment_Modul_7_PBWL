<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Perpustakaan</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach ([
                ['Total Buku', $totalBuku, 'blue'],
                ['Total Anggota', $totalAnggota, 'green'],
                ['Total Peminjaman', $totalPinjam, 'purple'],
                ['Sedang Dipinjam', $sedangDipinjam, 'amber'],
                ['Sudah Kembali', $sudahKembali, 'teal'],
            ] as [$label, $nilai, $warna])
                <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-{{ $warna }}-500">
                    <p class="text-sm text-gray-500">{{ $label }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $nilai }}</p>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
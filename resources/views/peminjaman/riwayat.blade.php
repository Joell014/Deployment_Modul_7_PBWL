<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Peminjaman Saya</h2>
    </x-slot>

    @php
        $warna = [
            'diajukan'     => 'bg-blue-100 text-blue-700',
            'dipinjam'     => 'bg-amber-100 text-amber-700',
            'dikembalikan' => 'bg-green-100 text-green-700',
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Buku</th>
                            <th class="px-4 py-3">Tgl Pinjam</th>
                            <th class="px-4 py-3">Tgl Kembali</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($data as $p)
                        <tr>
                            <td class="px-4 py-3">{{ $p->buku->judul ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $p->tanggal_pinjam }}</td>
                            <td class="px-4 py-3">{{ $p->tanggal_kembali ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs {{ $warna[$p->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
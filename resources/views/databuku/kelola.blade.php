<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Buku</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
            @endif

            @can('kelola-buku')
                <a href="{{ route('buku.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded">+ Tambah Buku</a>
            @endcan

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Judul</th>
                            <th class="px-4 py-3">Penulis</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Tahun</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($bukus as $b)
                        <tr>
                            <td class="px-4 py-3">{{ $b->judul }}</td>                            <td class="px-4 py-3">{{ $b->penulis }}</td>
                            <td class="px-4 py-3">{{ $b->kategori->nama ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $b->tahun_terbit }}</td>
                            <td class="px-4 py-3">
                                @can('kelola-buku')
                                <div class="flex gap-3">
                                    <a href="{{ route('buku.edit', $b->id) }}" class="text-indigo-600 underline">Ubah</a>
                                    
                                    <form method="POST" action="{{ route('buku.destroy', $b->id) }}"
                                          onsubmit="return confirm('Hapus buku ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 underline">Hapus</button>
                                    </form>
                                
                                </div>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada buku.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
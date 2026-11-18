<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $buku->exists ? 'Ubah Buku' : 'Tambah Buku' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST"
                  action="{{ $buku->exists ? route('buku.update', $buku->id) : route('buku.store') }}"
                  class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf
                @if ($buku->exists) @method('PUT') @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="kategori_id" class="mt-1 w-full border-gray-300 rounded">
                        @foreach ($kategoris as $k)
                            <option value="{{ $k->id }}" @selected(old('kategori_id', $buku->kategori_id) == $k->id)>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Judul</label>
                    <input type="text" name="judul" value="{{ old('judul', $buku->judul) }}" class="mt-1 w-full border-gray-300 rounded">
                    @error('judul') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Penulis</label>
                    <input type="text" name="penulis" value="{{ old('penulis', $buku->penulis) }}" class="mt-1 w-full border-gray-300 rounded">
                    @error('penulis') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" class="mt-1 w-full border-gray-300 rounded">
                    @error('tahun_terbit') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
                    <a href="{{ route('buku.kelola') }}" class="px-4 py-2 border rounded text-gray-700">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
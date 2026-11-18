<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengguna & Role</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
            @endif
            @error('role')
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ $message }}</div>
            @enderror

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Ubah Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($users as $u)
                        <tr>
                            <td class="px-4 py-3">{{ $u->name }}</td>
                            <td class="px-4 py-3">{{ $u->email }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                    {{ $u->roles->pluck('name')->implode(', ') ?: '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @can('kelola role pengguna')
                                <form method="POST" action="{{ route('admin.users.role', $u) }}" class="flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="border-gray-300 rounded text-sm">
                                        @foreach ($roles as $r)
                                            <option value="{{ $r }}" @selected($u->hasRole($r))>{{ $r }}</option>
                                        @endforeach
                                    </select>
                                    <button class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">Simpan</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
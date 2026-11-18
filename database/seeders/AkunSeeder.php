<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AkunSeeder extends Seeder
{
    public function run(): void
    {
        // Password admin dibaca dari .env, tidak ditulis di kode
        $passwordAdmin = env('ADMIN_PASSWORD');

        if (! $passwordAdmin || $passwordAdmin === 'password') {
            throw new \RuntimeException('Isi ADMIN_PASSWORD di .env dengan password yang kuat.');
        }

        $akun = [
            ['Admin', 'admin@perpus.test', 'admin'],
            ['Petugas', 'petugas@perpus.test', 'petugas'],
            ['Siswa', 'siswa@perpus.test', 'siswa'],
        ];

        foreach ($akun as [$nama, $email, $role]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $nama, 'password' => Hash::make('password')]
            );

            if ($role === 'admin') {
                $user->update(['password' => Hash::make($passwordAdmin)]);
            }

            $user->syncRoles([$role]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan cache bawaan Spatie supaya data baru langsung terbaca
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Permission yang dimiliki SEMUA role (Admin, Petugas, Siswa)
        $untukSemuaRole = [
            'lihat daftar buku',                    
            'mengajukan peminjaman buku',           
            'melihat status peminjaman sendiri',    
            'melihat riwayat peminjaman sendiri',   
            'lihat profil sendiri',                 
            'ubah profil sendiri',                  
        ];

        // Permission operasional: Admin dan Petugas saja
        $operasional = [
            'kelola data buku',                     
            'kelola peminjaman',                    
            'kelola pengembalian',                  
            'memproses persetujuan peminjaman',     
            'mencatat pengembalian buku',           
            'lihat laporan perpustakaan',           
        ];

        // Permission khusus
        $khususAdmin = [
            'akses dashboard admin',                
            'kelola data pengguna',                 
            'kelola role pengguna',                 
        ];
        $khususPetugas = [
            'akses dashboard petugas',              
        ];

        // 1) Buat permission
        $semuaPermission = array_merge($untukSemuaRole, $operasional, $khususAdmin, $khususPetugas);

        foreach ($semuaPermission as $nama) {
            Permission::firstOrCreate(['name' => $nama, 'guard_name' => 'web']);
        }

        // 2) Buat 3 role 
        $hakAkses = [
            'admin'   => array_merge($untukSemuaRole, $operasional, $khususAdmin),
            'petugas' => array_merge($untukSemuaRole, $operasional, $khususPetugas),
            'siswa'   => $untukSemuaRole,
        ];

        foreach ($hakAkses as $namaRole => $daftarPermission) {
            $role = Role::firstOrCreate(['name' => $namaRole, 'guard_name' => 'web']);
            $role->syncPermissions($daftarPermission);
        }
    }
}

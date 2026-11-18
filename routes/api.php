<?php

use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\RekapController;
use Illuminate\Support\Facades\Route;

// Baca data: tetap terbuka (dipakai halaman /buku yang sudah di balik login)
Route::apiResource('buku', BukuController::class)
    ->only(['index', 'show'])
    ->names('api.buku');

// Tulis data: wajib login dan punya permission
Route::middleware(['auth:sanctum', 'can:kelola-buku'])->group(function () {
    Route::apiResource('buku', BukuController::class)
        ->only(['store', 'update', 'destroy'])
        ->names('api.buku');
});

Route::get('rekap/buku-per-kategori', [RekapController::class, 'bukuPerKategori'])
    ->name('api.rekap.buku-per-kategori');

Route::get('rekap/peminjaman-per-buku', [RekapController::class, 'peminjamanPerBuku'])
    ->name('api.rekap.peminjaman-per-buku');
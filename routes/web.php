<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CekRoleController;
use App\Http\Controllers\Peminjam\BukuController as PeminjamBukuController;
use App\Http\Controllers\Peminjam\KeranjangController;
use App\Http\Controllers\Petugas\BukuController;
use App\Http\Controllers\Petugas\ChartController;
use App\Http\Controllers\Petugas\DashboardController;
use App\Http\Controllers\Petugas\KategoriController;
use App\Http\Controllers\Petugas\PenerbitController;
use App\Http\Controllers\Petugas\RakController;
use App\Http\Controllers\Petugas\TransaksiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Lab;

// Route::get('/lab-test', function () {
//     return \App\Helpers\Lab::mode() ? 'LAB MODE AKTIF' : 'LAB MODE MATI';
// });
// Route::get('/lab-a02-check', function () {
//     return [
//         'env' => config('app.env'),
//         'debug' => config('app.debug'),
//         'lab_mode' => env('LAB_MODE'),
//     ];
// });
/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', PeminjamBukuController::class);

Auth::routes();

Route::get('/lab-exception', function () {

    // MODE AMAN → route tidak tersedia
    if (!Lab::mode()) {
        abort(404);
    }

    // LAB MODE → sengaja lempar exception
    throw new Exception('A02 Security Misconfiguration');

});
/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/cek-role', CekRoleController::class);
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
        ->name('home');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */
    if (Lab::mode()) {
        // LAB MODE (RENTAN)
        // Semua user login bisa akses manajemen user
        Route::get('/user', UserController::class);
    } else {
        // MODE AMAN
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/user', UserController::class);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN + PETUGAS
    |--------------------------------------------------------------------------
    */
    if (Lab::mode()) {
        // LAB MODE (RENTAN)
        // Semua user login (termasuk peminjam) bisa akses fitur petugas
        Route::get('/dashboard', DashboardController::class);
        Route::get('/kategori', KategoriController::class);
        Route::get('/rak', RakController::class);
        Route::get('/penerbit', PenerbitController::class);
        Route::get('/buku', BukuController::class);
        Route::get('/transaksi', TransaksiController::class);
        Route::get('/chart', ChartController::class);
    } else {
        // MODE AMAN
        Route::middleware(['role:admin|petugas'])->group(function () {
            Route::get('/dashboard', DashboardController::class);
            Route::get('/kategori', KategoriController::class);
            Route::get('/rak', RakController::class);
            Route::get('/penerbit', PenerbitController::class);
            Route::get('/buku', BukuController::class);
            Route::get('/transaksi', TransaksiController::class);
            Route::get('/chart', ChartController::class);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PEMINJAMAN DETAIL (IDOR)
    |--------------------------------------------------------------------------
    */
    Route::get('/peminjaman/{id}', [TransaksiController::class, 'show'])
        ->middleware('auth');

    /*
    |--------------------------------------------------------------------------
    | PEMINJAM ONLY
    |--------------------------------------------------------------------------
    */
    if (Lab::mode()) {
        // LAB MODE (RENTAN)
        // Role tidak dibatasi (semua login bisa akses keranjang)
        Route::get('/keranjang', KeranjangController::class);
    } else {
        // MODE AMAN
        Route::middleware(['role:peminjam'])->group(function () {
            Route::get('/keranjang', KeranjangController::class);
        });
    }
    // Route::get('/', PeminjamBukuController::class);

    // Auth::routes();

    // Route::middleware(['auth'])->group(function () {
//     Route::get('/cek-role', CekRoleController::class);
//     Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    //     // role admin dan petugas
//     Route::middleware(['role:admin|petugas'])->group(function () {
//         Route::get('/dashboard', DashboardController::class);

    //         Route::get('/kategori', KategoriController::class);
//         Route::get('/rak', RakController::class);
//         Route::get('/penerbit', PenerbitController::class);
//         Route::get('/buku', BukuController::class);
//         Route::get('/transaksi', TransaksiController::class);
//         Route::get('/chart', ChartController::class);
//     });

    //     // role peminjam
//     Route::middleware(['role:peminjam'])->group(function () {
//         Route::get('/keranjang', KeranjangController::class);
//     });



    //     // ADMIN
//     if (Lab::mode()) {
//         // LAB MODE (RENTAN)
//         Route::get('/user', UserController::class)
//             ->middleware('auth');
//     } else {
//         // MODE AMAN
//         Route::middleware(['role:admin'])->group(function () {
//             Route::get('/user', UserController::class);
//         });
//     }
//     // // role admin
//     // Route::middleware(['role:admin'])->group(function () {
//     //     Route::get('/user', UserController::class);
//     // });
});
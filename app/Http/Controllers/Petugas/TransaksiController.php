<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Lab;
use App\Models\Peminjaman;

class TransaksiController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * MODE RENTAN (LAB):
     * - Tidak ada pengecekan role
     * - Semua user login bisa akses halaman transaksi
     *
     * MODE AMAN:
     * - Akses dibatasi via middleware route (admin|petugas)
     */
    public function __invoke(Request $request)
    {
        return view('petugas.transaksi.index');
    }

    /**
     * ===============================
     * DETAIL PEMINJAMAN (IDOR)
     * ===============================
     *
     * LAB MODE (RENTAN):
     * - Tidak cek pemilik data
     *
     * MODE AMAN:
     * - Cek peminjaman milik user
     */
    public function show($id)
    {
        if (Lab::mode()) {
            //LAB MODE — IDOR
            return Peminjaman::with('detail_peminjaman.buku')
                ->findOrFail($id);
        }

        //MODE AMAN
        return Peminjaman::with('detail_peminjaman.buku')
            ->where('id', $id)
            ->where('peminjam_id', auth()->id())
            ->firstOrFail();
    }
    // /**
    //  * Handle the incoming request.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function __invoke(Request $request)
    // {
    //     return view('petugas/transaksi/index');
    // }
}

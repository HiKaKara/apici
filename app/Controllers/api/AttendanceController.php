<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Config\Services; // Penting untuk mendapatkan request

class AttendanceController extends ResourceController
{
    // ... metode lain yang sudah ada ...

    /**
     * Memvalidasi apakah IP pengguna cocok dengan IP kantor untuk WFO.
     * Ini bertindak sebagai gerbang sebelum menampilkan halaman presensi.
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function validateWfoIp()
    {
        // 1. Muat konfigurasi IP kantor
        $officeConfig = config('Office');
        $allowedIps = $officeConfig->allowedIps;

        // 2. Dapatkan alamat IP pengguna saat ini
        $userIp = Services::request()->getIPAddress();

        // 3. Periksa apakah IP pengguna ada di dalam daftar yang diizinkan
        if (in_array($userIp, $allowedIps)) {
            // Jika IP diizinkan, kirim respons sukses
            return $this->respond([
                'status' => 200,
                'message' => 'Verifikasi IP berhasil. Anda dapat melanjutkan presensi WFO.'
            ]);
        } else {
            // Jika IP tidak diizinkan, kirim respons error 403 (Forbidden)
            return $this->failForbidden('Akses Ditolak. Anda harus menggunakan jaringan internet kantor untuk melakukan presensi WFO.');
        }
    }
}
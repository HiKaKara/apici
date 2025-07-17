<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Office extends BaseConfig
{
    /**
     * --------------------------------------------------------------------
     * Alamat IP Kantor yang Diizinkan untuk Presensi WFO
     * --------------------------------------------------------------------
     *
     * Daftarkan semua alamat IP statis kantor Anda di sini.
     * Presensi WFO hanya bisa dilakukan dari salah satu IP ini.
     */
    public array $allowedIps = [
                         // Contoh IP publik kantor A
        '140.213.7.28',  // Contoh IP publik kantor B
        '127.0.0.1',      // IP untuk development di localhost
        '::1'             // IP untuk development di localhost (IPv6)
    ];
}
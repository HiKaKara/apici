<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    /**
     * Endpoint untuk login pengguna.
     * Menerima email dan password melalui POST request.
     */
    public function login()
    {
        $model = new UserModel();

        // Ambil data dari body request
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // Cari pengguna berdasarkan email
        $user = $model->where('email', $email)->first();

        // 1. Periksa apakah pengguna ditemukan
        if (!$user) {
            return $this->failNotFound('Email tidak ditemukan.');
        }

        // 2. Verifikasi password yang di-hash
        if (!password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Password salah.');
        }

        // 3. Jika login berhasil
        // Catatan: Di aplikasi production, Anda sebaiknya menghasilkan
        // dan mengembalikan token (seperti JWT) di sini.
        $response = [
            'status'   => 200,
            'messages' => [
                'success' => 'Login Berhasil'
            ],
            'user'     => [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email']
            ]
        ];

        return $this->respond($response);
    }
}
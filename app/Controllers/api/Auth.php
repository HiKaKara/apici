<?php

namespace App\Controllers\api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class Auth extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function login()
    {
        $data = $this->request->getJSON();
        $email = $data->email ?? null;
        $password = $data->password ?? null;

        if ($email === null || $password === null) {
            return $this->fail('Email dan password harus diisi.', 400);
        }

        // Cari pengguna berdasarkan email
        $user = $this->model->where('email', $email)->first();

        // Jika pengguna tidak ditemukan
        if (!$user) {
            return $this->failNotFound('Email tidak ditemukan.');
        }

        // --- PERBAIKAN UTAMA ADA DI SINI ---
        // Verifikasi password yang diinput dengan hash di database
        if (!password_verify($password, $user['password'])) {
            return $this->fail('Password yang Anda masukkan salah.', 401); // 401 Unauthorized
        }
        
        // Hapus password dari data yang dikirim kembali demi keamanan
        unset($user['password']);

        // Jika berhasil
        return $this->respond([
            'status' => 'success',
            'message' => 'Login berhasil',
            'user' => $user
        ], 200);
    }
}

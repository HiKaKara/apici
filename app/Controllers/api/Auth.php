<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class Auth extends ResourceController
{
    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $model = new UserModel();
        $user = $model->where('email', $this->request->getVar('email'))->first();

        if (!$user) {
            return $this->failNotFound('Email tidak ditemukan.');
        }

        // Ganti password_verify jika Anda tidak menggunakan hashing bawaan CI4
        if (!password_verify($this->request->getVar('password'), $user['password'])) {
            return $this->fail('Password salah.', 401);
        }
        
        // --- PERBAIKAN PENTING PADA STRUKTUR RESPONS ---
        // Hapus password dari data yang akan dikirim kembali
        unset($user['password']);

        return $this->respond([
            'status'  => 200,
            'message' => 'Login berhasil',
            // Selalu bungkus data pengguna di dalam objek 'user'
            'user'    => $user 
        ]);
        // ---------------------------------------------
    }
}

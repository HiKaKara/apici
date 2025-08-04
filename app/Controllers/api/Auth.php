<?php

namespace App\Controllers\api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class Auth extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function login(){
        $data = $this->request->getJSON();
        $email = $data->email ?? null;
        $password = $data->password ?? null;

        if ($email === null || $password === null) {
            return $this->fail('Email dan password harus diisi.', 400);
        }

        // Cari pengguna berdasarkan email
        $user = $this->model->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Email atau password salah');
        }

        unset($user->password);

        // Jika berhasil
        return $this->respond([
            'status' => 'success',
            'message' => 'Login berhasil',
            'user' => $user
        ], 200);
    }
}

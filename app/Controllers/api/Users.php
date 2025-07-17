<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Users extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json'; // Otomatis response dalam format JSON

    /**
     * Mengembalikan semua data pengguna.
     * Endpoint: GET /api/users
     */
    public function index()
    {
        // Ambil semua data dari model dan kirim sebagai response
        return $this->respond($this->model->findAll());
    }
    public function show($id = null)
{
    $model = new \App\Models\UserModel();
    $data = $model->find($id);
    if ($data) {
        return $this->respond($data);
    }
    return $this->failNotFound('Pengguna dengan ID ' . $id . ' tidak ditemukan.');
}
public function uploadProfilePicture($id = null)
{
    $model = new \App\Models\UserModel();
    $user = $model->find($id);

    if (!$user) {
        return $this->failNotFound('Pengguna tidak ditemukan.');
    }

    $file = $this->request->getFile('profile_picture');

    if (!$file->isValid() || $file->hasMoved()) {
        return $this->fail($file->getErrorString() . '(' . $file->getError() . ')');
    }

    // Buat nama file baru yang unik
    $newName = $file->getRandomName();
    // Pindahkan file ke folder public/uploads/avatars
    $file->move(FCPATH . 'uploads/avatars', $newName);

    // Simpan nama file ke database
    $model->update($id, ['profile_picture' => $newName]);

    return $this->respond([
        'status' => 200,
        'message' => 'Foto profil berhasil diperbarui.',
        'file_path' => '/uploads/avatars/' . $newName 
    ]);
}
}
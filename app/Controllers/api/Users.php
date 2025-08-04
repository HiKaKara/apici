<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Users extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json'; 

    public function index()    {
        return $this->respond($this->model->findAll());
    }
    public function show($id = null){
    $model = new \App\Models\UserModel();
    $data = $model->find($id);
    if ($data) {
        return $this->respond($data);
    }
    return $this->failNotFound('Pengguna dengan ID ' . $id . ' tidak ditemukan.');
}
public function uploadProfilePicture($id = null){
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
public function create(){
    // Gunakan model yang sudah didefinisikan di properti kelas
    $data = $this->request->getJSON(true);
    if (empty($data)) {
        return $this->fail('Data JSON yang dikirim kosong atau tidak valid.', 400);
    }

    // Definisikan aturan validasi
    $rules = [
        'name'     => 'required',
        'email'    => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]',
        'role'     => 'required|in_list[Admin,Pegawai]',
        'position' => 'required',
    ];

    // Lakukan validasi
    if (!$this->validateData($data, $rules)) {
        return $this->fail($this->validator->getErrors(), 400);
    }

    // Hash password
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

    // Coba insert data
    if ($this->model->insert($data)) {
        return $this->respondCreated(['status' => 201, 'messages' => ['success' => 'User created successfully']]);
    }

    // Jika gagal, kembalikan error dari model
    return $this->fail($this->model->errors());
}
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['employee_id', 'name', 'email', 'password', 'role', 'position'];

    // Nonaktifkan timestamp jika tidak ada kolom created_at/updated_at
    protected $useTimestamps    = false; 

    // Daftarkan callback untuk dijalankan sebelum proses insert
    protected $beforeInsert     = ['generateEmployeeId', 'hashPassword'];

    /**
     * Aksi utama untuk membuat pengguna baru.
     * Menangani validasi dan penyisipan data.
     *
     * @param array $data Data pengguna dari controller
     * @return int|bool ID pengguna baru jika berhasil, false jika gagal.
     */
    public function createUser(array $data)
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'     => 'required',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,pegawai]',
            'position' => 'required',
        ]);

        if (!$validation->run($data)) {
            // Set error pada model agar bisa diambil oleh controller
            $this->errors = $validation->getErrors();
            return false;
        }

        // Panggil insert, callback 'beforeInsert' akan berjalan otomatis
        return $this->insert($data);
    }

    /**
     * Aksi utama untuk memperbarui pengguna.
     * Menangani validasi dan pembaruan data, termasuk password opsional.
     *
     * @param int $id ID pengguna yang akan diupdate
     * @param array $data Data baru dari controller
     * @return bool True jika berhasil, false jika gagal.
     */
    public function updateUser(int $id, array $data)
    {
        // Pastikan pengguna ada
        if (!$this->find($id)) {
            $this->errors = ['user' => 'User tidak ditemukan.'];
            return false;
        }

        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required',
            'role' => 'required|in_list[admin,pegawai]'
        ];

        // Jika ada password baru, hash dan tambahkan ke aturan validasi
        if (!empty($data['password'])) {
            $rules['password'] = 'min_length[6]';
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            // Hapus password dari data jika tidak diubah
            unset($data['password']);
        }
        
        $validation->setRules($rules);

        if (!$validation->run($data)) {
            $this->errors = $validation->getErrors();
            return false;
        }

        return $this->update($id, $data);
    }

    /**
     * Callback untuk membuat employee_id unik secara otomatis.
     */
    protected function generateEmployeeId(array $data)
    {
        if (isset($data['data']['employee_id'])) {
            return $data;
        }

        $prefix = 'CDGM';
        $lastUser = $this->orderBy('id', 'DESC')->first();
        $lastId = $lastUser ? (int)substr($lastUser['employee_id'], -4) : 0;
        $newIdNumber = str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        $data['data']['employee_id'] = $prefix . $newIdNumber;

        return $data;
    }

    /**
     * Callback untuk hash password secara otomatis.
     */
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }
        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }
}

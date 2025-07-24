<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleToUsersTable extends Migration
{
    public function up()
    {
        $fields = [
            'role' => [
                'type'       => "ENUM('Admin', 'Pegawai')", // Tentukan pilihan role di sini
                'default'    => 'Pegawai',
                'after'      => 'password', // Atur posisi kolom setelah 'password'
                'null'       => false,
            ],
        ];

        // Ganti 'users' jika nama tabel Anda berbeda
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        // Ganti 'users' jika nama tabel Anda berbeda
        $this->forge->dropColumn('users', 'role');
    }
}
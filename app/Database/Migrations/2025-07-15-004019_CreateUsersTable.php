<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    // app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateUsersTable.php
public function up()
{
    $this->forge->addField([
        'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
        'name' => ['type' => 'VARCHAR', 'constraint' => '255'],
        'email' => ['type' => 'VARCHAR', 'constraint' => '255', 'unique' => true],
        'password' => ['type' => 'VARCHAR', 'constraint' => '255'],
        'employee_id' => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => true, 'null' => true],
        'position' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
        'created_at' => ['type' => 'DATETIME', 'null' => true],
        'updated_at' => ['type' => 'DATETIME', 'null' => true],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('users');
}

public function down()
{
    $this->forge->dropTable('users');
}
}

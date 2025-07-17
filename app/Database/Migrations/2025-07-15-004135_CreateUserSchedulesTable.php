<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserSchedulesTable extends Migration
{
    // app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateUserSchedulesTable.php
public function up()
{
    $this->forge->addField([
        'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
        'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        'shift_id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true],
        'effective_date' => ['type' => 'DATE'], // Tanggal mulai berlakunya jadwal ini
    ]);
    $this->forge->addKey('id', true);
    $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('shift_id', 'shifts', 'id', 'CASCADE', 'CASCADE');
    $this->forge->createTable('user_schedules');
}

public function down()
{
    $this->forge->dropTable('user_schedules');
}
}

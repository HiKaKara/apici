<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShiftsTable extends Migration
{
    // app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateShiftsTable.php
public function up()
{
    $this->forge->addField([
        'id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
        'name' => ['type' => 'VARCHAR', 'constraint' => '100'], // e.g., "Office Hour", "Shift Pagi"
        'start_time' => ['type' => 'TIME'],
        'end_time' => ['type' => 'TIME'],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('shifts');
}

public function down()
{
    $this->forge->dropTable('shifts');
}
}

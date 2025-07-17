<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkLocationsTable extends Migration
{
    // app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateWorkLocationsTable.php
public function up()
{
    $this->forge->addField([
        'id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
        'name' => ['type' => 'VARCHAR', 'constraint' => '255'], // e.g., "Kantor Pusat Jakarta", "Kantor Cabang Bandung"
        'address' => ['type' => 'TEXT', 'null' => true],
        'latitude' => ['type' => 'VARCHAR', 'constraint' => '100'],
        'longitude' => ['type' => 'VARCHAR', 'constraint' => '100'],
        'radius' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'default' => 100], // Jarak toleransi (dalam meter)
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('work_locations');
}

public function down()
{
    $this->forge->dropTable('work_locations');
}
}

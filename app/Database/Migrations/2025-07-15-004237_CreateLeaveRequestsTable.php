<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeaveRequestsTable extends Migration
{
    // app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateLeaveRequestsTable.php
public function up()
{
    $this->forge->addField([
        'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
        'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        'leave_type' => [ // Tipe bisa berupa Sakit, Izin, atau Cuti Tahunan
            'type' => 'ENUM',
            'constraint' => ['Sakit', 'Izin', 'Cuti'],
        ],
        'start_date' => ['type' => 'DATE'],
        'end_date' => ['type' => 'DATE'],
        'reason' => ['type' => 'TEXT'],
        'attachment' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true], // Untuk surat dokter, dll.
        'status' => [
            'type' => 'ENUM',
            'constraint' => ['Pending', 'Approved', 'Rejected'],
            'default' => 'Pending',
        ],
        'approved_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
        'created_at' => ['type' => 'DATETIME', 'null' => true],
        'updated_at' => ['type' => 'DATETIME', 'null' => true],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'SET NULL');
    $this->forge->createTable('leave_requests');
}

public function down()
{
    $this->forge->dropTable('leave_requests');
}
}

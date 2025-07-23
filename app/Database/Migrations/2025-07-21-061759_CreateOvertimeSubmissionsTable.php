<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOvertimeSubmissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true,
            ],
            'overtime_type' => [
                'type' => 'VARCHAR', 'constraint' => '100',
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'start_time' => [
                'type' => 'TIME',
            ],
            'end_time' => [
                'type' => 'TIME',
            ],
            'coworker_id' => [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true,
            ],
            'evidence_photo' => [
                'type' => 'VARCHAR', 'constraint' => '255',
            ],
            'location_address' => [
                'type' => 'TEXT',
            ],
            'latitude' => [
                'type' => 'VARCHAR', 'constraint' => '50',
            ],
            'longitude' => [
                'type' => 'VARCHAR', 'constraint' => '50',
            ],
            'status' => [
                'type' => 'VARCHAR', 'constraint' => '50', 'default' => 'pending',
            ],
            'created_at' => [
                'type' => 'DATETIME', 'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME', 'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('overtime_submissions');
    }

    public function down()
    {
        $this->forge->dropTable('overtime_submissions');
    }
}
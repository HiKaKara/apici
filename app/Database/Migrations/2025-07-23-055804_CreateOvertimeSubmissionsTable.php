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
                'type'       => 'INT', 'constraint' => 11, 'unsigned'   => true,
            ],
            'overtime_type' => [
                'type'       => 'VARCHAR', 'constraint' => '100', 'null' => false,
            ],
            'start_date' => [
                'type' => 'DATE', 'null' => false,
            ],
            'end_date' => [
                'type' => 'DATE', 'null' => false,
            ],
            'start_time' => [
                'type' => 'TIME', 'null' => false,
            ],
            'end_time' => [
                'type' => 'TIME', 'null' => false,
            ],
            'coworker_id' => [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true,
            ],
            'evidence_photo' => [
                'type'       => 'VARCHAR', 'constraint' => '255', 'null' => false,
            ],
            'location_address' => [
                'type' => 'TEXT', 'null' => false,
            ],
            'latitude' => [
                'type'       => 'VARCHAR', 'constraint' => '50', 'null' => false,
            ],
            'longitude' => [
                'type'       => 'VARCHAR', 'constraint' => '50', 'null' => false,
            ],
            'status' => [
                'type'       => 'VARCHAR', 'constraint' => '50', 'default' => 'pending',
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
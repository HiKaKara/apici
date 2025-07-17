<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimestampsToAttendances extends Migration
{
    public function up()
    {
        $this->forge->addColumn('attendances', [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('attendances', ['created_at', 'updated_at']);
    }
}
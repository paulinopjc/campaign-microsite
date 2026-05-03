<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubmissions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'campaign_id' => ['type' => 'INT'],
            'utm_source' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'utm_medium' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'utm_campaign' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'utm_content' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('campaign_id');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('campaign_id', 'campaigns', 'id', '', 'CASCADE');
        $this->forge->createTable('submissions');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'submission_id' => ['type' => 'INT'],
            'campaign_field_id' => ['type' => 'INT'],
            'value' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('submission_id');
        $this->forge->addForeignKey('submission_id', 'submissions', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('campaign_field_id', 'campaign_fields', 'id', '', 'CASCADE');
        $this->forge->createTable('submission_values');
    }

    public function down()
    {
        $this->forge->dropTable('submission_values');
        $this->forge->dropTable('submissions');
    }
}
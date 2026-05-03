<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCampaigns extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'user_id' => ['type' => 'INT'],
            'name' => ['type' => 'VARCHAR', 'constraint' => 500],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 500],
            'description' => ['type' => 'TEXT', 'null' => true],
            'template' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'default'],
            'branding' => ['type' => 'JSON'],
            'countdown_target' => ['type' => 'TIMESTAMP', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'],
            'starts_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'ends_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'thank_you_message' => ['type' => 'TEXT', 'null' => true, 'default' => 'Thank you for your submission!'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->createTable('campaigns');
        $this->db->query("ALTER TABLE campaigns ADD CONSTRAINT check_status CHECK (status IN ('draft', 'published', 'closed'))");
    }

    public function down()
    {
        $this->forge->dropTable('campaigns');
    }
}
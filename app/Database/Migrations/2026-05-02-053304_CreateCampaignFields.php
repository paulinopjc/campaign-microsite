<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCampaignFields extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'campaign_id' => ['type' => 'INT'],
            'label' => ['type' => 'VARCHAR', 'constraint' => 255],
            'field_key' => ['type' => 'VARCHAR', 'constraint' => 100],
            'field_type' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'text'],
            'options' => ['type' => 'JSON', 'null' => true],
            'is_required' => ['type' => 'BOOLEAN', 'default' => false],
            'placeholder' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('campaign_id');
        $this->forge->addForeignKey('campaign_id', 'campaigns', 'id', '', 'CASCADE');
        $this->forge->createTable('campaign_fields');
        $this->db->query("ALTER TABLE campaign_fields ADD CONSTRAINT check_field_type CHECK (field_type IN ('text', 'email', 'phone', 'textarea', 'dropdown', 'checkbox', 'radio', 'date'))");
    }

    public function down()
    {
        $this->forge->dropTable('campaign_fields');
    }
}
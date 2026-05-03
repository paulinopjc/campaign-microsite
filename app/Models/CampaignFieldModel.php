<?php

namespace App\Models;

use CodeIgniter\Model;

class CampaignFieldModel extends Model
{
    protected $table = 'campaign_fields';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'campaign_id', 'label', 'field_key', 'field_type',
        'options', 'is_required', 'placeholder', 'sort_order',
    ];
    protected $useTimestamps = false;

    public function getForCampaign(int $campaignId): array
    {
        $fields = $this->where('campaign_id', $campaignId)
            ->orderBy('sort_order')
            ->findAll();

        // Parse JSON options for dropdown/radio/checkbox fields
        foreach ($fields as &$field) {
            $field['options_parsed'] = $field['options']
                ? json_decode($field['options'], true)
                : [];
        }

        return $fields;
    }
}
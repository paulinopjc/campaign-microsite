<?php

namespace App\Models;

use CodeIgniter\Model;

class CampaignModel extends Model
{
    protected $table = 'campaigns';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'name', 'slug', 'description', 'template',
        'branding', 'countdown_target', 'status', 'starts_at',
        'ends_at', 'thank_you_message',
    ];
    protected $useTimestamps = true;

    public function getBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    public function getPublished(string $slug)
    {
        $campaign = $this->where('slug', $slug)->where('status', 'published')->first();
        if (!$campaign) return null;

        // Check date range
        $now = date('Y-m-d H:i:s');
        if ($campaign['starts_at'] && $campaign['starts_at'] > $now) return null;
        if ($campaign['ends_at'] && $campaign['ends_at'] < $now) return null;

        return $campaign;
    }
}
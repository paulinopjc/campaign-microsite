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
        'ends_at', 'thank_you_message', 'timezone',
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

        // Compare dates in the campaign's timezone
        $tz = new \DateTimeZone($campaign['timezone'] ?? 'UTC');
        $now = (new \DateTime('now', $tz))->format('Y-m-d H:i:s');

        if ($campaign['starts_at'] && $campaign['starts_at'] > $now) return null;
        if ($campaign['ends_at'] && $campaign['ends_at'] < $now) return null;

        return $campaign;
    }

    public function hasEnded(string $slug): bool
    {
        $campaign = $this->where('slug', $slug)->where('status', 'published')->first();
        if (!$campaign || !$campaign['ends_at']) return false;

        $tz = new \DateTimeZone($campaign['timezone'] ?? 'UTC');
        $now = (new \DateTime('now', $tz))->format('Y-m-d H:i:s');

        return $campaign['ends_at'] < $now;
    }

    public function getActiveCampaigns(): array
    {
        $campaigns = $this->where('status', 'published')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return array_filter($campaigns, function ($campaign) {
            if (!$campaign['ends_at']) return true;

            $tz = new \DateTimeZone($campaign['timezone'] ?? 'UTC');
            $now = (new \DateTime('now', $tz))->format('Y-m-d H:i:s');

            return $campaign['ends_at'] >= $now;
        });
    }
}
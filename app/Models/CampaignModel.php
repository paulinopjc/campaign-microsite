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
        if (!$campaign) {
            error_log("CAMPAIGN DEBUG: No published campaign found for slug '{$slug}'");
            return null;
        }

        // Compare dates in the campaign's timezone
        $tz = new \DateTimeZone($campaign['timezone'] ?? 'UTC');
        $now = (new \DateTime('now', $tz))->format('Y-m-d H:i:s');

        error_log("CAMPAIGN DEBUG: slug={$slug} tz={$campaign['timezone']} now={$now} starts_at={$campaign['starts_at']} ends_at={$campaign['ends_at']}");

        if ($campaign['starts_at'] && $campaign['starts_at'] > $now) {
            error_log("CAMPAIGN DEBUG: Not started yet");
            return null;
        }
        if ($campaign['ends_at'] && $campaign['ends_at'] < $now) {
            error_log("CAMPAIGN DEBUG: Already ended");
            return null;
        }

        return $campaign;
    }
}
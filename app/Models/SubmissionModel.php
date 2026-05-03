<?php

namespace App\Models;

use CodeIgniter\Model;

class SubmissionModel extends Model
{
    protected $table = 'submissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'campaign_id', 'utm_source', 'utm_medium',
        'utm_campaign', 'utm_content', 'ip_address', 'user_agent',
    ];
    protected $useTimestamps = true;
    protected $updatedField = '';
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class PageViewModel extends Model
{
    protected $table = 'page_views';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'campaign_id', 'utm_source', 'utm_medium',
        'ip_address', 'viewed_at',
    ];
    protected $useTimestamps = false;
}

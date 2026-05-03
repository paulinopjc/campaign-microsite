<?php

namespace App\Models;

use CodeIgniter\Model;

class SubmissionValueModel extends Model
{
    protected $table = 'submission_values';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'submission_id', 'campaign_field_id', 'value',
    ];
    protected $useTimestamps = false;
}
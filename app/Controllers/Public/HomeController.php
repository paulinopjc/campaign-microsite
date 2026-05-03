<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CampaignModel;

class HomeController extends BaseController
{
    public function index()
    {
        $model = new CampaignModel();
        $campaigns = $model->where('status', 'published')->orderBy('created_at', 'DESC')->findAll();

        return view('public/home', ['campaigns' => $campaigns]);
    }
}

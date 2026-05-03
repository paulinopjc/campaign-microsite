<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CampaignModel;

class HomeController extends BaseController
{
    public function index()
    {
        $model = new CampaignModel();
        $campaigns = $model->getActiveCampaigns();

        return view('public/home', ['campaigns' => $campaigns]);
    }
}

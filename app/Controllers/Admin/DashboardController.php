<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CampaignModel;
use App\Models\SubmissionModel;
use App\Models\PageViewModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $campaignModel = new CampaignModel();
        $submissionModel = new SubmissionModel();
        $pageViewModel = new PageViewModel();

        $totalCampaigns = $campaignModel->where('user_id', session()->get('user_id'))->countAllResults();
        $publishedCampaigns = $campaignModel->where('user_id', session()->get('user_id'))
            ->where('status', 'published')->countAllResults();

        $campaignIds = array_column(
            $campaignModel->select('id')->where('user_id', session()->get('user_id'))->findAll(),
            'id'
        );

        $totalSubmissions = 0;
        $totalPageViews = 0;
        if (!empty($campaignIds)) {
            $totalSubmissions = $submissionModel->whereIn('campaign_id', $campaignIds)->countAllResults();
            $totalPageViews = $pageViewModel->whereIn('campaign_id', $campaignIds)->countAllResults();
        }

        $conversionRate = $totalPageViews > 0
            ? round(($totalSubmissions / $totalPageViews) * 100, 1)
            : 0;

        $recentCampaigns = $campaignModel->where('user_id', session()->get('user_id'))
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        return view('admin/dashboard', [
            'totalCampaigns' => $totalCampaigns,
            'publishedCampaigns' => $publishedCampaigns,
            'totalSubmissions' => $totalSubmissions,
            'totalPageViews' => $totalPageViews,
            'conversionRate' => $conversionRate,
            'recentCampaigns' => $recentCampaigns,
        ]);
    }
}
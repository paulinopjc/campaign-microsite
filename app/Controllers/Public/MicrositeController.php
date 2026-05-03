<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CampaignModel;
use App\Models\CampaignFieldModel;
use App\Models\SubmissionModel;
use App\Models\SubmissionValueModel;
use App\Models\PageViewModel;

use \CodeIgniter\Exceptions\PageNotFoundException;

class MicrositeController extends BaseController
{
    public function show(string $slug)
    {
        $model = new CampaignModel();
        $campaign = $model->getPublished($slug);

        if (!$campaign) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Record page view
        $pvModel = new PageViewModel();
        $pvModel->save([
            'campaign_id' => $campaign['id'],
            'utm_source' => $this->request->getGet('utm_source'),
            'utm_medium' => $this->request->getGet('utm_medium'),
            'ip_address' => $this->request->getIPAddress(),
            'viewed_at' => date('Y-m-d H:i:s'),
        ]);

        $fieldModel = new CampaignFieldModel();
        $fields = $fieldModel->getForCampaign($campaign['id']);

        $branding = json_decode($campaign['branding'], true);

        $twig = new \App\Libraries\TwigRenderer();
        return $twig->render($campaign['template'], [
            'campaign' => $campaign,
            'branding' => $branding,
            'fields' => $fields,
            'countdown_target' => $campaign['countdown_target'],
            'share_url' => current_url(),
            'csrf_name' => csrf_token(),
            'csrf_value' => csrf_hash(),
        ]);
    }

    public function submit(string $slug)
    {
        $model = new CampaignModel();
        $campaign = $model->getPublished($slug);

        if (!$campaign) {
            throw PageNotFoundException::forPageNotFound();
        }

        $fieldModel = new CampaignFieldModel();
        $fields = $fieldModel->getForCampaign($campaign['id']);

        // Validate required fields
        $rules = [];
        foreach ($fields as $field) {
            $fieldRules = [];
            if ($field['is_required']) $fieldRules[] = 'required';
            if ($field['field_type'] === 'email') $fieldRules[] = 'valid_email';
            if (!empty($fieldRules)) {
                $rules['field_' . $field['id']] = implode('|', $fieldRules);
            }
        }

        if (!empty($rules) && !$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Save submission
        $subModel = new SubmissionModel();
        $subModel->save([
            'campaign_id' => $campaign['id'],
            'utm_source' => $this->request->getPost('utm_source'),
            'utm_medium' => $this->request->getPost('utm_medium'),
            'utm_campaign' => $this->request->getPost('utm_campaign'),
            'utm_content' => $this->request->getPost('utm_content'),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $submissionId = $subModel->getInsertID();

        // Save field values
        $valueModel = new SubmissionValueModel();
        foreach ($fields as $field) {
            $value = $this->request->getPost('field_' . $field['id']);
            if ($value !== null) {
                $valueModel->save([
                    'submission_id' => $submissionId,
                    'campaign_field_id' => $field['id'],
                    'value' => is_array($value) ? json_encode($value) : $value,
                ]);
            }
        }

        return redirect()->to("/c/{$slug}/thanks");
    }

    public function thanks(string $slug)
    {
        $model = new CampaignModel();
        $campaign = $model->getBySlug($slug);

        if (!$campaign) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('public/thanks', ['campaign' => $campaign]);
    }
}
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CampaignModel;
use App\Models\CampaignFieldModel;
use App\Models\SubmissionModel;
use App\Models\SubmissionValueModel;

class CampaignController extends BaseController
{
    public function index()
    {
        $model = new CampaignModel();
        $campaigns = $model->where('user_id', session()->get('user_id'))
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('admin/campaigns/index', ['campaigns' => $campaigns]);
    }

    public function create()
    {
        return view('admin/campaigns/create');
    }

    public function store()
    {
        $model = new CampaignModel();

        $branding = [
            'primary_color' => $this->request->getPost('primary_color') ?: '#0d6efd',
            'background_color' => $this->request->getPost('background_color') ?: '#ffffff',
            'text_color' => $this->request->getPost('text_color') ?: '#333333',
            'font_family' => $this->request->getPost('font_family') ?: "'Inter', sans-serif",
            'logo_url' => '',
            'hero_image_url' => '',
        ];

        // Handle logo upload
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/logos', $newName);
            $branding['logo_url'] = base_url('uploads/logos/' . $newName);
        }

        // Handle hero image upload
        $hero = $this->request->getFile('hero_image');
        if ($hero && $hero->isValid() && !$hero->hasMoved()) {
            $newName = $hero->getRandomName();
            $hero->move(FCPATH . 'uploads/heroes', $newName);
            $branding['hero_image_url'] = base_url('uploads/heroes/' . $newName);
        }

        $data = [
            'user_id' => session()->get('user_id'),
            'name' => $this->request->getPost('name'),
            'slug' => url_title($this->request->getPost('name'), '-', true),
            'description' => $this->request->getPost('description'),
            'template' => $this->request->getPost('template') ?: 'default',
            'branding' => json_encode($branding),
            'countdown_target' => $this->request->getPost('countdown_target') ?: null,
            'thank_you_message' => $this->request->getPost('thank_you_message') ?: 'Thank you for your submission!',
            'starts_at' => $this->request->getPost('starts_at') ?: null,
            'ends_at' => $this->request->getPost('ends_at') ?: null,
            'timezone' => $this->request->getPost('timezone') ?: 'UTC',
            'status' => 'draft',
        ];

        $model->save($data);
        $campaignId = $model->getInsertID();

        // Save form fields
        $fieldModel = new CampaignFieldModel();
        $labels = $this->request->getPost('field_label') ?? [];
        foreach ($labels as $i => $label) {
            if (empty($label)) continue;
            $fieldModel->save([
                'campaign_id' => $campaignId,
                'label' => $label,
                'field_key' => url_title($label, '_', true),
                'field_type' => ($this->request->getPost('field_type')[$i]) ?? 'text',
                'is_required' => ($this->request->getPost('field_required')[$i]) ?? 0,
                'placeholder' => ($this->request->getPost('field_placeholder')[$i]) ?? '',
                'options' => !empty($this->request->getPost('field_options')[$i])
                    ? json_encode(array_map('trim', explode(',', $this->request->getPost('field_options')[$i])))
                    : null,
                'sort_order' => $i,
            ]);
        }

        return redirect()->to("/admin/campaigns/{$campaignId}/edit")
            ->with('success', 'Campaign created');
    }

    public function edit(int $id)
    {
        $model = new CampaignModel();
        $campaign = $model->find($id);
        if (!$campaign) return redirect()->to('/admin/campaigns');

        $fieldModel = new CampaignFieldModel();
        $fields = $fieldModel->getForCampaign($id);

        $campaign['branding_parsed'] = json_decode($campaign['branding'], true);

        return view('admin/campaigns/edit', [
            'campaign' => $campaign,
            'fields' => $fields,
        ]);
    }

    public function update(int $id)
    {
        $model = new CampaignModel();
        $campaign = $model->find($id);
        if (!$campaign) return redirect()->to('/admin/campaigns');

        $branding = json_decode($campaign['branding'], true) ?: [];
        $branding['primary_color'] = $this->request->getPost('primary_color') ?: $branding['primary_color'] ?? '#0d6efd';
        $branding['background_color'] = $this->request->getPost('background_color') ?: $branding['background_color'] ?? '#ffffff';
        $branding['text_color'] = $this->request->getPost('text_color') ?: $branding['text_color'] ?? '#333333';
        $branding['font_family'] = $this->request->getPost('font_family') ?: $branding['font_family'] ?? "'Inter', sans-serif";

        // Handle logo upload
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/logos', $newName);
            $branding['logo_url'] = base_url('uploads/logos/' . $newName);
        }

        // Handle hero image upload
        $hero = $this->request->getFile('hero_image');
        if ($hero && $hero->isValid() && !$hero->hasMoved()) {
            $newName = $hero->getRandomName();
            $hero->move(FCPATH . 'uploads/heroes', $newName);
            $branding['hero_image_url'] = base_url('uploads/heroes/' . $newName);
        }

        $model->update($id, [
            'name' => $this->request->getPost('name'),
            'slug' => url_title($this->request->getPost('name'), '-', true),
            'description' => $this->request->getPost('description'),
            'template' => $this->request->getPost('template') ?: 'default',
            'branding' => json_encode($branding),
            'countdown_target' => $this->request->getPost('countdown_target') ?: null,
            'thank_you_message' => $this->request->getPost('thank_you_message') ?: 'Thank you for your submission!',
            'starts_at' => $this->request->getPost('starts_at') ?: null,
            'ends_at' => $this->request->getPost('ends_at') ?: null,
            'timezone' => $this->request->getPost('timezone') ?: 'UTC',
        ]);

        // Update form fields: delete existing and re-insert
        $fieldModel = new CampaignFieldModel();
        $fieldModel->where('campaign_id', $id)->delete();

        $labels = $this->request->getPost('field_label') ?? [];
        foreach ($labels as $i => $label) {
            if (empty($label)) continue;
            $fieldModel->save([
                'campaign_id' => $id,
                'label' => $label,
                'field_key' => url_title($label, '_', true),
                'field_type' => ($this->request->getPost('field_type')[$i]) ?? 'text',
                'is_required' => ($this->request->getPost('field_required')[$i]) ?? 0,
                'placeholder' => ($this->request->getPost('field_placeholder')[$i]) ?? '',
                'options' => !empty($this->request->getPost('field_options')[$i])
                    ? json_encode(array_map('trim', explode(',', $this->request->getPost('field_options')[$i])))
                    : null,
                'sort_order' => $i,
            ]);
        }

        return redirect()->to("/admin/campaigns/{$id}/edit")
            ->with('success', 'Campaign updated');
    }

    public function preview(int $id)
    {
        $model = new CampaignModel();
        $campaign = $model->find($id);
        if (!$campaign) return redirect()->to('/admin/campaigns');

        return $this->renderMicrosite($campaign);
    }

    public function publish(int $id)
    {
        $model = new CampaignModel();
        $model->update($id, ['status' => 'published']);
        return redirect()->back()->with('success', 'Campaign published');
    }

    public function close(int $id)
    {
        $model = new CampaignModel();
        $model->update($id, ['status' => 'closed']);
        return redirect()->back()->with('success', 'Campaign closed');
    }

    public function submissions(int $id)
    {
        $model = new CampaignModel();
        $campaign = $model->find($id);

        $subModel = new SubmissionModel();
        $submissions = $subModel->where('campaign_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $fieldModel = new CampaignFieldModel();
        $fields = $fieldModel->getForCampaign($id);

        // Load values for each submission
        $valueModel = new SubmissionValueModel();
        foreach ($submissions as &$sub) {
            $values = $valueModel->where('submission_id', $sub['id'])->findAll();
            $sub['values'] = [];
            foreach ($values as $v) {
                $sub['values'][$v['campaign_field_id']] = $v['value'];
            }
        }

        return view('admin/campaigns/submissions', [
            'campaign' => $campaign,
            'submissions' => $submissions,
            'fields' => $fields,
        ]);
    }

    public function export(int $id)
    {
        $model = new CampaignModel();
        $campaign = $model->find($id);

        $fieldModel = new CampaignFieldModel();
        $fields = $fieldModel->getForCampaign($id);

        $subModel = new SubmissionModel();
        $submissions = $subModel->where('campaign_id', $id)->orderBy('created_at', 'ASC')->findAll();

        $valueModel = new SubmissionValueModel();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $campaign['slug'] . '-submissions.csv"');

        $output = fopen('php://output', 'w');

        // Header row
        $headers = ['Submitted At', 'UTM Source', 'UTM Medium', 'UTM Campaign'];
        foreach ($fields as $f) {
            $headers[] = $f['label'];
        }
        fputcsv($output, $headers);

        // Data rows
        foreach ($submissions as $sub) {
            $values = $valueModel->where('submission_id', $sub['id'])->findAll();
            $valueMap = [];
            foreach ($values as $v) {
                $valueMap[$v['campaign_field_id']] = $v['value'];
            }

            $row = [$sub['created_at'], $sub['utm_source'], $sub['utm_medium'], $sub['utm_campaign']];
            foreach ($fields as $f) {
                $row[] = $valueMap[$f['id']] ?? '';
            }
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    private function renderMicrosite(array $campaign): string
    {
        $fieldModel = new CampaignFieldModel();
        $fields = $fieldModel->getForCampaign($campaign['id']);

        $branding = json_decode($campaign['branding'], true);

        $twig = new \App\Libraries\TwigRenderer();
        return $twig->render($campaign['template'], [
            'campaign' => $campaign,
            'branding' => $branding,
            'fields' => $fields,
            'countdown_target' => $campaign['countdown_target'],
            'share_url' => base_url('c/' . $campaign['slug']),
            'csrf_name' => csrf_token(),
            'csrf_value' => csrf_hash(),
        ]);
    }
}
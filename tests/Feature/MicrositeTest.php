<?php

namespace Tests\Feature;

use App\Models\CampaignModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class MicrositeTest extends CIUnitTestCase
{
    use DatabaseTestTrait, FeatureTestTrait;

    protected $DBGroup = 'tests';
    protected $migrate = false;
    protected $refresh = false;

    protected function setUp(): void
    {
        parent::setUp();

        $db = \Config\Database::connect('tests');
        $db->query('TRUNCATE page_views, submission_values, submissions, campaign_fields, campaigns, users CASCADE');
    }

    public function testPublishedCampaignReturns200(): void
    {
        $model = new CampaignModel();
        $model->insert([
            'name'   => 'Open House',
            'slug'   => 'open-house',
            'status' => 'published',
        ]);

        $result = $this->get('/c/open-house');
        $result->assertStatus(200);
    }

    public function testDraftCampaignReturns404(): void
    {
        $model = new CampaignModel();
        $model->insert([
            'name'   => 'Hidden Deal',
            'slug'   => 'hidden-deal',
            'status' => 'draft',
        ]);

        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('/c/hidden-deal');
    }

    public function testFormSubmissionCreatesLead(): void
    {
        $model = new CampaignModel();
        $id = (int) $model->insert([
            'name'   => 'Lead Gen',
            'slug'   => 'lead-gen',
            'status' => 'published',
        ]);

        $result = $this->post('/c/lead-gen/submit', [
            'name'  => 'Charlie',
            'email' => 'charlie@example.com',
            'phone' => '09171234567',
        ]);

        $result->assertStatus(302);

        $submissionModel = new \App\Models\SubmissionModel();
        $count = $submissionModel->where('campaign_id', $id)->countAllResults();
        $this->assertEquals(1, $count);
    }

    public function testNonexistentSlugReturns404(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('/c/does-not-exist');
    }
}

<?php

namespace Tests\Models;

use App\Models\CampaignModel;
use App\Models\SubmissionModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class SubmissionModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $DBGroup = 'tests';
    protected $migrate = false;
    protected $refresh = false;

    private SubmissionModel $submissionModel;
    private int $campaignId;

    protected function setUp(): void
    {
        parent::setUp();

        $db = \Config\Database::connect('tests');
        $db->query('TRUNCATE page_views, submission_values, submissions, campaign_fields, campaigns, users CASCADE');

        $this->submissionModel = new SubmissionModel();

        $campaignModel = new CampaignModel();
        $this->campaignId = (int) $campaignModel->insert([
            'name'   => 'Test Campaign',
            'slug'   => 'test-campaign',
            'status' => 'published',
        ]);
    }

    public function testCreateSubmission(): void
    {
        $id = $this->submissionModel->insert([
            'campaign_id' => $this->campaignId,
            'utm_source'  => 'google',
            'utm_medium'  => 'cpc',
        ]);

        $this->assertIsInt((int) $id);

        $submission = $this->submissionModel->find($id);
        $this->assertEquals($this->campaignId, $submission['campaign_id']);
        $this->assertEquals('google', $submission['utm_source']);
    }

    public function testCountByCampaign(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->submissionModel->insert([
                'campaign_id' => $this->campaignId,
            ]);
        }

        $count = $this->submissionModel
            ->where('campaign_id', $this->campaignId)
            ->countAllResults();

        $this->assertEquals(3, $count);
    }

    public function testSubmissionBelongsToCampaign(): void
    {
        $id = $this->submissionModel->insert([
            'campaign_id' => $this->campaignId,
            'utm_source'  => 'facebook',
        ]);

        $submission = $this->submissionModel->find($id);
        $this->assertEquals($this->campaignId, $submission['campaign_id']);
    }
}

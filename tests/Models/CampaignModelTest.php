<?php

namespace Tests\Models;

use App\Models\CampaignModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class CampaignModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $DBGroup = 'tests';
    protected $migrate = false;
    protected $refresh = false;

    private CampaignModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new CampaignModel();

        $db = \Config\Database::connect('tests');
        $db->query('TRUNCATE page_views, submission_values, submissions, campaign_fields, campaigns, users CASCADE');
    }

    public function testCreateCampaign(): void
    {
        $id = $this->model->insert([
            'name'   => 'Summer Sale',
            'slug'   => 'summer-sale',
            'status' => 'draft',
        ]);

        $this->assertIsInt((int) $id);

        $campaign = $this->model->find($id);
        $this->assertEquals('Summer Sale', $campaign['name']);
        $this->assertEquals('draft', $campaign['status']);
    }

    public function testGetBySlug(): void
    {
        $this->model->insert([
            'name'   => 'Flash Promo',
            'slug'   => 'flash-promo',
            'status' => 'published',
        ]);

        $result = $this->model->where('slug', 'flash-promo')->first();

        $this->assertNotNull($result);
        $this->assertEquals('Flash Promo', $result['name']);
    }

    public function testGetBySlugReturnsNullForMissing(): void
    {
        $result = $this->model->where('slug', 'nonexistent')->first();
        $this->assertNull($result);
    }

    public function testGetPublishedCampaigns(): void
    {
        $this->model->insert([
            'name'   => 'Draft One',
            'slug'   => 'draft-one',
            'status' => 'draft',
        ]);
        $this->model->insert([
            'name'   => 'Live One',
            'slug'   => 'live-one',
            'status' => 'published',
        ]);
        $this->model->insert([
            'name'   => 'Live Two',
            'slug'   => 'live-two',
            'status' => 'published',
        ]);

        $published = $this->model->where('status', 'published')->findAll();

        $this->assertCount(2, $published);
    }

    public function testUpdateCampaignStatus(): void
    {
        $id = $this->model->insert([
            'name'   => 'To Publish',
            'slug'   => 'to-publish',
            'status' => 'draft',
        ]);

        $this->model->update($id, ['status' => 'published']);

        $campaign = $this->model->find($id);
        $this->assertEquals('published', $campaign['status']);
    }
}

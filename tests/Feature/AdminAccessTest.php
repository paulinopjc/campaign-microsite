<?php

namespace Tests\Feature;

use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class AdminAccessTest extends CIUnitTestCase
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

    public function testUnauthenticatedRedirectsToLogin(): void
    {
        $result = $this->get('/admin/campaigns');
        $result->assertRedirectTo('/login');
    }

    public function testAuthenticatedCanAccessDashboard(): void
    {
        $userModel = new UserModel();
        $userId = (int) $userModel->insert([
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'google_sub' => null,
            'role'     => 'admin',
            'is_active' => true,
        ]);

        $result = $this->withSession(['user_id' => $userId, 'user_name' => 'Admin User', 'user_role' => 'admin', 'logged_in' => true])
                       ->get('/admin/campaigns');

        $result->assertStatus(200);
    }

    public function testAuthenticatedCanViewSubmissions(): void
    {
        $userModel = new UserModel();
        $userId = (int) $userModel->insert([
            'name'     => 'Manager',
            'email'    => 'manager@example.com',
            'google_sub' => null,
            'role'     => 'admin',
            'is_active' => true,
        ]);

        $campaignModel = new \App\Models\CampaignModel();
        $campaignId = (int) $campaignModel->insert([
            'name'   => 'View Test',
            'slug'   => 'view-test',
            'status' => 'published',
        ]);

        $result = $this->withSession(['user_id' => $userId, 'user_name' => 'Manager', 'user_role' => 'admin', 'logged_in' => true])
                       ->get("/admin/campaigns/{$campaignId}/submissions");

        $result->assertStatus(200);
    }
}

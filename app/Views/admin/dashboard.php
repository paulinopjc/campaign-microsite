<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3><?= $totalCampaigns ?></h3>
                <p class="text-muted mb-0">Total Campaigns</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3><?= $publishedCampaigns ?></h3>
                <p class="text-muted mb-0">Published</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3><?= $totalSubmissions ?></h3>
                <p class="text-muted mb-0">Total Submissions</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3><?= $conversionRate ?>%</h3>
                <p class="text-muted mb-0">Conversion Rate</p>
            </div>
        </div>
    </div>
</div>

<h5>Recent Campaigns</h5>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentCampaigns as $campaign): ?>
        <tr>
            <td><?= esc($campaign['name']) ?></td>
            <td>
                <span class="badge bg-<?= $campaign['status'] === 'published' ? 'success' : ($campaign['status'] === 'draft' ? 'secondary' : 'danger') ?>">
                    <?= ucfirst($campaign['status']) ?>
                </span>
            </td>
            <td><?= date('M d, Y', strtotime($campaign['created_at'])) ?></td>
            <td>
                <a href="/admin/campaigns/<?= $campaign['id'] ?>/edit" class="btn btn-sm btn-outline-primary">Edit</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recentCampaigns)): ?>
        <tr><td colspan="4" class="text-muted text-center">No campaigns yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
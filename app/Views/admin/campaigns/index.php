<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Campaigns</h2>
    <a href="/admin/campaigns/create" class="btn btn-primary">New Campaign</a>
</div>

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
        <?php foreach ($campaigns as $campaign): ?>
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
                <a href="/admin/campaigns/<?= $campaign['id'] ?>/preview" class="btn btn-sm btn-outline-secondary" target="_blank">Preview</a>
                <a href="/admin/campaigns/<?= $campaign['id'] ?>/submissions" class="btn btn-sm btn-outline-info">Submissions</a>
                <?php if ($campaign['status'] === 'draft'): ?>
                    <form action="/admin/campaigns/<?= $campaign['id'] ?>/publish" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-success">Publish</button>
                    </form>
                <?php elseif ($campaign['status'] === 'published'): ?>
                    <a href="/c/<?= $campaign['slug'] ?>" class="btn btn-sm btn-outline-dark" target="_blank">Live</a>
                    <form action="/admin/campaigns/<?= $campaign['id'] ?>/close" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-warning">Close</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($campaigns)): ?>
        <tr><td colspan="4" class="text-muted text-center">No campaigns yet. Create your first one.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
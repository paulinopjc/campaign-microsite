<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Submissions: <?= esc($campaign['name']) ?></h2>
    <div>
        <a href="/admin/campaigns/<?= $campaign['id'] ?>/export" class="btn btn-outline-secondary btn-sm">Export CSV</a>
        <a href="/admin/campaigns/<?= $campaign['id'] ?>/edit" class="btn btn-outline-primary btn-sm">Back to Campaign</a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Submitted</th>
                <?php foreach ($fields as $field): ?>
                    <th><?= esc($field['label']) ?></th>
                <?php endforeach; ?>
                <th>UTM Source</th>
                <th>UTM Medium</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $sub): ?>
            <tr>
                <td><?= date('M d H:i', strtotime($sub['created_at'])) ?></td>
                <?php foreach ($fields as $field): ?>
                    <td><?= esc($sub['values'][$field['id']] ?? '') ?></td>
                <?php endforeach; ?>
                <td><?= esc($sub['utm_source'] ?? '') ?></td>
                <td><?= esc($sub['utm_medium'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($submissions)): ?>
            <tr><td colspan="<?= count($fields) + 3 ?>" class="text-muted text-center">No submissions yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
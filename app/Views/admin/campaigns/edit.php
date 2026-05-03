<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit: <?= esc($campaign['name']) ?></h2>
    <div>
        <a href="/admin/campaigns/<?= $campaign['id'] ?>/preview" class="btn btn-outline-secondary btn-sm" target="_blank">Preview</a>
        <?php if ($campaign['status'] === 'draft'): ?>
            <form action="/admin/campaigns/<?= $campaign['id'] ?>/publish" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button class="btn btn-success btn-sm">Publish</button>
            </form>
        <?php elseif ($campaign['status'] === 'published'): ?>
            <a href="/c/<?= $campaign['slug'] ?>" class="btn btn-outline-dark btn-sm" target="_blank">View Live</a>
        <?php endif; ?>
    </div>
</div>

<form action="/admin/campaigns/<?= $campaign['id'] ?>/edit" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="card mb-4">
        <div class="card-header">Campaign Details</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Campaign Name *</label>
                <input type="text" name="name" class="form-control" value="<?= esc($campaign['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= esc($campaign['description']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Template</label>
                    <select name="template" class="form-select">
                        <option value="default" <?= $campaign['template'] === 'default' ? 'selected' : '' ?>>Default</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Countdown Target</label>
                    <input type="datetime-local" name="countdown_target" class="form-control"
                           value="<?= $campaign['countdown_target'] ? date('Y-m-d\TH:i', strtotime($campaign['countdown_target'])) : '' ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Starts At</label>
                    <input type="datetime-local" name="starts_at" class="form-control"
                           value="<?= $campaign['starts_at'] ? date('Y-m-d\TH:i', strtotime($campaign['starts_at'])) : '' ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ends At</label>
                    <input type="datetime-local" name="ends_at" class="form-control"
                           value="<?= $campaign['ends_at'] ? date('Y-m-d\TH:i', strtotime($campaign['ends_at'])) : '' ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Thank You Message</label>
                <input type="text" name="thank_you_message" class="form-control" value="<?= esc($campaign['thank_you_message']) ?>">
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Branding</div>
        <div class="card-body">
            <?php $b = $campaign['branding_parsed']; ?>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Primary Color</label>
                    <input type="color" name="primary_color" class="form-control form-control-color" value="<?= $b['primary_color'] ?? '#0d6efd' ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Background Color</label>
                    <input type="color" name="background_color" class="form-control form-control-color" value="<?= $b['background_color'] ?? '#ffffff' ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Text Color</label>
                    <input type="color" name="text_color" class="form-control form-control-color" value="<?= $b['text_color'] ?? '#333333' ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Font Family</label>
                    <select name="font_family" class="form-select">
                        <option value="'Inter', sans-serif" <?= ($b['font_family'] ?? '') === "'Inter', sans-serif" ? 'selected' : '' ?>>Inter</option>
                        <option value="'Roboto', sans-serif" <?= ($b['font_family'] ?? '') === "'Roboto', sans-serif" ? 'selected' : '' ?>>Roboto</option>
                        <option value="'Open Sans', sans-serif" <?= ($b['font_family'] ?? '') === "'Open Sans', sans-serif" ? 'selected' : '' ?>>Open Sans</option>
                        <option value="'Montserrat', sans-serif" <?= ($b['font_family'] ?? '') === "'Montserrat', sans-serif" ? 'selected' : '' ?>>Montserrat</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Logo</label>
                    <?php if (!empty($b['logo_url'])): ?>
                        <div class="mb-2"><img src="<?= $b['logo_url'] ?>" style="max-height: 60px;" alt="Current logo"></div>
                    <?php endif; ?>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    <div class="form-text">Leave empty to keep current logo.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Hero Image</label>
                    <?php if (!empty($b['hero_image_url'])): ?>
                        <div class="mb-2"><img src="<?= $b['hero_image_url'] ?>" style="max-height: 60px;" alt="Current hero"></div>
                    <?php endif; ?>
                    <input type="file" name="hero_image" class="form-control" accept="image/*">
                    <div class="form-text">Leave empty to keep current hero image.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            Form Fields
            <button type="button" class="btn btn-sm btn-outline-primary" id="add-field">+ Add Field</button>
        </div>
        <div class="card-body" id="fields-container">
            <?php foreach ($fields as $field): ?>
            <div class="field-row border rounded p-3 mb-2">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="field_label[]" class="form-control" value="<?= esc($field['label']) ?>" placeholder="Field label">
                    </div>
                    <div class="col-md-2">
                        <select name="field_type[]" class="form-select">
                            <?php foreach (['text','email','phone','textarea','dropdown','checkbox','radio','date'] as $type): ?>
                                <option value="<?= $type ?>" <?= $field['field_type'] === $type ? 'selected' : '' ?>><?= ucfirst($type) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="field_placeholder[]" class="form-control" value="<?= esc($field['placeholder'] ?? '') ?>" placeholder="Placeholder text">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="field_options[]" class="form-control" value="<?= esc(is_array($field['options_parsed']) ? implode(', ', $field['options_parsed']) : '') ?>" placeholder="Options (comma sep)">
                    </div>
                    <div class="col-md-1">
                        <div class="form-check mt-2">
                            <input type="hidden" name="field_required[]" value="<?= $field['is_required'] ? '1' : '0' ?>">
                            <input type="checkbox" class="form-check-input" <?= $field['is_required'] ? 'checked' : '' ?> onchange="this.previousElementSibling.value = this.checked ? 1 : 0">
                            <label class="form-check-label">Req</label>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-field">X</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="/admin/campaigns" class="btn btn-secondary">Cancel</a>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#add-field').click(function() {
    var html = `
        <div class="field-row border rounded p-3 mb-2">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="field_label[]" class="form-control" placeholder="Field label">
                </div>
                <div class="col-md-2">
                    <select name="field_type[]" class="form-select">
                        <option value="text">Text</option>
                        <option value="email">Email</option>
                        <option value="phone">Phone</option>
                        <option value="textarea">Textarea</option>
                        <option value="dropdown">Dropdown</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="radio">Radio</option>
                        <option value="date">Date</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="field_placeholder[]" class="form-control" placeholder="Placeholder text">
                </div>
                <div class="col-md-2">
                    <input type="text" name="field_options[]" class="form-control" placeholder="Options (comma sep)">
                </div>
                <div class="col-md-1">
                    <div class="form-check mt-2">
                        <input type="hidden" name="field_required[]" value="0">
                        <input type="checkbox" class="form-check-input" onchange="this.previousElementSibling.value = this.checked ? 1 : 0">
                        <label class="form-check-label">Req</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-field">X</button>
                </div>
            </div>
        </div>`;
    $('#fields-container').append(html);
});

$(document).on('click', '.remove-field', function() {
    $(this).closest('.field-row').remove();
});
</script>
<?= $this->endSection() ?>
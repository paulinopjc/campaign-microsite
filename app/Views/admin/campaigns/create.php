<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">New Campaign</h2>

<form action="/admin/campaigns/create" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="card mb-4">
        <div class="card-header">Campaign Details</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Campaign Name *</label>
                <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= old('description') ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Template</label>
                    <select name="template" class="form-select">
                        <option value="default">Default</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Countdown Target</label>
                    <input type="datetime-local" name="countdown_target" class="form-control" value="<?= old('countdown_target') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Starts At</label>
                    <input type="datetime-local" name="starts_at" class="form-control" value="<?= old('starts_at') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ends At</label>
                    <input type="datetime-local" name="ends_at" class="form-control" value="<?= old('ends_at') ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Thank You Message</label>
                <input type="text" name="thank_you_message" class="form-control" value="<?= old('thank_you_message') ?: 'Thank you for your submission!' ?>">
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Branding</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Primary Color</label>
                    <input type="color" name="primary_color" class="form-control form-control-color" value="<?= old('primary_color') ?: '#0d6efd' ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Background Color</label>
                    <input type="color" name="background_color" class="form-control form-control-color" value="<?= old('background_color') ?: '#ffffff' ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Text Color</label>
                    <input type="color" name="text_color" class="form-control form-control-color" value="<?= old('text_color') ?: '#333333' ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Font Family</label>
                    <select name="font_family" class="form-select">
                        <option value="'Inter', sans-serif">Inter</option>
                        <option value="'Roboto', sans-serif">Roboto</option>
                        <option value="'Open Sans', sans-serif">Open Sans</option>
                        <option value="'Montserrat', sans-serif">Montserrat</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Hero Image</label>
                    <input type="file" name="hero_image" class="form-control" accept="image/*">
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
            <p class="text-muted small">Add form fields that visitors will fill out on the microsite.</p>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Create Campaign</button>
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
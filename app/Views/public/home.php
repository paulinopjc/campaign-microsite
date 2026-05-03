<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Campaign Platform</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="/">Campaign Platform</a>
                <?php if (session()->get('logged_in')): ?>
                    <a href="/admin/dashboard" class="btn btn-outline-light btn-sm">Admin</a>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline-light btn-sm">Login</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="container py-5">
            <h1 class="mb-4">Active Campaigns</h1>

            <?php if (empty($campaigns)): ?>
                <p class="text-muted">No campaigns available at the moment.</p>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($campaigns as $campaign): ?>
                    <?php $branding = json_decode($campaign['branding'] ?? '{}', true); ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <?php if (!empty($branding['hero_image_url'])): ?>
                                <img src="<?= $branding['hero_image_url'] ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?= esc($campaign['name']) ?>">
                            <?php else: ?>
                                <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 180px; background-color: <?= $branding['primary_color'] ?? '#0d6efd' ?>;">
                                    <?php if (!empty($branding['logo_url'])): ?>
                                        <img src="<?= $branding['logo_url'] ?>" style="max-height: 80px;" alt="Logo">
                                    <?php else: ?>
                                        <span class="text-white fs-1"><?= esc(mb_substr($campaign['name'], 0, 1)) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= esc($campaign['name']) ?></h5>
                                <?php if ($campaign['description']): ?>
                                    <p class="card-text small"><?= esc(mb_strimwidth(strip_tags($campaign['description']), 0, 120, '...')) ?></p>
                                <?php endif; ?>
                                <a href="/c/<?= $campaign['slug'] ?>" class="btn btn-primary mt-auto">View Campaign</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>

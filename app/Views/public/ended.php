<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= esc($campaign['name']) ?> - Campaign Ended</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <div class="card p-5">
                        <h2 class="mb-3"><?= esc($campaign['name']) ?></h2>
                        <p class="text-muted fs-5">This campaign has ended.</p>
                        <p class="text-muted">Thank you for your interest. The registration period for this campaign is now closed.</p>
                        <a href="/" class="btn btn-outline-primary mt-3">View Other Campaigns</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

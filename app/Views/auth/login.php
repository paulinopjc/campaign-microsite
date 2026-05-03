<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Campaign Platform</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://accounts.google.com/gsi/client" async></script>
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-4 text-center">
                    <div class="card p-4">
                        <h3 class="mb-4">Campaign Platform</h3>
                        <div id="g_id_onload"
                             data-client_id="<?= getenv('GOOGLE_CLIENT_ID') ?>"
                             data-callback="handleCredentialResponse"
                             data-auto_prompt="false">
                        </div>
                        <div class="g_id_signin"
                             data-type="standard"
                             data-size="large"
                             data-theme="outline"
                             data-text="sign_in_with">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
        function handleCredentialResponse(response) {
            fetch('/auth/google', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({credential: response.credential})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) window.location.href = data.redirect;
                else alert(data.message || 'Login failed');
            });
        }
        </script>
    </body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Google Login VONIC</title>

    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
    <h1>Test Google Login VONIC</h1>
    <p>Silakan login menggunakan akun Google UGM.</p>

    <div id="g_id_onload"
         data-client_id="{{ config('services.google.client_id') }}"
         data-callback="handleGoogleLogin">
    </div>

    <div class="g_id_signin"
         data-type="standard"
         data-size="large"
         data-theme="outline"
         data-text="signin_with"
         data-shape="rectangular"
         data-logo_alignment="left">
    </div>

    <div id="result" style="margin-top: 20px;"></div>

    <script>
        async function handleGoogleLogin(response) {
            const result = document.getElementById('result');

            result.innerHTML = 'Sedang memproses login...';

            try {
                const res = await fetch('/api/login-user', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_token: response.credential
                    })
                });

                const data = await res.json();

                result.innerHTML = `
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
            } catch (error) {
                result.innerHTML = 'Terjadi kesalahan: ' + error.message;
            }
        }
    </script>
</body>
</html>

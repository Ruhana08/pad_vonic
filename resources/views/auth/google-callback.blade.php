<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Masuk...</title>
  <script>
    // Set Sanctum token + user data ke localStorage, lalu redirect ke home
    try {
      localStorage.setItem('vonic_token', @json($token));
      localStorage.setItem('vonic_user', @json($userData));
    } catch (e) {}
    window.location.replace(@json($homeUrl));
  </script>
</head>
<body>
  <p style="font-family:sans-serif;text-align:center;margin-top:40vh;color:#666;">Sedang masuk...</p>
</body>
</html>

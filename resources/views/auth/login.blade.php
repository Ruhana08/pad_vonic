<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Vonic</title>
</head>

<body>

    <h1>Login Vonic</h1>

    <p>Polling DTEDI SV UGM</p>

    <form method="POST" action="{{ route('login.process') }}">
        @csrf

        <label for="email">Email UGM</label>
        <br>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Masukkan email UGM"
            value="{{ old('email') }}"
            required
        >

        <br><br>

        <button type="submit">Login</button>
    </form>

    @if (session('error'))
        <p style="color: red;">
            {{ session('error') }}
        </p>
    @endif

    @error('email')
        <p style="color: red;">
            {{ $message }}
        </p>
    @enderror

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Treasury Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="login-wrapper">
    <div class="login-page">
        <div class="login-left">
            <div class="login-seal"></div>
            <div class="login-heading">
                <p class="login-title">Municipality of Prieto Diaz</p>
                <p class="login-subtitle">Treasury Management System</p>
            </div>
        </div>

        <div class="login-illustration login-illustration--cabinet"></div>
        <div class="login-illustration login-illustration--shadow"></div>
        <div class="login-illustration login-illustration--streetlight"></div>
        <div class="login-illustration login-illustration--speech"></div>
        <div class="login-illustration login-illustration--character"></div>
        <div class="login-illustration login-illustration--plant"></div>

        <div class="login-card">
            <div class="login-avatar"></div>

            <form method="POST" action="{{ route('login.attempt') }}" class="login-form">
                @csrf

                @error('username')
                    <p class="login-error">{{ $message }}</p>
                @enderror

                <input type="text" name="username" class="login-input" placeholder="Username" value="{{ old('username') }}">
                <input type="password" name="password" class="login-input" placeholder="Password">

                <label class="login-remember">
                    <input type="checkbox" name="remember" class="login-checkbox">
                    Remember Me
                </label>

                <button type="submit" class="login-submit">Sign In</button>
            </form>
        </div>
    </div>
    </div>
</body>
</html>

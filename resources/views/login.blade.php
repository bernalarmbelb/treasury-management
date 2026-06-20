<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Treasury Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="login-screen">
        {{-- Smart-city backdrop --}}
        <div class="login-city" aria-hidden="true">
            <svg viewBox="0 0 1440 260" preserveAspectRatio="xMidYMax slice">
                {{-- ground glow line --}}
                <path d="M0,236 C320,236 460,214 720,214 C980,214 1120,236 1440,236" fill="none" stroke="#7FE3F2" stroke-width="2" opacity="0.45"/>

                {{-- streetlight --}}
                <g opacity="0.85"><line x1="1340" y1="120" x2="1340" y2="260" stroke="#9BD2F5" stroke-width="3"/><circle cx="1340" cy="118" r="6" fill="#FFE7A8"/></g>

                {{-- light-trail dots --}}
                <g fill="#BFF1FA" opacity="0.9"><circle cx="260" cy="224" r="3"/><circle cx="540" cy="214" r="3"/><circle cx="820" cy="216" r="3"/><circle cx="1100" cy="224" r="3"/></g>
            </svg>
        </div>

        <div class="login-content">
            <div class="login-brand">
                <div class="login-seal"></div>
                <h1 class="login-title">Municipality of Prieto Diaz</h1>
                <p class="login-subtitle">Treasury Management System</p>
            </div>

            <div class="login-card">
                <form method="POST" action="{{ route('login.attempt') }}" class="login-form">
                    @csrf

                    @error('username')
                        <p class="login-error">{{ $message }}</p>
                    @enderror

                    <div class="login-field">
                        <label for="username" class="login-label">Username</label>
                        <div class="login-input-wrap">
                            <svg class="login-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input type="text" id="username" name="username" class="login-input" placeholder="Enter username" autocomplete="username" value="{{ old('username') }}" autofocus>
                        </div>
                    </div>

                    <div class="login-field">
                        <label for="password" class="login-label">Password</label>
                        <div class="login-input-wrap">
                            <svg class="login-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="password" id="password" name="password" class="login-input" placeholder="Enter password" autocomplete="current-password">
                            <button type="button" class="login-eye" data-toggle-password="password" aria-label="Show password">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="login-row">
                        <label class="login-remember">
                            <input type="checkbox" name="remember" class="login-checkbox">
                            Remember me
                        </label>
                        <a href="#" class="login-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="login-submit">Sign in</button>

                    <div class="login-foot">
                        <a href="#">Create account</a>
                        <span>&middot;</span>
                        <a href="#">Need help?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.getAttribute('data-toggle-password'));
                if (!input) return;
                var reveal = input.type === 'password';
                input.type = reveal ? 'text' : 'password';
                btn.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
                btn.classList.toggle('is-on', reveal);
            });
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — TaskMaster</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#0f1117; --bg2:#181c27; --bg3:#1e2333; --border:#2a3048;
            --text:#e8eaf2; --text2:#9aa0bb; --text3:#5c6380;
            --accent:#6366f1; --accent2:#818cf8; --accent-glow:rgba(99,102,241,0.25);
            --red:#ef4444; --radius:12px; --radius-sm:8px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        /* Decorative background grid */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.3;
            pointer-events: none;
        }
        .auth-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            box-shadow: 0 24px 64px rgba(0,0,0,0.5);
        }
        .auth-card::before {
            content: '';
            position: absolute;
            top: -1px; left: 30px; right: 30px; height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            border-radius: 0 0 4px 4px;
        }
        .brand { text-align: center; margin-bottom: 32px; }
        .brand h1 { font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800; }
        .brand h1 em { color: var(--accent2); font-style: normal; }
        .brand p { color: var(--text3); font-size: 14px; margin-top: 4px; }
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: 12px; font-weight: 600;
            color: var(--text2); text-transform: uppercase;
            letter-spacing: 0.5px; margin-bottom: 6px;
        }
        .form-control {
            width: 100%; padding: 11px 14px;
            background: var(--bg3); border: 1px solid var(--border);
            border-radius: var(--radius-sm); color: var(--text);
            font-family: 'DM Sans', sans-serif; font-size: 14px;
            outline: none; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }
        .form-control::placeholder { color: var(--text3); }
        .form-error { color: var(--red); font-size: 12px; margin-top: 4px; }
        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 20px; font-size: 13px; color: var(--text2);
        }
        .btn-primary {
            width: 100%; padding: 12px;
            background: var(--accent); color: white;
            border: none; border-radius: var(--radius-sm);
            font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 700;
            cursor: pointer; letter-spacing: 0.3px;
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
        }
        .btn-primary:hover {
            background: var(--accent2);
            box-shadow: 0 0 20px var(--accent-glow);
            transform: translateY(-1px);
        }
        .auth-footer {
            text-align: center; margin-top: 20px;
            font-size: 13px; color: var(--text3);
        }
        .auth-footer a { color: var(--accent2); text-decoration: none; font-weight: 500; }
        .auth-footer a:hover { text-decoration: underline; }
        .demo-box {
            background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.2);
            border-radius: var(--radius-sm); padding: 10px 14px;
            font-size: 12px; color: var(--text2); margin-bottom: 20px;
        }
        .demo-box strong { color: var(--accent2); }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="brand">
        <h1>Task<em>Master</em></h1>
        <p>Sign in to your student planner</p>
    </div>

    {{-- Demo credentials hint --}}
    <div class="demo-box">
        🎓 Demo: <strong>demo@taskmaster.com</strong> / <strong>password</strong>
    </div>

    @if($errors->any())
        <div style="color:#f87171;font-size:13px;margin-bottom:14px;padding:10px 14px;background:rgba(239,68,68,0.1);border-radius:8px;">
            ✕ {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" id="loginForm" novalidate>
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input id="email" type="email" name="email" class="form-control"
                   placeholder="you@example.com" value="{{ old('email') }}" autocomplete="email">
            <div class="form-error" id="emailErr"></div>
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input id="password" type="password" name="password" class="form-control"
                   placeholder="••••••••" autocomplete="current-password">
            <div class="form-error" id="passErr"></div>
        </div>
        <div class="remember-row">
            <input type="checkbox" name="remember" id="remember" value="1">
            <label for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn-primary">Sign In →</button>
    </form>

    <div class="auth-footer">
        Don't have an account? <a href="{{ route('register') }}">Register here</a>
    </div>
</div>

{{-- Client-side JavaScript validation --}}
<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    let valid = true;
    const email = document.getElementById('email');
    const pass  = document.getElementById('password');
    const emailErr = document.getElementById('emailErr');
    const passErr  = document.getElementById('passErr');

    // Clear previous errors
    emailErr.textContent = '';
    passErr.textContent  = '';

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim()) {
        emailErr.textContent = 'Email is required.';
        valid = false;
    } else if (!emailRegex.test(email.value)) {
        emailErr.textContent = 'Please enter a valid email address.';
        valid = false;
    }

    // Validate password
    if (!pass.value) {
        passErr.textContent = 'Password is required.';
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>
</body>
</html>

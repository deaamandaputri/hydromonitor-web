<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HydroMonitor - Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #eef6f8;
            --panel: #ffffff;
            --panel-soft: #f6fbfc;
            --line: #d9e7ea;
            --text: #102027;
            --muted: #667985;
            --brand: #0f8ea1;
            --brand-dark: #0b6576;
            --green: #16835f;
            --shadow: 0 24px 70px rgba(12, 61, 74, 0.14);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 82% 18%, rgba(22, 131, 95, 0.16), transparent 28%),
                linear-gradient(135deg, #eef6f8 0%, #f8fbf8 55%, #e8f3f1 100%);
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .auth-card {
            width: min(470px, 100%);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(217, 231, 234, 0.95);
            border-radius: 26px;
            box-shadow: var(--shadow);
            padding: 34px;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 26px;
            color: var(--brand-dark);
            font-weight: 800;
            font-size: 1.12rem;
        }

        .logo-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--brand), var(--green));
            color: #fff;
            box-shadow: 0 12px 24px rgba(15, 142, 161, 0.2);
        }

        h1 {
            margin: 0 0 8px;
            text-align: center;
            font-size: 1.8rem;
            letter-spacing: 0;
        }

        .subtitle {
            margin: 0 0 28px;
            text-align: center;
            color: var(--muted);
            line-height: 1.6;
        }

        .alert {
            margin-bottom: 20px;
            padding: 13px 14px;
            color: #a42323;
            background: #fff1f1;
            border: 1px solid #ffd1d1;
            border-radius: 12px;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .field { margin-bottom: 17px; }

        label {
            display: block;
            margin-bottom: 8px;
            color: #36515b;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .input-wrap { position: relative; }

        .input-wrap svg {
            position: absolute;
            left: 15px;
            top: 50%;
            width: 20px;
            height: 20px;
            transform: translateY(-50%);
            color: #86a1aa;
        }

        input {
            width: 100%;
            min-height: 50px;
            padding: 13px 15px 13px 48px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--panel-soft);
            color: var(--text);
            font: inherit;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        input:focus {
            border-color: rgba(15, 142, 161, 0.72);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(15, 142, 161, 0.12);
        }

        .btn {
            width: 100%;
            min-height: 52px;
            margin-top: 6px;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--brand), var(--green));
            color: #fff;
            cursor: pointer;
            font: inherit;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 14px 28px rgba(15, 142, 161, 0.24);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 34px rgba(15, 142, 161, 0.28);
        }

        .switch {
            margin: 22px 0 0;
            text-align: center;
            color: var(--muted);
            font-size: 0.94rem;
        }

        .text-link {
            color: var(--brand-dark);
            text-decoration: none;
            font-weight: 700;
        }

        .footer {
            margin-top: 34px;
            text-align: center;
            color: #8a9ca3;
            font-size: 0.78rem;
        }

        @media (max-width: 520px) {
            body { padding: 14px; place-items: start center; }
            .auth-card { padding: 26px 20px; border-radius: 22px; }
        }
    </style>
</head>
<body>
    <main class="auth-card">
        <div class="logo">
            <span class="logo-mark">
                <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3C8.5 6.2 6 9.4 6 13a6 6 0 0 0 12 0c0-3.6-2.5-6.8-6-10Z"/>
                </svg>
            </span>
            HydroMonitor
        </div>

        <h1>Buat Akun</h1>
        <p class="subtitle">Daftar untuk mengakses sistem monitoring tangki air.</p>

        <form action="{{ url('/register') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="field">
                <label for="name">Nama lengkap</label>
                <div class="input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z"/></svg>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Nama Anda" required>
                </div>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l8 5a2 2 0 0 0 2 0l8-5M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z"/></svg>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
                </div>
            </div>

            <div class="field">
                <label for="password">Kata sandi</label>
                <div class="input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z"/></svg>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>
            </div>

            <div class="field">
                <label for="password_confirmation">Konfirmasi kata sandi</label>
                <div class="input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z"/></svg>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********" required>
                </div>
            </div>

            <button type="submit" class="btn">
                Daftar
                <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM3 20a6 6 0 0 1 12 0v1H3v-1Z"/></svg>
            </button>

            <p class="switch">Sudah punya akun? <a class="text-link" href="{{ route('login') }}">Masuk di sini</a></p>
        </form>

        <div class="footer">2026 HydroMonitor. Smart Tank Monitoring System.</div>
    </main>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HydroMonitor - Login</title>
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
                radial-gradient(circle at 18% 16%, rgba(15, 142, 161, 0.18), transparent 28%),
                linear-gradient(135deg, #eef6f8 0%, #f8fbf8 55%, #e8f3f1 100%);
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .auth-shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: 1.08fr 0.92fr;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(217, 231, 234, 0.9);
            border-radius: 28px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .brand-panel {
            min-height: 620px;
            padding: 42px;
            background:
                linear-gradient(160deg, rgba(15, 142, 161, 0.92), rgba(16, 131, 95, 0.86)),
                url("{{ asset('images/tandon.png') }}") center/cover;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .logo-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.28);
            display: grid;
            place-items: center;
        }

        .brand-copy h1 {
            margin: 0 0 16px;
            max-width: 520px;
            font-size: clamp(2rem, 4vw, 3.6rem);
            line-height: 1.02;
            letter-spacing: 0;
        }

        .brand-copy p {
            margin: 0;
            max-width: 460px;
            color: rgba(255, 255, 255, 0.84);
            line-height: 1.7;
            font-size: 1rem;
        }

        .metric-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .metric {
            border: 1px solid rgba(255, 255, 255, 0.22);
            background: rgba(255, 255, 255, 0.14);
            border-radius: 16px;
            padding: 16px;
        }

        .metric strong {
            display: block;
            font-size: 1.35rem;
            margin-bottom: 4px;
        }

        .metric span {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.78);
        }

        .form-panel {
            padding: 52px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-panel h2 {
            margin: 0 0 24px;
            font-size: 2rem;
            letter-spacing: 0;
            text-align: center;
        }

        .subtitle {
            margin: 0 0 30px;
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

        .field { margin-bottom: 18px; }

        label {
            display: block;
            margin-bottom: 8px;
            color: #36515b;
            font-size: 0.82rem;
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

        input[type="email"],
        input[type="password"] {
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

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin: 2px 0 24px;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 9px;
        }

        .remember input { accent-color: var(--brand); }

        .text-link {
            color: var(--brand-dark);
            text-decoration: none;
            font-weight: 700;
        }

        .btn {
            width: 100%;
            min-height: 52px;
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
            margin: 24px 0 0;
            text-align: center;
            color: var(--muted);
            font-size: 0.94rem;
        }

        .footer {
            margin-top: 36px;
            text-align: center;
            color: #8a9ca3;
            font-size: 0.78rem;
        }

        @media (max-width: 820px) {
            body { padding: 14px; place-items: start center; }
            .auth-shell { grid-template-columns: 1fr; border-radius: 22px; }
            .brand-panel { min-height: 300px; padding: 28px; }
            .metric-row { grid-template-columns: 1fr 1fr 1fr; }
            .form-panel { padding: 34px 24px; }
        }

        @media (max-width: 520px) {
            .metric-row { display: none; }
            .brand-panel { min-height: 240px; }
            .form-options { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <main class="auth-shell">
        <section class="brand-panel">
            <div class="logo">
                <span class="logo-mark">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3C8.5 6.2 6 9.4 6 13a6 6 0 0 0 12 0c0-3.6-2.5-6.8-6-10Z"/>
                    </svg>
                </span>
                HydroMonitor
            </div>

            <div class="brand-copy">
                <h1>Monitoring tangki air real-time.</h1>
                <p>Pantau ketinggian, kejernihan, aliran air, dan status pompa dalam satu dashboard yang mudah dibaca.</p>
            </div>

            <div class="metric-row">
                <div class="metric"><strong>24/7</strong><span>Pemantauan</span></div>
                <div class="metric"><strong>IoT</strong><span>ESP32</span></div>
                <div class="metric"><strong>Live</strong><span>Sensor data</span></div>
            </div>
        </section>

        <section class="form-panel">
            <h2>Login</h2>

            <form action="{{ url('/login') }}" method="POST">
                @csrf

                @if($errors->any())
                    <div class="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

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

                <div class="form-options">
                    <label class="remember" for="remember">
                        <input type="checkbox" id="remember" name="remember">
                        Ingat saya
                    </label>
                    <a class="text-link" href="#">Lupa kata sandi?</a>
                </div>

                <button type="submit" class="btn">
                    Masuk ke Dashboard
                    <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0-7 7m7-7H3"/></svg>
                </button>

                <p class="switch">Belum punya akun? <a class="text-link" href="{{ route('register') }}">Daftar sekarang</a></p>
            </form>

            <div class="footer">2026 Smart Tank Monitoring System.</div>
        </section>
    </main>
</body>
</html>

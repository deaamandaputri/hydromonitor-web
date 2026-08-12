<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Tank Monitoring System - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <style>
        :root {
            --bg: #eef6f8;
            --surface: #ffffff;
            --surface-soft: #f6fbfc;
            --line: #d9e7ea;
            --text: #102027;
            --muted: #667985;
            --brand: #0f8ea1;
            --brand-dark: #0b6576;
            --green: #16835f;
            --amber: #b76b00;
            --red: #b42323;
            --shadow: 0 18px 50px rgba(12, 61, 74, 0.11);
            --radius: 18px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 12% 10%, rgba(15, 142, 161, 0.14), transparent 25%),
                linear-gradient(180deg, #eef6f8 0%, #f8fbf8 100%);
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            margin-left: -280px;
            background: rgba(255, 255, 255, 0.9);
            border-right: 1px solid var(--line);
            padding: 24px;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(12, 61, 74, 0.05);
            backdrop-filter: blur(14px);
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            transition: margin-left 0.3s ease;
            position: sticky;
            top: 0;
        }

        .sidebar.active {
            margin-left: 0;
        }

        @media (max-width: 980px) {
            .sidebar {
                position: fixed;
                margin-left: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.active {
                transform: translateX(0);
            }
        }

        .main-content {
            flex: 1;
            padding: 16px 24px;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-profile {
            margin: 32px 0;
            padding: 20px;
            background: var(--surface-soft);
            border: 1px solid var(--line);
            border-radius: 16px;
            text-align: center;
        }

        .profile-img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-bottom: 12px;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(15, 142, 161, 0.15);
            object-fit: cover;
        }

        .profile-name {
            font-weight: 800;
            color: var(--text);
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .profile-role {
            color: var(--brand);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .nav-item:hover {
            background: var(--surface-soft);
            color: var(--brand);
        }

        .nav-item.active {
            background: #f0fbfc;
            color: var(--brand);
            border: 1px solid #d2ecef;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 24px;
        }

        .sidebar-logout {
            width: 100%;
            justify-content: center;
        }

        .app-main {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            flex: 1;
        }

        .topbar-title h2 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        .topbar-title p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 0.9rem;
        }

        @media (max-width: 980px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: static; border-right: none; border-bottom: 1px solid var(--line); }
            .main-content { padding: 16px; }
        }

        .app {
            width: min(1220px, calc(100% - 32px));
            margin: 0 auto;
            padding: 24px 0 42px;
        }

        .topbar {
            min-height: 74px;
            padding: 16px 18px;
            border: 1px solid var(--line);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.86);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: sticky;
            top: 14px;
            z-index: 5;
            backdrop-filter: blur(14px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }

        .brand-mark {
            width: 46px;
            height: 46px;
            flex: 0 0 auto;
            border-radius: 14px;
            display: grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), var(--green));
            box-shadow: 0 12px 24px rgba(15, 142, 161, 0.2);
        }

        .brand h1 {
            margin: 0;
            font-size: 1.18rem;
            line-height: 1.2;
            letter-spacing: 0;
        }

        .brand p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 0.88rem;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .pill {
            min-height: 38px;
            padding: 8px 13px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: var(--surface-soft);
            color: var(--muted);
            font-weight: 700;
            font-size: 0.86rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .pill::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #93a4aa;
        }

        .pill.is-online {
            color: var(--green);
            background: #effaf5;
            border-color: #cdeee0;
        }

        .pill.is-online::before {
            background: var(--green);
            box-shadow: 0 0 0 5px rgba(22, 131, 95, 0.12);
        }

        .logout-btn {
            min-height: 38px;
            border: 1px solid #ffd0d0;
            background: #fff5f5;
            color: var(--red);
            border-radius: 999px;
            padding: 8px 14px;
            cursor: pointer;
            font: inherit;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .hero {
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1.25fr 0.75fr;
            gap: 16px;
        }

        .panel {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .tank-panel {
            padding: 16px 20px;
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 20px;
            align-items: center;
        }

        .ring {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: conic-gradient(var(--brand) 0%, #e4eef0 0);
            display: grid;
            place-items: center;
            transition: background 0.35s ease;
        }

        .ring-inner {
            width: 116px;
            height: 116px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid var(--line);
            display: grid;
            place-items: center;
            text-align: center;
            box-shadow: inset 0 2px 10px rgba(12, 61, 74, 0.08);
        }

        .ring-inner strong {
            display: block;
            font-size: 2.2rem;
            line-height: 1;
            letter-spacing: 0;
        }

        .ring-inner span {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-size: 0.86rem;
            font-weight: 700;
        }

        .section-label {
            margin: 0 0 10px;
            color: var(--brand-dark);
            font-size: 0.82rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .tank-copy h2 {
            margin: 0;
            font-size: clamp(1.5rem, 3vw, 2.5rem);
            line-height: 1.02;
            letter-spacing: 0;
        }

        .tank-copy p {
            margin: 8px 0 12px;
            color: var(--muted);
            line-height: 1.5;
            font-size: 0.9rem;
            max-width: 560px;
        }

        .status-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .status-chip {
            padding: 9px 12px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: var(--surface-soft);
            color: #36515b;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .summary-panel {
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 12px;
        }

        .summary-panel h2 {
            margin: 0;
            font-size: 1.2rem;
            letter-spacing: 0;
        }

        .summary-panel p {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .pump-state {
            border: 1px solid #f2dfb7;
            background: #fff9ed;
            color: var(--amber);
            border-radius: 16px;
            padding: 12px 18px;
        }

        .pump-state span {
            display: block;
            color: #8a6b37;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 5px;
        }

        .pump-state strong {
            display: block;
            font-size: 1.8rem;
            line-height: 1.1;
        }

        .grid {
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .card {
            padding: 16px;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .card-title {
            margin: 0;
            color: #36515b;
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .icon-box {
            width: 36px;
            height: 36px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            background: #eef8fa;
            color: var(--brand);
            border: 1px solid #d2ecef;
        }

        .value {
            display: flex;
            align-items: baseline;
            gap: 8px;
            flex-wrap: wrap;
        }

        .value strong {
            font-size: 2rem;
            line-height: 1;
            letter-spacing: 0;
        }

        .value span {
            color: var(--muted);
            font-weight: 800;
        }

        .caption {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.4;
            font-size: 0.85rem;
        }

        .quality-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            margin-top: 16px;
            padding: 8px 11px;
            border-radius: 999px;
            background: #effaf5;
            border: 1px solid #cdeee0;
            color: var(--green);
            font-weight: 800;
            font-size: 0.86rem;
        }

        .muted-line {
            margin-top: 12px;
            color: #7c9098;
            text-align: center;
            font-size: 0.8rem;
        }

        @media (max-width: 980px) {
            .hero { grid-template-columns: 1fr; }
            .grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 720px) {
            .app { width: min(100% - 24px, 1220px); padding-top: 12px; }
            .topbar { position: static; align-items: flex-start; flex-direction: column; border-radius: 18px; }
            .topbar-actions { justify-content: flex-start; width: 100%; }
            .tank-panel { grid-template-columns: 1fr; padding: 22px; }
            .ring { margin: 0 auto; }
            .grid { grid-template-columns: 1fr; }
            .value strong { font-size: 2.3rem; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">
                    <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3C8.5 6.2 6 9.4 6 13a6 6 0 0 0 12 0c0-3.6-2.5-6.8-6-10Z"/>
                    </svg>
                </div>
                <div>
                    <h1>Smart Tank</h1>
                    <p style="margin:0;color:var(--muted);font-size:0.8rem;">Monitoring System</p>
                </div>
            </div>

            <div class="sidebar-profile">
                <img src="{{ asset('profile.jpg') }}" alt="Profile" class="profile-img">
                <div class="profile-info">
                    <div class="profile-name">{{ auth()->user()->name ?? 'Admin Tangki' }}</div>
                    <div class="profile-role">Pemilik Tangki</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="#" class="nav-item active">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('history') }}" class="nav-item">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Riwayat Sensor
                </a>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST" style="margin: 0; width: 100%;">
                    @csrf
                    <button type="submit" class="logout-btn sidebar-logout">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="main-content">
            <header class="topbar" style="border:none;box-shadow:none;background:transparent;padding:0 0 12px 0;position:static;min-height:auto;flex-shrink:0;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <button id="sidebar-toggle" style="background: none; border: none; cursor: pointer; padding: 8px; color: var(--text); display: grid; place-items: center; border-radius: 8px;">
                        <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="topbar-title">
                        <h2 style="margin: 0;font-size: 1.5rem;color: var(--text);letter-spacing: -0.02em;">Smart Tank</h2>
                        <p style="margin: 4px 0 0;color: var(--muted);font-size: 0.9rem;">Pantau kondisi air secara realtime</p>
                    </div>
                </div>
                <div class="topbar-actions">
                    <div class="pill" id="online-status">Menghubungkan</div>
                </div>
            </header>

            <main class="app-main">
                <section class="hero">
                    <div class="panel tank-panel">
                        <div class="ring" id="water-level-chart">
                            <div class="ring-inner">
                                <div>
                                    <strong id="water-percent-val">0</strong>
                                    <span>Persen penuh</span>
                                </div>
                            </div>
                        </div>

                        <div class="tank-copy">
                            <p class="section-label">Kapasitas Tangki</p>
                            <h2 id="water-level-val">0 cm</h2>
                            <p>Tinggi air dihitung dari atas sampai dasar tangki. Warna indikator akan berubah sesuai level air .</p>
                            <div class="status-strip">
                                <span class="status-chip">ESP32 connected</span>
                                <span class="status-chip">Auto refresh 2s</span>
                            </div>
                        </div>
                    </div>

                    <aside class="panel summary-panel">
                        <div class="pump-state" id="pump-card-bg" style="border: 1px solid #f2dfb7; background: #fff9ed; color: var(--amber); border-radius: 16px; padding: 10px 16px; text-align: center; transition: all 0.3s ease; flex-shrink: 0; margin-bottom: 8px;">
                            <span id="pump-card-title" style="display: block; color: #8a6b37; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 3px;">STATUS POMPA</span>
                            <strong id="pump-status-val" style="display: block; font-size: 1.5rem; line-height: 1.1; font-weight: 800;">Menunggu</strong>
                        </div>
                        
                        <div style="display: flex; gap: 8px; flex-shrink: 0;">
                            <div id="pompa1-card-bg" style="flex: 1; border: 1px solid var(--line); background: #f8fafc; border-radius: 12px; padding: 8px; text-align: center; transition: all 0.3s ease;">
                                <span style="display: block; color: #667985; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; margin-bottom: 2px;">POMPA 1</span>
                                <strong id="pompa1-status-val" style="display: block; font-size: 1.1rem; font-weight: 800; color: #36515b;">-</strong>
                            </div>
                            <div id="pompa2-card-bg" style="flex: 1; border: 1px solid var(--line); background: #f8fafc; border-radius: 12px; padding: 8px; text-align: center; transition: all 0.3s ease;">
                                <span style="display: block; color: #667985; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; margin-bottom: 2px;">POMPA 2</span>
                                <strong id="pompa2-status-val" style="display: block; font-size: 1.1rem; font-weight: 800; color: #36515b;">-</strong>
                            </div>
                        </div>

                        <!-- KONTROL MANUAL -->
                        <div class="pump-controls" style="margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid var(--line);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="font-size: 0.85rem; font-weight: 700; color: #36515b;">Mode Relay</span>
                                <select id="pump-mode-select" onchange="changePumpMode()" style="padding: 6px 12px; border-radius: 8px; border: 1px solid var(--line); font-family: inherit; font-weight: 600; font-size: 0.85rem; background: #fff; cursor: pointer;">
                                    <option value="auto">Auto</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>
                            <div id="manual-controls" style="display: none; flex-direction: column; gap: 8px;">
                                <!-- Pompa -->
                                <div style="font-size: 0.7rem; font-weight: bold; color: #667985; text-transform: uppercase;">Pompa:</div>
                                <div style="display: flex; gap: 8px;">
                                    <button id="btn-pump-on" onclick="sendRelayCommand('pump', 'ON')" style="flex: 1; background: #effaf5; color: var(--green); border: 1px solid #cdeee0; padding: 6px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.2s;">ON</button>
                                    <button id="btn-pump-off" onclick="sendRelayCommand('pump', 'OFF')" style="flex: 1; background: #fff1f1; color: var(--red); border: 1px solid #ffd1d1; padding: 6px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.2s;">OFF</button>
                                </div>
                                <!-- Pompa 1 -->
                                <div style="font-size: 0.7rem; font-weight: bold; color: #667985; text-transform: uppercase; margin-top: 4px;">Pompa 1:</div>
                                <div style="display: flex; gap: 8px;">
                                    <button id="btn-pompa1-on" onclick="sendRelayCommand('pompa1', 'ON')" style="flex: 1; background: #effaf5; color: var(--green); border: 1px solid #cdeee0; padding: 6px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.2s;">ON</button>
                                    <button id="btn-pompa1-off" onclick="sendRelayCommand('pompa1', 'OFF')" style="flex: 1; background: #fff1f1; color: var(--red); border: 1px solid #ffd1d1; padding: 6px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.2s;">OFF</button>
                                </div>
                                <!-- Pompa 2 -->
                                <div style="font-size: 0.7rem; font-weight: bold; color: #667985; text-transform: uppercase; margin-top: 4px;">Pompa 2:</div>
                                <div style="display: flex; gap: 8px;">
                                    <button id="btn-pompa2-on" onclick="sendRelayCommand('pompa2', 'ON')" style="flex: 1; background: #effaf5; color: var(--green); border: 1px solid #cdeee0; padding: 6px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.2s;">ON</button>
                                    <button id="btn-pompa2-off" onclick="sendRelayCommand('pompa2', 'OFF')" style="flex: 1; background: #fff1f1; color: var(--red); border: 1px solid #ffd1d1; padding: 6px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.2s;">OFF</button>
                                </div>
                            </div>
                        </div>

                        <div style="flex: 1; min-height: 0; width: 100%; position: relative; padding-top: 8px;">
                            <canvas id="usageChart"></canvas>
                        </div>
                    </aside>
                </section>

                <section class="grid">
                    <article class="panel card">
                        <div>
                            <div class="card-head">
                                <h3 class="card-title">Kecepatan Aliran</h3>
                                <div class="icon-box">
                                    <svg width="21" height="21" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7Z"/></svg>
                                </div>
                            </div>
                            <div class="value">
                                <strong id="flow-rate-val">0.00</strong>
                                <span>L/min</span>
                            </div>
                        </div>
                        <p class="caption">Total air mengalir: <strong id="total-litres-val">0.000 L</strong></p>
                    </article>

                    <article class="panel card">
                        <div>
                            <div class="card-head">
                                <h3 class="card-title">Kejernihan Air</h3>
                                <div class="icon-box" style="background:#effaf5;color:var(--green);border-color:#cdeee0;">
                                    <svg width="21" height="21" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15.4a2 2 0 0 0-1-.5l-2.4-.5a6 6 0 0 0-3.8.5l-.4.2a6 6 0 0 1-3.8.5l-1.9-.4a2 2 0 0 0-1.8.5M8 4h8l-1 1v5.2a2 2 0 0 0 .6 1.4l5 5A2 2 0 0 1 19.2 20H4.8a2 2 0 0 1-1.4-3.4l5-5A2 2 0 0 0 9 10.2V5L8 4Z"/></svg>
                                </div>
                            </div>
                            <div class="value" style="margin-bottom: 8px;">
                                <strong id="clarity-status-val" style="font-size: 1.5rem; font-weight: 800; color: var(--green); display: block; line-height: 1.2;">Air Sangat Jernih</strong>
                                <span style="font-size: 0.7rem; color: var(--muted); font-weight: 500; margin-top: 4px; display: block; opacity: 0.6;">(Tegangan Sensor: <span id="turbidity-val">0.00</span> Volt)</span>
                            </div>
                        </div>
                        <span class="quality-badge" id="water-status-val" style="display: block; width: 100%; white-space: normal; line-height: 1.4;">Menganalisa</span>
                    </article>

                    <article class="panel card">
                        <div>
                            <div class="card-head">
                                <h3 class="card-title">Jarak Sensor</h3>
                                <div class="icon-box" style="background:#fff7e8;color:var(--amber);border-color:#f2dfb7;">
                                    <svg width="21" height="21" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16M8 4v16m8-16v16"/></svg>
                                </div>
                            </div>
                            <div class="value">
                                <strong id="proximity-val">0</strong>
                                <span>cm</span>
                            </div>
                        </div>
                        <p class="caption">Jarak ultrasonic ke permukaan air.</p>
                    </article>
                </section>

                <div class="muted-line">Smart Water Tank memperbarui data otomatis setiap 2 detik.</div>
            </main>
        </div>
    </div>

    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        const previous = {
            percent: 0,
            flow: 0,
            turbidity: 0,
            proximity: 0
        };

        function numberValue(value, fallback = 0) {
            const parsed = Number.parseFloat(value);
            return Number.isFinite(parsed) ? parsed : fallback;
        }

        function setStatusBadge(isOnline) {
            const status = document.getElementById('online-status');
            status.textContent = isOnline ? 'Online' : 'Offline';
            status.classList.toggle('is-online', isOnline);
        }

        function animateNumber(element, from, to, duration = 500, decimals = 0) {
            if (element) {
                element.textContent = to.toFixed(decimals);
            }
        }

        function setQualityBadge(text, voltage) {
            const textBadge = document.getElementById('water-status-val');
            textBadge.textContent = (text || 'Menganalisa').toString();
            const upper = textBadge.textContent.toUpperCase();

            let textColor = '#16835f';
            let textBg = '#effaf5';
            let textBorder = '#cdeee0';

            if (upper.includes('KERUH') && !upper.includes('ABAIKAN') || upper.includes('PERINGATAN')) {
                textColor = '#b42323';
                textBg = '#fff1f1';
                textBorder = '#ffd1d1';
            } else if (upper.includes('MENUNGGU') || upper.includes('ISTIRAHAT')) {
                textColor = '#b76b00';
                textBg = '#fff7e8';
                textBorder = '#f2dfb7';
            }

            textBadge.style.color = textColor;
            textBadge.style.background = textBg;
            textBadge.style.borderColor = textBorder;

            const clarityBadge = document.getElementById('clarity-status-val');
            let clarityColor = '#16835f';
            let clarityText = 'Air Bersih';

            if (voltage >= 2.00) {
                clarityColor = '#16835f';
                clarityText = 'Air Sangat Jernih';
            } else if (voltage >= 1.61) {
                clarityColor = '#16835f';
                clarityText = 'Air Bersih';
            } else {
                clarityColor = '#b42323';
                clarityText = 'Air Keruh';
            }

            if (clarityBadge) {
                clarityBadge.textContent = clarityText;
                clarityBadge.style.color = clarityColor;
            }
        }

        function setPumpState(state) {
            const status = document.getElementById('pump-status-val');
            const bg = document.getElementById('pump-card-bg');
            const title = document.getElementById('pump-card-title');
            
            if (!state) state = 'Non-Aktif';
            status.textContent = state;

            if (state.toLowerCase() === 'aktif') {
                bg.style.background = '#effaf5';
                bg.style.borderColor = '#cdeee0';
                bg.style.color = 'var(--green)';
                title.style.color = '#52636d';
            } else {
                bg.style.background = '#fff9ed';
                bg.style.borderColor = '#f2dfb7';
                bg.style.color = 'var(--amber)';
                title.style.color = '#8a6b37';
            }
        }
        
        function setPompaState(id, state) {
            const status = document.getElementById(`pompa${id}-status-val`);
            const bg = document.getElementById(`pompa${id}-card-bg`);
            
            if (!state) state = 'Non-Aktif';
            status.textContent = state;

            if (state.toLowerCase() === 'aktif') {
                bg.style.background = '#effaf5';
                bg.style.borderColor = '#cdeee0';
                status.style.color = 'var(--green)';
            } else {
                bg.style.background = '#f8fafc';
                bg.style.borderColor = 'var(--line)';
                status.style.color = '#36515b';
            }
        }

        function setWaterRing(percent) {
            const ring = document.getElementById('water-level-chart');
            let color = '#0f8ea1';

            if (percent < 20) {
                color = '#b42323';
            } else if (percent < 45) {
                color = '#b76b00';
            } else if (percent > 80) {
                color = '#16835f';
            }

            ring.style.background = `conic-gradient(${color} ${percent}%, #e4eef0 ${percent}%)`;
        }

        function updateDashboard(data) {
            setStatusBadge(true);

            const percent = Math.max(0, Math.min(100, numberValue(data.water_level_percent)));
            const waterLevel = numberValue(data.water_level_cm);
            const flow = numberValue(data.flow_rate);
            const total = numberValue(data.total_litres);
            const turbidity = numberValue(data.turbidity_voltage);
            const proximity = numberValue(data.proximity);

            if (Math.round(previous.percent) !== Math.round(percent)) {
                animateNumber(document.getElementById('water-percent-val'), previous.percent, percent, 650, 0);
                previous.percent = percent;
            }
            setWaterRing(percent);

            document.getElementById('water-level-val').textContent = `${waterLevel.toFixed(1)} cm`;

            if (previous.flow !== flow) {
                animateNumber(document.getElementById('flow-rate-val'), previous.flow, flow, 550, 2);
                previous.flow = flow;
            }

            document.getElementById('total-litres-val').textContent = `${total.toFixed(3)} L`;

            if (Math.abs(previous.turbidity - turbidity) >= 0.03 || previous.turbidity === 0) {
                document.getElementById('turbidity-val').textContent = turbidity.toFixed(2);
                previous.turbidity = turbidity;
            }

            if (Math.round(previous.proximity) !== Math.round(proximity)) {
                animateNumber(document.getElementById('proximity-val'), previous.proximity, proximity, 550, 0);
                previous.proximity = proximity;
            }

            setQualityBadge(data.water_status, turbidity);
            setPumpState(data.pump_status);
            setPompaState(1, data.pompa1_status);
            setPompaState(2, data.pompa2_status);
        }

        function fetchLatestData() {
            fetch('/api/sensor/latest')
                .then((response) => {
                    if (!response.ok) throw new Error('Request failed');
                    return response.json();
                })
                .then((data) => {
                    if (data) updateDashboard(data);
                })
                .catch(err => {
                    setStatusBadge(false);
                    console.error("Error fetching live data:", err);
                });
        }

        // --- CHART LOGIC ---
        function loadChart() {
            fetch('/api/chart-data')
                .then(res => res.json())
                .then(data => {
                    const ctx = document.getElementById('usageChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Air Digunakan (Liter)',
                                data: data.values,
                                backgroundColor: 'rgba(15, 142, 161, 0.85)',
                                borderRadius: 6,
                                barThickness: 'flex',
                                maxBarThickness: 32
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                title: {
                                    display: true,
                                    text: 'Pemakaian 7 Hari Terakhir',
                                    color: '#667985',
                                    font: { family: 'Inter', size: 11, weight: 'bold' },
                                    padding: { top: 0, bottom: 8 }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#eef6f8' },
                                    border: { display: false },
                                    ticks: { font: { size: 10 }, color: '#8b9ea7', maxTicksLimit: 5 }
                                },
                                x: {
                                    grid: { display: false },
                                    border: { display: false },
                                    ticks: { font: { size: 10 }, color: '#8b9ea7' }
                                }
                            }
                        }
                    });
                })
                .catch(err => console.error("Error loading chart:", err));
        }

        // --- CONTROL PUMP LOGIC ---
        // Variabel untuk menyimpan state manual sementara
        let currentManualState = {
            pump: 'OFF',
            pompa1: 'OFF',
            pompa2: 'OFF'
        };

        function changePumpMode() {
            const mode = document.getElementById('pump-mode-select').value;
            const manualControls = document.getElementById('manual-controls');
            
            if (mode === 'manual') {
                manualControls.style.display = 'flex';
            } else {
                manualControls.style.display = 'none';
            }
            
            fetch('/api/pump/control', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ 
                    mode: mode, 
                    pump: currentManualState.pump,
                    pompa1: currentManualState.pompa1,
                    pompa2: currentManualState.pompa2
                })
            }).catch(err => console.error("Error setting mode:", err));
        }

        function sendRelayCommand(type, state) {
            const btnOn = document.getElementById(`btn-${type}-on`);
            const btnOff = document.getElementById(`btn-${type}-off`);

            // Update internal state
            currentManualState[type] = state;

            // Reset warna tombol ke default
            btnOn.style.background = '#effaf5';
            btnOn.style.color = 'var(--green)';
            btnOff.style.background = '#fff1f1';
            btnOff.style.color = 'var(--red)';

            // Beri warna tebal pada tombol yang ditekan
            if (state === 'ON') {
                btnOn.style.background = 'var(--green)';
                btnOn.style.color = '#fff';
            } else if (state === 'OFF') {
                btnOff.style.background = 'var(--red)';
                btnOff.style.color = '#fff';
            }

            fetch('/api/pump/control', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ 
                    mode: 'manual', 
                    pump: currentManualState.pump,
                    pompa1: currentManualState.pompa1,
                    pompa2: currentManualState.pompa2
                })
            }).then(res => res.json())
              .then(data => {
                  console.log("Relay command sent", data);
              })
              .catch(err => console.error("Error sending relay command:", err));
        }

        setInterval(fetchLatestData, 2000);
        fetchLatestData(); // First load
        loadChart(); // Load chart once on page load

        // --- KONEKSI MQTT WEBSOCKET REAL-TIME (LANGSUNG DARI ESP32) ---
        try {
            const mqttClient = mqtt.connect('wss://broker.emqx.io:8084/mqtt');
            mqttClient.on('connect', function () {
                console.log('Terhubung ke Broker MQTT EMQX via WebSocket');
                mqttClient.subscribe('hydromonitor/sensor_data');
            });
            mqttClient.on('message', function (topic, message) {
                if (topic === 'hydromonitor/sensor_data') {
                    try {
                        const data = JSON.parse(message.toString());
                        if (data) {
                            updateDashboard(data);
                        }
                    } catch (e) {
                        console.error('Gagal membaca data JSON MQTT:', e);
                    }
                }
            });
        } catch (e) {
            console.error('Gagal koneksi WebSocket MQTT:', e);
        }
    </script>
</body>
</html>

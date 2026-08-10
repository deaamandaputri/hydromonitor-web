import sys
import os

file_path = r'd:\TA TK3B\TA_DEYA\TA_DEYA\Web\resources\views\dashboard.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

new_css = '''
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: rgba(255, 255, 255, 0.9); border-right: 1px solid var(--line); padding: 24px; display: flex; flex-direction: column; box-shadow: 4px 0 24px rgba(12, 61, 74, 0.05); backdrop-filter: blur(14px); position: sticky; top: 0; height: 100vh; overflow-y: auto; z-index: 10; }
        .main-content { flex: 1; padding: 24px 32px; min-width: 0; display: flex; flex-direction: column; }
        .sidebar-profile { margin: 32px 0; padding: 20px; background: var(--surface-soft); border: 1px solid var(--line); border-radius: 16px; text-align: center; }
        .profile-img { width: 72px; height: 72px; border-radius: 50%; margin-bottom: 12px; border: 3px solid #fff; box-shadow: 0 8px 16px rgba(15, 142, 161, 0.15); object-fit: cover; }
        .profile-name { font-weight: 800; color: var(--text); font-size: 1.1rem; margin-bottom: 4px; }
        .profile-role { color: var(--brand); font-size: 0.85rem; font-weight: 600; }
        .sidebar-nav { display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 12px; color: var(--muted); text-decoration: none; font-weight: 600; transition: all 0.2s ease; }
        .nav-item:hover { background: var(--surface-soft); color: var(--brand); }
        .nav-item.active { background: #f0fbfc; color: var(--brand); border: 1px solid #d2ecef; }
        .sidebar-footer { margin-top: auto; padding-top: 24px; }
        .sidebar-logout { width: 100%; justify-content: center; }
        .app-main { width: 100%; max-width: 1100px; margin: 0 auto; }
        .topbar-title h2 { margin: 0; font-size: 1.5rem; color: var(--text); letter-spacing: -0.02em; }
        .topbar-title p { margin: 4px 0 0; color: var(--muted); font-size: 0.9rem; }
        @media (max-width: 980px) { .layout { flex-direction: column; } .sidebar { width: 100%; height: auto; position: static; border-right: none; border-bottom: 1px solid var(--line); } .main-content { padding: 16px; } }
'''

content = content.replace('        .app {', new_css + '\n        .app {')

body_start = content.find('<body>') + len('<body>')
body_end = content.find('<script>')

new_body = '''
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
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin Tangki') }}&background=0f8ea1&color=fff&size=128" alt="Profile" class="profile-img">
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
            <header class="topbar" style="border:none;box-shadow:none;background:transparent;padding:0 0 24px 0;position:static;min-height:auto;">
                <div class="topbar-title">
                    <h2>Overview Tangki</h2>
                    <p>Pantau kondisi air secara realtime</p>
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
                            <p>Tinggi air dihitung dari dasar tangki. Warna indikator akan berubah saat level air rendah agar lebih mudah diperhatikan.</p>
                            <div class="status-strip">
                                <span class="status-chip">Realtime sensor</span>
                                <span class="status-chip">ESP32 connected</span>
                                <span class="status-chip">Auto refresh 2s</span>
                            </div>
                        </div>
                    </div>

                    <aside class="panel summary-panel" style="justify-content: center; gap: 10px;">
                        <div class="pump-state" id="pump-card-bg" style="border: 1px solid #f2dfb7; background: #fff9ed; color: var(--amber); border-radius: 16px; padding: 26px; text-align: center; transition: all 0.3s ease;">
                            <span id="pump-card-title" style="display: block; color: #8a6b37; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">STATUS POMPA / RELAY</span>
                            <strong id="pump-status-val" style="display: block; font-size: 2.8rem; line-height: 1.1; font-weight: 800;">Menunggu</strong>
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
                            <div class="value">
                                <strong id="turbidity-val">0.00</strong>
                                <span>Volt</span>
                            </div>
                        </div>
                        <span class="quality-badge" id="water-status-val">Menganalisa</span>
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
                        <p class="caption">Pembacaan jarak ultrasonic dari sensor ke permukaan air.</p>
                    </article>
                </section>

                <div class="muted-line">HydroMonitor memperbarui data otomatis setiap 2 detik.</div>
            </main>
        </div>
    </div>
'''

content = content[:body_start] + new_body + content[body_end:]

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

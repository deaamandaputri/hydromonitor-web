<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Tank Monitoring System - Riwayat Sensor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #eef6f8;
            --surface: #ffffff;
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
            font-family: Inter, system-ui, -apple-system, sans-serif;
            color: var(--text);
            background: linear-gradient(180deg, #eef6f8 0%, #f8fbf8 100%);
            padding: 20px 0;
        }

        .container {
            width: min(1200px, calc(100% - 32px));
            margin: 0 auto;
        }

        .header-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 999px;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.9rem;
            cursor: pointer;
            border: 1px solid var(--line);
            background: var(--surface);
            color: var(--text);
            transition: all 0.2s;
        }

        .btn:hover {
            background: #f0f7f8;
            border-color: var(--brand);
        }

        .btn-primary {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        .btn-primary:hover {
            background: var(--brand-dark);
            color: #fff;
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 24px;
            overflow: hidden;
        }

        h1 {
            margin: 0;
            font-size: 1.8rem;
            color: var(--brand-dark);
        }

        .table-wrapper {
            overflow-x: auto;
            margin-top: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }

        th {
            background: #eff6f8;
            color: #36515b;
            padding: 14px 16px;
            font-weight: 800;
            border-bottom: 2px solid var(--line);
            text-transform: uppercase;
            font-size: 0.78rem;
            letter-spacing: 0.05em;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            color: #33444c;
        }

        tr:hover td {
            background: #f7fcfd;
        }

        .badge {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .badge-clean { background: #effaf5; color: var(--green); border: 1px solid #cdeee0; }
        .badge-mid { background: #fff7e8; color: var(--amber); border: 1px solid #f2dfb7; }
        .badge-dirty { background: #fff1f1; color: var(--red); border: 1px solid #ffd1d1; }

        .badge-pump-on { background: #fff7e8; color: var(--amber); border: 1px solid #f2dfb7; }
        .badge-pump-off { background: #f0f4f6; color: var(--muted); border: 1px solid var(--line); }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .pagination a, .pagination span {
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid var(--line);
            text-decoration: none;
            color: var(--text);
            font-weight: 600;
            background: var(--surface);
        }

        .pagination .active {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        .pagination .disabled {
            color: var(--muted);
            background: #f5f8f9;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-area">
            <div>
                <a href="{{ route('dashboard') }}" class="btn">&larr; Kembali ke Dashboard</a>
            </div>
            <h1>Riwayat Data Sensor</h1>
        </div>

        <div class="panel">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Jarak Sensor</th>
                            <th>Kapasitas Air</th>
                            <th>Persen Air</th>
                            <th>Tegangan Turbidity</th>
                            <th>Status Kejernihan</th>
                            <th>Kec. Aliran</th>
                            <th>Total Volume</th>
                            <th>Status Pompa</th>
                            <th>Pompa 1</th>
                            <th>Pompa 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $row)
                            <tr>
                                <td><strong>{{ $row->created_at->format('d M Y - H:i:s') }}</strong></td>
                                <td>{{ $row->proximity }} cm</td>
                                <td>{{ $row->water_level_cm }} cm</td>
                                <td><strong>{{ $row->water_level_percent }} %</strong></td>
                                <td>{{ $row->turbidity_voltage }} V</td>
                                <td>
                                    @if($row->turbidity_voltage >= 2.00)
                                        <span class="badge badge-mid">Tangki Kosong</span>
                                    @elseif($row->turbidity_voltage >= 1.61)
                                        <span class="badge badge-clean">Air Bersih</span>
                                    @else
                                        <span class="badge badge-dirty">Air Keruh</span>
                                    @endif
                                </td>
                                <td>{{ $row->flow_rate }} L/min</td>
                                <td>{{ $row->total_litres }} L</td>
                                <td>
                                    @if(strtolower($row->pump_status) == 'aktif')
                                        <span class="badge badge-pump-on">Aktif</span>
                                    @else
                                        <span class="badge badge-pump-off">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    @if(strtolower($row->pompa1_status) == 'aktif')
                                        <span class="badge badge-pump-on">Aktif</span>
                                    @else
                                        <span class="badge badge-pump-off">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    @if(strtolower($row->pompa2_status) == 'aktif')
                                        <span class="badge badge-pump-on">Aktif</span>
                                    @else
                                        <span class="badge badge-pump-off">Non-Aktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" style="text-align: center; color: var(--muted); padding: 40px;">Belum ada data sensor yang terekam.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Laravel Pagination Links -->
            <div class="pagination-container">
                {{ $history->links() }}
            </div>
        </div>
    </div>
</body>
</html>

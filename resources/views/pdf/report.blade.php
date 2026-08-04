<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Work Order</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #111827; }
        .header p { margin: 5px 0 0; color: #6b7280; }

        .summary-box { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .summary-box td { text-align: center; padding: 15px; border: 1px solid #e2e8f0; background: #f8fafc; width: 20%; }
        .summary-box h3 { margin: 0; font-size: 11px; color: #64748b; text-transform: uppercase; }
        .summary-box .val { font-size: 22px; font-weight: bold; margin-top: 5px; color: #0f172a; }

        .insight-box { width: 100%; margin-bottom: 26px; border-collapse: collapse; }
        .insight-box td { padding: 12px 15px; border: 1px solid #e2e8f0; width: 50%; }
        .insight-box .label { font-size: 11px; color: #64748b; text-transform: uppercase; margin: 0; }
        .insight-box .value { font-size: 18px; font-weight: bold; color: #0f172a; margin-top: 4px; }

        h3.section-title { color: #1e293b; border-left: 4px solid #2563eb; padding-left: 8px; margin: 22px 0 8px; font-size: 15px; }

        .table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .table th, .table td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; font-size: 13px; }
        .table th { background-color: #f1f5f9; color: #334155; }
        .table td.num { text-align: right; font-weight: bold; }

        .footer { margin-top: 30px; padding-top: 8px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Analisis Work Order</h1>
        <p>Harris Hotel Seminyak</p>
        <p style="font-size: 12px;">
            Periode:
            {{ !empty($filters['from_date']) ? date('d M Y', strtotime($filters['from_date'])) : 'Awal' }}
            s/d
            {{ !empty($filters['to_date']) ? date('d M Y', strtotime($filters['to_date'])) : 'Sekarang' }}
            @if(!empty($staffFilterName))
                &nbsp;|&nbsp; Teknisi: {{ $staffFilterName }}
            @endif
        </p>
    </div>

    <table class="summary-box">
        <tr>
            <td><h3>Total</h3><div class="val">{{ $totalOrders }}</div></td>
            <td><h3>Pending</h3><div class="val">{{ $pendingOrders }}</div></td>
            <td><h3>On Progress</h3><div class="val">{{ $onProgressOrders }}</div></td>
            <td><h3>Completed</h3><div class="val">{{ $completedOrders }}</div></td>
            <td><h3>Tingkat Selesai</h3><div class="val">{{ $completionRate }}%</div></td>
        </tr>
    </table>

    <table class="insight-box">
        <tr>
            <td>
                <p class="label">Rata-rata Waktu Penyelesaian</p>
                <p class="value">{{ $avgResolutionTime }}</p>
            </td>
            <td>
                <p class="label">Lokasi Paling Sering Bermasalah</p>
                <p class="value">{{ $locationStats->keys()->first() ?? '-' }}</p>
            </td>
        </tr>
    </table>

    <h3 class="section-title">Berdasarkan Departemen</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Departemen</th>
                <th style="width: 100px; text-align: right;">Jumlah WO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departmentStats as $dept => $count)
            <tr>
                <td>{{ $dept }}</td>
                <td class="num">{{ $count }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" style="text-align: center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="section-title">Berdasarkan Jenis Masalah</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Jenis Masalah</th>
                <th style="width: 100px; text-align: right;">Jumlah WO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($issueStats as $issue => $count)
            <tr>
                <td>{{ $issue }}</td>
                <td class="num">{{ $count }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" style="text-align: center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="section-title">Kinerja Staff / Teknisi</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th style="width: 90px; text-align: right;">Ditangani</th>
                <th style="width: 90px; text-align: right;">Selesai</th>
                <th style="width: 100px; text-align: right;">% Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffStats as $s)
            <tr>
                <td>{{ $s['name'] }}</td>
                <td class="num">{{ $s['total'] }}</td>
                <td class="num">{{ $s['completed'] }}</td>
                <td class="num">{{ $s['total'] > 0 ? round(($s['completed'] / $s['total']) * 100) : 0 }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="section-title">Top 5 Lokasi Paling Sering Bermasalah</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Lokasi</th>
                <th style="width: 100px; text-align: right;">Jumlah WO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($locationStats as $location => $count)
            <tr>
                <td>{{ $location }}</td>
                <td class="num">{{ $count }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" style="text-align: center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ $generatedAt }} &middot; Sistem Work Order Harris Hotel Seminyak
    </div>

</body>
</html>
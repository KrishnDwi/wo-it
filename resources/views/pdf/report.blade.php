<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Work Order</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #111827; }
        .header p { margin: 5px 0 0; color: #6b7280; }
        
        .summary-box { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .summary-box td { text-align: center; padding: 15px; border: 1px solid #e2e8f0; background: #f8fafc; width: 25%; }
        .summary-box h3 { margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; }
        .summary-box .val { font-size: 24px; font-weight: bold; margin-top: 5px; color: #0f172a; }

        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th, .table td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
        .table th { background-color: #f1f5f9; color: #334155; }
        .table td.num { text-align: right; font-weight: bold; }
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
        </p>
    </div>

    <table class="summary-box">
        <tr>
            <td><h3>Total</h3><div class="val">{{ $totalOrders }}</div></td>
            <td><h3>Pending</h3><div class="val">{{ $pendingOrders }}</div></td>
            <td><h3>On Progress</h3><div class="val">{{ $onProgressOrders }}</div></td>
            <td><h3>Completed</h3><div class="val">{{ $completedOrders }}</div></td>
        </tr>
    </table>

    <h3>Berdasarkan Departemen</h3>
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

    <h3>Berdasarkan Jenis Masalah</h3>
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

</body>
</html>
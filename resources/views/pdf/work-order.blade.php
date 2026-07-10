<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Work Order {{ $order->wo_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #1e293b; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; margin: 0; color: #2563eb; }
        .header p { margin: 4px 0 0; font-size: 12px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td { padding: 8px 10px; border: 1px solid #e2e8f0; }
        td:first-child { font-weight: bold; background: #f8fafc; width: 35%; }
        .badge-pending { color: #92400e; background: #fef3c7; padding: 2px 8px; border-radius: 4px; }
        .badge-progress { color: #1e40af; background: #dbeafe; padding: 2px 8px; border-radius: 4px; }
        .badge-completed { color: #065f46; background: #d1fae5; padding: 2px 8px; border-radius: 4px; }
        .section-title { font-size: 13px; font-weight: bold; color: #2563eb; margin: 18px 0 6px; }
        .footer { margin-top: 40px; font-size: 11px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>WORK ORDER #{{ $order->wo_number }}</h1>
        <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    <p class="section-title">Informasi Work Order</p>
    <table>
        <tr><td>Nomor WO</td><td>{{ $order->wo_number }}</td></tr>
        <tr><td>Departemen</td><td>{{ $order->department }}</td></tr>
        <tr><td>Jenis Masalah</td><td>{{ $order->issue_type }}</td></tr>
        <tr><td>Lokasi</td><td>{{ $order->location ?: '-' }}</td></tr>
        <tr>
            <td>Status</td>
            <td>
                @if($order->status === 'Pending')
                    <span class="badge-pending">Pending</span>
                @elseif($order->status === 'On Progress')
                    <span class="badge-progress">On Progress</span>
                @else
                    <span class="badge-completed">Completed</span>
                @endif
            </td>
        </tr>
        <tr><td>Waktu Dilaporkan</td><td>{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</td></tr>
        @if($order->completed_at)
        <tr><td>Waktu Selesai</td><td>{{ date('d/m/Y H:i', strtotime($order->completed_at)) }}</td></tr>
        @endif
    </table>

    <p class="section-title">Deskripsi Laporan</p>
    <table>
        <tr><td colspan="2">{{ $order->description ?: 'Tidak ada deskripsi.' }}</td></tr>
    </table>

    @if($order->status === 'Completed' && $order->resolution_note)
    <p class="section-title">Catatan Penyelesaian</p>
    <table>
        <tr><td colspan="2">{{ $order->resolution_note }}</td></tr>
    </table>
    @endif

    <div class="footer">Dokumen ini digenerate otomatis oleh sistem Work Order.</div>
</body>
</html>
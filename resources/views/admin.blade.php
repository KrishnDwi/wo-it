<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel | Work Order</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        /* Tombol topbar responsive */
        .topbar-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .topbar-actions a {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.65rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        /* Mobile: tabel urgent jadi card stack */
        @media (max-width: 768px) {
            .topbar-actions { width: 100%; }
            .topbar-actions a { flex: 1 1 auto; justify-content: center; font-size: 0.85rem; }

            .urgent-table thead { display: none; }
            .urgent-table,
            .urgent-table tbody,
            .urgent-table tr,
            .urgent-table td { display: block; width: 100%; }
            .urgent-table tr {
                background: #fff5f5;
                border-radius: 0.75rem;
                margin-bottom: 0.75rem;
                padding: 1rem;
                border: 1px solid #fecaca;
            }
            .urgent-table td {
                padding: 0.3rem 0;
                border: none;
                font-size: 0.9rem;
                display: flex;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .urgent-table td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #9ca3af;
                font-size: 0.75rem;
                text-transform: uppercase;
                min-width: 90px;
                flex-shrink: 0;
                padding-top: 2px;
            }
        }
    </style>
</head>
<body>
    <div class="layout">
        @include('partials.sidebar')
        <main class="content">
            <div class="topbar">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p>Overview of current operations and recent activity.</p>
                </div>
                <div class="topbar-actions">
                    <a href="/admin/report/pdf?from_date={{ date('Y-m-d') }}&to_date={{ date('Y-m-d') }}"
                       style="background: #10b981; color: white;">
                       Download Laporan Hari Ini
                    </a>
                    <a href="/add"
                       style="background: #2563eb; color: white;">
                       Buat WO Baru
                    </a>
                </div>
            </div>

            @if(session('status'))
                <div class="message">{{ session('status') }}</div>
            @endif

            <section class="grid-cards">
                <article class="card">
                    <h2>Total Work Orders</h2>
                    <div class="value">{{ $workOrders->count() }}</div>
                    <small>Jumlah semua work order</small>
                </article>
                <article class="card">
                    <h2>Pending</h2>
                    <div class="value">{{ $workOrders->where('status', 'Pending')->count() }}</div>
                    <small>Work order with status Pending</small>
                </article>
                <article class="card">
                    <h2>On Progress</h2>
                    <div class="value">{{ $workOrders->where('status', 'On Progress')->count() }}</div>
                    <small>Work order that are in progress</small>
                </article>
                <article class="card">
                    <h2>Completed</h2>
                    <div class="value">{{ $workOrders->where('status', 'Completed')->count() }}</div>
                    <small>Work order with status Completed</small>
                </article>
            </section>

            @if($urgentOrders->count() > 0)
            <section class="card" style="border-left: 4px solid #ef4444; background: #fef2f2;">
                <h2 style="color: #b91c1c; margin-bottom: 1rem;">Work Order Baru</h2>
                <div class="table-wrapper" style="margin-bottom: 0; box-shadow: none;">
                    <table class="table urgent-table" style="background: transparent; box-shadow: none;">
                        <thead>
                            <tr>
                                <th>Work Order Number</th>
                                <th>Department</th>
                                <th>Issue Type</th>
                                <th>Wait Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($urgentOrders as $urgent)
                            <tr>
                                <td data-label="Work Order Number"><strong>{{ $urgent->wo_number }}</strong></td>
                                <td data-label="Department">{{ $urgent->department }}</td>
                                <td data-label="Issue Type">{{ $urgent->issue_type }}</td>
                                <td data-label="Wait Time" style="color: #ef4444; font-weight: 600;">
                                    {{ \Carbon\Carbon::parse($urgent->created_at)->diffForHumans() }}
                                </td>
                                <td data-label="Actions">
                                    <a href="/admin/order/{{ $urgent->id }}" style="color: #2563eb; font-weight: 700; font-size: 0.9rem;">
                                        View Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            @endif

            @include('partials.footer')
        </main>
    </div>
</body>
</html>
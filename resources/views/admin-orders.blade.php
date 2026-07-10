<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Work Orders | Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        /* Mobile: tabel tampil sebagai card stack */
        @media (max-width: 768px) {
            .table thead { display: none; }
            .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
            .table tr {
                background: white;
                border-radius: 0.75rem;
                margin-bottom: 0.75rem;
                padding: 1rem;
                box-shadow: 0 2px 8px rgba(15,23,42,0.07);
                border: 1px solid #e5e7eb;
            }
            .table td {
                padding: 0.3rem 0;
                border: none;
                font-size: 0.9rem;
                display: flex;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .table td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #6b7280;
                font-size: 0.75rem;
                text-transform: uppercase;
                min-width: 80px;
                flex-shrink: 0;
                padding-top: 2px;
            }
            .table-wrapper { box-shadow: none; background: transparent; }
            .table { box-shadow: none; background: transparent; }
            .clickable-row { cursor: pointer; }
            .clickable-row:hover { background: #f8fafc !important; }
        }
    </style>
</head>
<body>
    <div class="layout">
        @include('partials.sidebar')
        <main class="content">
            <div class="topbar">
                <div>
                    <h1>Work Orders</h1>
                    <p>Filter and browse all work orders from the admin panel.</p>
                </div>
            </div>

            <section class="filter-panel">
                <form method="GET" action="/admin/orders">
                    <div class="filter-grid">
                        <div>
                            <label for="search">Search</label>
                            <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}" placeholder="WO Number, Department, Type, Description">
                        </div>
                        <div>
                            <label for="department">Department</label>
                            <select id="department" name="department">
                                <option value="">All Departments</option>
                                @foreach(["FB Kitchen","Housekeeping","Front Office","DT","FB Service","P&C","Security","Sales","Acct","A&G"] as $department)
                                    <option value="{{ $department }}" {{ (isset($filters['department']) && $filters['department'] === $department) ? 'selected' : '' }}>{{ $department }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="issue_type">Issue Type</label>
                            <select id="issue_type" name="issue_type">
                                <option value="">All Types</option>
                                @foreach(["ELECTRICAL","MECHANICAL","PLUMBING","HVAC","BUILDING","FURNITURE","AV","SAFETY","OTHER"] as $type)
                                    <option value="{{ $type }}" {{ (isset($filters['issue_type']) && $filters['issue_type'] === $type) ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ (isset($filters['status']) && $filters['status'] === 'Pending') ? 'selected' : '' }}>Pending</option>
                                <option value="On Progress" {{ (isset($filters['status']) && $filters['status'] === 'On Progress') ? 'selected' : '' }}>On Progress</option>
                                <option value="Completed" {{ (isset($filters['status']) && $filters['status'] === 'Completed') ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div>
                            <label for="from_date">From Date</label>
                            <input id="from_date" name="from_date" type="date" value="{{ $filters['from_date'] ?? '' }}">
                        </div>
                        <div>
                            <label for="to_date">To Date</label>
                            <input id="to_date" name="to_date" type="date" value="{{ $filters['to_date'] ?? '' }}">
                        </div>
                        <div class="filter-actions">
                            <button type="submit">Apply</button>
                            <a href="/admin/orders">Reset</a>
                            {{-- <a href="{{ url('/admin/orders/excel') }}?{{ request()->getQueryString() }}" 
                            style="background: #3b82f6; color: white; padding: 0.85rem 1rem; border-radius: 0.75rem; font-weight: 700; margin-left: auto;">
                            📊 Download Excel
                            </a> --}}
                        </div>
                    </div>
                </form>
            </section>

            <section>
                <h2 style="margin: 0 0 1rem; font-size: 1.125rem; font-weight: 700;">Work Orders List</h2>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>WO Number</th>
                                <th>Department</th>
                                <th>Issue Type</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workOrders as $order)
                                <tr class="clickable-row" data-href="/admin/order/{{ $order->id }}" onclick="navigateToDetail(event, '/admin/order/{{ $order->id }}')">
                                    <td data-label="Nomor WO"><strong>{{ $order->wo_number }}</strong></td>
                                    <td data-label="Departemen">{{ $order->department }}</td>
                                    <td data-label="Jenis">{{ $order->issue_type }}</td>
                                    <td data-label="Lokasi">{{ $order->location ?? '-' }}</td>
                                    <td data-label="Status">
                                        @if($order->status === 'Pending')
                                            <span class="status pending">{{ $order->status }}</span>
                                        @elseif($order->status === 'On Progress')
                                            <span class="status open">{{ $order->status }}</span>
                                        @else
                                            <span class="status completed">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td data-label="Created">{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</td>
                                    <td data-label="Actions">
                                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            {{-- <a href="/admin/order/{{ $order->id }}/edit" style="background: #f59e0b; color: white; padding: 0.4rem 0.8rem; border-radius: 0.4rem; text-decoration: none; font-size: 0.85rem; font-weight: 600;">Edit</a> --}}
                                            <button onclick="deleteOrder({{ $order->id }})" style="background: #ef4444; color: white; padding: 0.4rem 0.8rem; border-radius: 0.4rem; border: none; font-size: 0.85rem; font-weight: 600; cursor: pointer;">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center; padding:2rem; color:#6b7280;">No work orders found for this filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="pagination">
                        {{ $workOrders->appends(request()->query())->links() }}
                    </div>
                </div>
            </section>

            @include('partials.footer')
        </main>
    </div>

    <script>
        function navigateToDetail(event, url) {
            // Jangan navigasi jika yang diklik adalah kolom Aksi atau anak-anaknya
            const aksiColumn = event.currentTarget.querySelector('[data-label="Aksi"]');
            if (aksiColumn && aksiColumn.contains(event.target)) {
                return;
            }
            window.location.href = url;
        }

        function deleteOrder(orderId) {
            if (confirm('Are you sure you want to delete this work order? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/order/' + orderId + '/delete';
                form.innerHTML = '@csrf';
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

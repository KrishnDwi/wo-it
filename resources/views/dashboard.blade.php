<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | Work Order</title>
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
</head>
<body>
    <nav>
        <div class="container">
            <div class="brand">Harris Hotel Seminyak</div>
            <ul class="nav-links">
                <li><a href="/" class="active">Dashboard</a></li>
                <li><a href="/add">Create Work Order</a></li>
            </ul>
            <button class="hamburger">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <div class="page">
        <header class="header">
            <div class="hero">
                <h1>Work Order Dashboard</h1>
                <p>Manage and monitor all work orders in one place.</p>
            </div>
            <a href="/add" class="create-btn">+ Create New Work Order</a>
        </header>

        @if(session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <strong>{{ $workOrders->count() }}</strong>
                <span>Total Work Order</span>
            </div>
            <div class="stat-card">
                <strong>{{ $workOrders->where('status', 'Pending')->count() }}</strong>
                <span>Pending</span>
            </div>
            <div class="stat-card">
                <strong>{{ $workOrders->where('status', 'On Progress')->count() }}</strong>
                <span>On Progress</span>
            </div>
            <div class="stat-card">
                <strong>{{ $workOrders->where('status', 'Completed')->count() }}</strong>
                <span>Completed</span>
            </div>
        </div>

        <div class="panel">
            <h2>Daftar Work Order</h2>
            @if($workOrders->count() > 0)
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Nomor WO</th>
                                <th>Departemen</th>
                                <th>Jenis Masalah</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workOrders as $order)
                                <tr class="clickable-row"
                                    data-wo-number="{{ $order->wo_number }}"
                                    data-department="{{ $order->department }}"
                                    data-issue-type="{{ $order->issue_type }}"
                                    data-location="{{ $order->location ?? 'Tidak ada lokasi' }}"
                                    data-status="{{ $order->status }}"
                                    data-created-at="{{ date('d/m/Y H:i', strtotime($order->created_at)) }}"
                                    data-description="{{ $order->description ?? 'Tidak ada deskripsi.' }}">
                                    <td><strong>{{ $order->wo_number }}</strong></td>
                                    <td>{{ $order->department }}</td>
                                    <td>{{ $order->issue_type }}</td>
                                    <td>{{ $order->location ?? '-' }}</td>
                                    <td>
                                        @if($order->status == 'Pending')
                                            <span class="tag pending">{{ $order->status }}</span>
                                        @elseif($order->status == 'On Progress')
                                            <span class="tag progress">{{ $order->status }}</span>
                                        @else
                                            <span class="tag completed">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty">
                    <p>Belum ada work order</p>
                    <a href="/add" class="create-btn">Buat Work Order Pertama</a>
                </div>
            @endif
        </div>
    </div>

    <div class="modal-backdrop" id="order-detail-modal">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div class="modal-header">
                <h3 id="modal-title">Detail Work Order</h3>
                <button class="modal-close" id="modal-close" aria-label="Tutup detail">×</button>
            </div>
            <div class="modal-body" id="modal-body">
                <p><strong>Nomor WO</strong> <span id="modal-wo-number"></span></p>
                <p><strong>Departemen</strong> <span id="modal-department"></span></p>
                <p><strong>Jenis Masalah</strong> <span id="modal-issue-type"></span></p>
                <p><strong>Lokasi</strong> <span id="modal-location"></span></p>
                <p><strong>Status</strong> <span id="modal-status"></span></p>
                <p><strong>Tanggal Dibuat</strong> <span id="modal-created-at"></span></p>
                <p><strong>Deskripsi</strong></p>
                <p id="modal-description"></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.querySelector('.hamburger');
            const navLinks = document.querySelector('.nav-links');
            const modal = document.getElementById('order-detail-modal');
            const modalClose = document.getElementById('modal-close');
            const fields = {
                woNumber: document.getElementById('modal-wo-number'),
                department: document.getElementById('modal-department'),
                issueType: document.getElementById('modal-issue-type'),
                location: document.getElementById('modal-location'),
                status: document.getElementById('modal-status'),
                createdAt: document.getElementById('modal-created-at'),
                description: document.getElementById('modal-description'),
            };

            hamburger?.addEventListener('click', () => {
                hamburger.classList.toggle('active');
                navLinks.classList.toggle('active');
            });

            document.querySelectorAll('.nav-links a').forEach(a => {
                a.addEventListener('click', () => {
                    hamburger.classList.remove('active');
                    navLinks.classList.remove('active');
                });
            });

            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', () => {
                    fields.woNumber.textContent = row.dataset.woNumber;
                    fields.department.textContent = row.dataset.department;
                    fields.issueType.textContent = row.dataset.issueType;
                    fields.location.textContent = row.dataset.location;
                    fields.status.textContent = row.dataset.status;
                    fields.createdAt.textContent = row.dataset.createdAt;
                    fields.description.textContent = row.dataset.description;
                    modal.classList.add('active');
                });
            });

            modalClose?.addEventListener('click', () => modal.classList.remove('active'));
            modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('active'); });
            document.addEventListener('keydown', e => { if (e.key === 'Escape') modal.classList.remove('active'); });
        });
    </script>
</body>
</html>

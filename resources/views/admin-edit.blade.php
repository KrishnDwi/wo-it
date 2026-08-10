<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Work Order | Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div class="layout">
        @include('partials.sidebar')
        <main class="content">
            <div class="topbar">
                <div>
                    <h1>Edit Work Order</h1>
                    <p>Change work order information and details.</p>
                </div>
            </div>
            
            <div style="display: flex; gap: 0.75rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
                <a href="/admin/orders" class="back-link">← Back</a>
                <a href="/admin/order/{{ $order->id }}" style="background: #6b7280; color: white; padding: 0.6rem 1.2rem; border-radius: 0.6rem; text-decoration: none; font-weight: 700;">👁️ View Detail</a>
            </div>

            @if($errors->any())
                <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1rem; border-left: 4px solid #dc2626;">
                    <strong>❌ Error:</strong>
                    <ul style="margin: 0.5rem 0 0 1rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1rem; border-left: 4px solid #22c55e;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="/admin/order/{{ $order->id }}/update" style="display: grid; gap: 1.5rem;">
                @csrf

                <div class="card">
                    <h2 style="margin-top: 0;">Work Order {{ $order->wo_number }}</h2>
                    <p style="color: #64748b; margin-bottom: 1.5rem;">Created: {{ date('d/m/Y H:i', strtotime($order->created_at)) }}</p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <label for="department" style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Departemen</label>
                            <select name="department" id="department" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.95rem;">
                                @foreach($departmentOptions as $dept)
                                    <option value="{{ $dept }}" {{ $order->department === $dept ? 'selected' : '' }}>
                                        {{ $dept }}@if(!$masterDepartments->contains($dept)) (sudah tidak ada di Master Data) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="issue_type" style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Jenis Masalah</label>
                            <select name="issue_type" id="issue_type" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.95rem;">
                                @foreach($issueTypeOptions as $type)
                                    <option value="{{ $type }}" {{ $order->issue_type === $type ? 'selected' : '' }}>
                                        {{ $type }}@if(!$masterIssueTypes->contains($type)) (sudah tidak ada di Master Data) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="location" style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Lokasi</label>
                            <input type="text" name="location" id="location" value="{{ $order->location }}" placeholder="Lokasi kendala" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.95rem;">
                        </div>

                        <div>
                            <label style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Status</label>
                            <div style="padding: 0.75rem; background: #f1f5f9; border-radius: 0.5rem; font-weight: 700; color: #475569;">
                                @if($order->status === 'Pending')
                                    ⏳ Pending
                                @elseif($order->status === 'On Progress')
                                    🔧 On Progress
                                @else
                                    ✅ Completed
                                @endif
                                <span style="font-weight: 400; font-size: 0.85rem; display: block; margin-top: 0.25rem;">Ubah status lewat halaman detail work order.</span>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <label for="description" style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Deskripsi Laporan</label>
                        <textarea name="description" id="description" rows="4" placeholder="Deskripsi detail kendala..." style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.95rem; font-family: inherit;">{{ $order->description }}</textarea>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button type="submit" style="background: #10b981; color: white; border: none; padding: 0.85rem 1.5rem; border-radius: 0.75rem; font-weight: 700; cursor: pointer; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);">💾 Simpan Perubahan</button>
                    
                    <a href="/admin/order/{{ $order->id }}" style="background: #6b7280; color: white; padding: 0.85rem 1.5rem; border-radius: 0.75rem; text-decoration: none; font-weight: 700;">↩️ Batal</a>
                    
                    <button type="button" onclick="confirmDelete()" style="background: #ef4444; color: white; border: none; padding: 0.85rem 1.5rem; border-radius: 0.75rem; font-weight: 700; cursor: pointer; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);">🗑️ Hapus</button>
                </div>
            </form>

            @include('partials.footer')
        </main>
    </div>

    <script>
        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus work order ini? Tindakan ini tidak dapat dibatalkan.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/order/{{ $order->id }}/delete';
                form.innerHTML = '@csrf';
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admin WA | Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .option-card { background: white; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1rem; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; }
        .option-info { flex: 1; }
        .option-info h3 { margin: 0 0 0.25rem 0; color: #1f2937; display: flex; align-items: center; gap: 0.5rem; }
        .option-info p { margin: 0; color: #6b7280; font-size: 0.9rem; margin-bottom: 0.25rem; }
        .option-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .btn-edit { padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; cursor: pointer; font-size: 0.85rem; font-weight: 600; background: #f59e0b; color: white; }
        .btn-edit:hover { background: #d97706; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 700; margin-bottom: 0.5rem; color: #334155; }
        .form-group input[type="text"] { width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.95rem; font-family: inherit; box-sizing: border-box; }
        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem; }
        .checkbox-group input[type="checkbox"] { width: 1.25rem; height: 1.25rem; cursor: pointer; }
        .checkbox-group label { margin: 0; cursor: pointer; color: #16a34a; font-weight: bold; }
        .btn-submit { background: #10b981; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-weight: 700; cursor: pointer; }
        .btn-submit:hover { background: #059669; }
        .badge-active { background: #10b981; color: white; padding: 0.1rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: bold; }
        .badge-inactive { background: #9ca3af; color: white; padding: 0.1rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: bold; }
        .modal-content { background: white; max-width: 500px; margin: auto; padding: 2rem; border-radius: 0.75rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-height: 90vh; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="layout">
        @include('partials.sidebar')
        <main class="content">
            <div class="topbar">
                <div>
                    <h1>Manage WhatsApp Admins</h1>
                    <p>Manage the WhatsApp admins who will receive notifications</p>
                </div>
            </div>

            {{-- <a href="/admin/orders" class="back-link">← Back to Orders</a> --}}

            @if(session('success'))
                <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border-left: 4px solid #22c55e;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
                    <h2 style="margin: 0;">List Admin ({{ $users->count() }})</h2>
                    <button onclick="openAddModal()" class="btn-submit" style="padding: 0.6rem 1.2rem; font-size: 0.9rem;">Tambah Admin Baru</button>
                </div>
                
                @if($users->isEmpty())
                    <p style="color: #6b7280; text-align: center; padding: 2rem;">no users found</p>
                @else
                    @foreach($users as $user)
                        <div class="option-card">
                            <div class="option-info">
                                <h3>
                                    {{ $user->name }}
                                    @if($user->is_wa_active)
                                        <span class="badge-active">WA AKTIF</span>
                                    @else
                                        <span class="badge-inactive">OFF</span>
                                    @endif
                                </h3>
                                <p style="font-size: 1.1rem; margin-top: 0.5rem;">Nomor WhatsApp: <strong style="color: #1f2937;">{{ $user->phone_number ?? 'Belum diatur' }}</strong></p>
                            </div>
                            <div class="option-actions" style="display: flex; align-items: center;">
                                <button class="btn-edit" onclick="editUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->phone_number }}', {{ $user->is_wa_active ? 'true' : 'false' }})">Edit Nomor / Status</button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </main>
    </div>

    <div id="addModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; padding: 2rem;">
        <div class="modal-content">
            <h2 style="margin-top: 0;">Tambah Kontak Admin WA</h2>
            <p style="color: #6b7280; margin-top: -10px; margin-bottom: 20px;">Masukkan nama dan nomor WhatsApp admin yang bertugas.</p>
            
            <form method="POST" action="/admin/settings/users">
                @csrf
                <div class="form-group">
                    <label for="name">Nama Admin</label>
                    <input type="text" name="name" id="name" placeholder="Contoh: Admin Budi" required value="{{ old('name') }}">
                    @error('name') <small style="color: #ef4444;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label for="phone_number">Nomor WhatsApp</label>
                    <input type="text" name="phone_number" id="phone_number" placeholder="Contoh: 628563978602 (Gunakan 62, tanpa +)" value="{{ old('phone_number') }}">
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" name="is_wa_active" id="is_wa_active" value="1">
                    <label for="is_wa_active">Jadikan Admin ini penerima utama WA sekarang</label>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" onclick="closeAddModal()" style="padding: 0.75rem 1.5rem; border: 1px solid #cbd5e1; background: white; border-radius: 0.5rem; cursor: pointer; font-weight: bold; color: #475569;">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Kontak</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; padding: 2rem;">
        <div class="modal-content">
            <h2 style="margin-top: 0;">Edit Nomor & Status WA</h2>
            <p id="editAdminName" style="color: #1f2937; margin-top: -10px; margin-bottom: 20px; font-weight: bold; font-size: 1.1rem;"></p>
            
            <form id="editForm" method="POST">
                @csrf
                <div class="form-group">
                    <label for="editPhone">Nomor WhatsApp Baru</label>
                    <input type="text" name="phone_number" id="editPhone" placeholder="Contoh: 628563978602">
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" name="is_wa_active" id="editWaActive" value="1">
                    <label for="editWaActive">Aktifkan Notifikasi WA untuk Admin ini</label>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" onclick="closeEditModal()" style="padding: 0.75rem 1.5rem; border: 1px solid #cbd5e1; background: white; border-radius: 0.5rem; cursor: pointer; font-weight: bold; color: #475569;">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';
            document.getElementById('addModal').style.alignItems = 'center';
        }
        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function editUser(id, name, phone, isWaActive) {
            document.getElementById('editAdminName').innerText = "Nama: " + name;
            document.getElementById('editPhone').value = phone || '';
            document.getElementById('editWaActive').checked = isWaActive;
            document.getElementById('editForm').action = '/admin/settings/users/' + id + '/update';
            
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('editModal').style.alignItems = 'center';
        }
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Tutup modal jika overlay diklik
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddModal();
        });
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });

        // Buka kembali modal tambah jika ada error (nama kosong)
        @if($errors->has('name'))
            window.onload = function() {
                openAddModal();
            }
        @endif
    </script>
</body>
</html>
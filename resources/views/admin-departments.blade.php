<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments | Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .option-card { background: white; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1rem; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; }
        .option-info { flex: 1; }
        .option-info h3 { margin: 0 0 0.25rem 0; color: #1f2937; display: flex; align-items: center; gap: 0.5rem; }
        .option-info p { margin: 0; color: #6b7280; font-size: 0.9rem; margin-bottom: 0.25rem; }
        .option-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .btn-edit { padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; cursor: pointer; font-size: 0.85rem; font-weight: 600; background: #f59e0b; color: white; }
        .btn-edit:hover { background: #d97706; }
        .btn-delete { padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; cursor: pointer; font-size: 0.85rem; font-weight: 600; background: #ef4444; color: white; }
        .btn-delete:hover { background: #dc2626; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 700; margin-bottom: 0.5rem; color: #334155; }
        .form-group input[type="text"], .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.95rem; font-family: inherit; box-sizing: border-box; }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .btn-submit { background: #10b981; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-weight: 700; cursor: pointer; }
        .btn-submit:hover { background: #059669; }
        .modal-content { background: white; max-width: 500px; margin: auto; padding: 2rem; border-radius: 0.75rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-height: 90vh; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="layout">
        @include('partials.sidebar')
        <main class="content">
            <div class="topbar">
                <div>
                    <h1>Manage Departments</h1>
                    <p>Manage the departments that will be used in the system.</p>
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
                    <h2 style="margin: 0;">List Departments ({{ $departments->count() }})</h2>
                    <button onclick="openAddModal()" class="btn-submit" style="padding: 0.6rem 1.2rem; font-size: 0.9rem;">Add Department</button>
                </div>
                
                @if($departments->isEmpty())
                    <p style="color: #6b7280; text-align: center; padding: 2rem;">No departments found. Add a new one.</p>
                @else
                    @foreach($departments as $dept)
                        <div class="option-card">
                            <div class="option-info">
                                <h3>{{ $dept->name }}</h3>
                                @if($dept->description)
                                    <p>{{ $dept->description }}</p>
                                @endif
                            </div>
                            <div class="option-actions">
                                <button class="btn-edit" onclick="editDepartment({{ $dept->id }}, '{{ addslashes($dept->name) }}', '{{ addslashes($dept->description) }}')">Edit</button>
                                <form method="POST" action="/admin/settings/departments/{{ $dept->id }}/delete" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this department?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </main>
    </div>

    <div id="addModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; padding: 2rem;">
        <div class="modal-content">
            <h2 style="margin-top: 0;">Add Department</h2>
            
            <form method="POST" action="/admin/settings/departments">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" placeholder="Example: FB Kitchen, Engineering" required value="{{ old('name') }}">
                    @error('name') <small style="color: #ef4444;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <textarea name="description" id="description" placeholder="Brief explanation about this department...">{{ old('description') }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" onclick="closeAddModal()" style="padding: 0.75rem 1.5rem; border: 1px solid #cbd5e1; background: white; border-radius: 0.5rem; cursor: pointer; font-weight: bold; color: #475569;">Cancel</button>
                    <button type="submit" class="btn-submit">Save Department</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; padding: 2rem;">
        <div class="modal-content">
            <h2 style="margin-top: 0;">Edit Department</h2>
            
            <form id="editForm" method="POST">
                @csrf
                <div class="form-group">
                    <label for="editName">Name</label>
                    <input type="text" name="name" id="editName" required>
                </div>
                
                <div class="form-group">
                    <label for="editDescription">Description (Optional)</label>
                    <textarea name="description" id="editDescription"></textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" onclick="closeEditModal()" style="padding: 0.75rem 1.5rem; border: 1px solid #cbd5e1; background: white; border-radius: 0.5rem; cursor: pointer; font-weight: bold; color: #475569;">Cancel</button>
                    <button type="submit" class="btn-submit">Save Changes</button>
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

        function editDepartment(id, name, description) {
            document.getElementById('editName').value = name;
            document.getElementById('editDescription').value = description || '';
            document.getElementById('editForm').action = '/admin/settings/departments/' + id + '/update';
            
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('editModal').style.alignItems = 'center';
        }
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        document.getElementById('addModal').addEventListener('click', function(e) { if (e.target === this) closeAddModal(); });
        document.getElementById('editModal').addEventListener('click', function(e) { if (e.target === this) closeEditModal(); });

        @if($errors->has('name'))
            window.onload = function() { openAddModal(); }
        @endif
    </script>
</body>
</html>
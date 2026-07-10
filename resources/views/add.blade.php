<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Work Order</title>
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
</head>
<body>
    <nav>
        <div class="container">
            <div class="brand">Harris Hotel Seminyak</div>
            <ul class="nav-links">
                <li><a href="/">Dashboard</a></li>
                <li><a href="/add" class="active">Create Work Order</a></li>
            </ul>
            <button class="hamburger">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <div class="page-narrow">
        <div class="card">
            <h1>Create Work Order Form</h1>
            <p class="lead">Fill in the new work order data below to create a new service request.</p>

            {{-- <div class="info-box">
                <strong>ℹ Work order number will be generated automatically</strong> with format YYYYMM### (example: 202605001 for first order in May 2026) and will reset at the beginning of each month.
            </div> --}}

            @if ($errors->any())
                <div class="error-list">
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/add" method="POST" enctype="multipart/form-data" target="_blank" onsubmit="setTimeout(function(){ window.location.href = '/'; }, 300);">
                @csrf
                <div class="grid">
                    <div>
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->name }}" {{ old('department') == $dept->name ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="issue_type">Issue Type</label>
                        <select id="issue_type" name="issue_type" required>
                            <option value="">Select Issue Type</option>
                            @foreach($issueTypes as $type)
                                <option value="{{ $type->name }}" {{ old('issue_type') == $type->name ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="location">Location</label>
                        <input id="location" name="location" type="text" placeholder="Example: Room 101, Floor 3" value="{{ old('location') }}">
                    </div>
                    <div class="grid-full">
                        <label for="description">Work Order Description</label>
                        <textarea id="description" name="description" placeholder="Explain the work requirement or issue...">{{ old('description') }}</textarea>
                    </div>
                    <div class="grid-full">
                        <label for="image">Attach Photo (Optional)</label>
                        <input type="file" name="image" id="image" accept="image/* capture=environment">
                        <small style="color: #6b7280; display: block; margin-top: 0.35rem;">Max 5MB. Format: JPG, JPEG, PNG.</small>
                    </div>
                </div>
                <div class="actions">
                    <button type="submit">Save Work Order</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.querySelector('.hamburger');
            const navLinks = document.querySelector('.nav-links');

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
        });
    </script>
</body>
</html>

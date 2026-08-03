<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Login | Create Work Order</title>
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
</head>
<body>
    <nav>
        <div class="container">
            <div class="brand">Harris Hotel Seminyak</div>
        </div>
    </nav>

    <div class="page-narrow">
        <div class="card">
            <h1>Department Login</h1>
            <p class="lead">Enter your department and password to submit a new work order.</p>

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

            <form action="/add/login" method="POST">
                @csrf
                <div class="grid">
                    <div>
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->name }}" {{ old('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" placeholder="Department login password" required>
                    </div>
                </div>
                <div class="actions">
                    <button type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

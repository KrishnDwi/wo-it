<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order Detail | Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div class="layout">
        @include('partials.sidebar')
        <main class="content">
            <div class="topbar">
                <div>
                    <h1>Work Order Detail</h1>
                    <p>Complete information and ticket status updates.</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                {{-- <a href="/admin/orders" class="back-link">← Back to List</a> --}}
            
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="/admin/order/{{ $order->id }}/pdf"
                    style="display: inline-flex; align-items: center; gap: 0.4rem;
                            background: #2563eb; color: #fff; text-decoration: none;
                            padding: 0.6rem 1.2rem; border-radius: 0.6rem;
                            font-weight: 700; font-size: 0.9rem;
                            box-shadow: 0 2px 6px rgba(37,99,235,0.25);
                            transition: background 0.2s;">
                        Download PDF
                    </a>
                    {{-- <a href="/admin/order/{{ $order->id }}/edit"
                    style="display: inline-flex; align-items: center; gap: 0.4rem;
                            background: #f59e0b; color: #fff; text-decoration: none;
                            padding: 0.6rem 1.2rem; border-radius: 0.6rem;
                            font-weight: 700; font-size: 0.9rem;
                            box-shadow: 0 2px 6px rgba(245,158,11,0.25);
                            transition: background 0.2s;">
                        ✏️ Edit
                    </a> --}}
                </div>
            </div>
                        @if(session('status'))
                <div class="message">{{ session('status') }}</div>
            @endif

            <div class="card">
                <h2 style="font-size: 1.5rem; color: #2563eb; margin-bottom: 1.5rem;">{{ $order->wo_number }}</h2>
                
                <div class="details-grid">
                    <div>
                        <strong>Department</strong>
                        <p>{{ $order->department }}</p>
                    </div>
                    <div>
                        <strong>Issue Type</strong>
                        <p>{{ $order->issue_type }}</p>
                    </div>
                    <div>
                        <strong>Location</strong>
                        <p>{{ $order->location ?: 'No location' }}</p>
                    </div>
                    <div>
                        <strong>Current Status</strong>
                        <p>
                            @if($order->status === 'Pending')
                                <span class="status pending">{{ $order->status }}</span>
                            @elseif($order->status === 'On Progress')
                                <span class="status open">{{ $order->status }}</span>
                            @else
                                <span class="status completed">{{ $order->status }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <strong>Report Time</strong>
                        <p>{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</p>
                    </div>
                    @if($order->started_at)
                    <div>
                        <strong>Start Time</strong>
                        <p>{{ date('d/m/Y H:i', strtotime($order->started_at)) }}</p>
                    </div>
                    @endif
                    @if($order->completed_at)
                    <div>
                        <strong>Completion Time</strong>
                        <p>{{ date('d/m/Y H:i', strtotime($order->completed_at)) }}</p>
                    </div>
                    @endif
                </div>

                <div style="margin-top: 1.5rem; background: #f8fafc; padding: 1.25rem; border-radius: 0.75rem; border: 1px solid #e2e8f0;">
                    <strong>User Report Description:</strong>
                    <p style="margin-top: 0.5rem; line-height: 1.6;">{{ $order->description ?: 'No additional description.' }}</p>
                </div>

                @if($order->status === 'Completed' && $order->completed_at)
                @php
                    // Gunakan duration_minutes dari database (sudah dihitung dari started_at ke completed_at)
                    if ($order->duration_minutes !== null) {
                        $totalMenit = (int) $order->duration_minutes;
                    } else {
                        // Fallback: hitung dari started_at (atau created_at jika started_at null)
                        $startTime = $order->started_at ?? $order->created_at;
                        $totalMenit = (int) \Carbon\Carbon::parse($startTime)->diffInMinutes($order->completed_at);
                    }

                    if ($totalMenit >= 1440) {
                        $durationDisplay = round($totalMenit / 1440, 1) . ' Hari';
                    } elseif ($totalMenit >= 60) {
                        $durationDisplay = round($totalMenit / 60, 1) . ' Jam';
                    } else {
                        $durationDisplay = $totalMenit . ' Menit';
                    }
                @endphp
                <div style="margin-top: 1.5rem; background: #ecfdf5; padding: 1.25rem; border-radius: 0.75rem; border: 1px solid #10b981;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                        <div style="flex: 1; min-width: 250px;">
                            <strong style="color: #065f46;">Resolution Action (Resolution Note):</strong>
                            <p style="margin-top: 0.5rem; line-height: 1.6; color: #064e3b;">
                                {{ $order->resolution_note ?: 'No notes.' }}
                            </p>
                        </div>
                        <div style="background: white; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #a7f3d0; text-align: right;">
                            <span style="display: block; font-size: 0.75rem; color: #059669; font-weight: bold; text-transform: uppercase;">Total Work Duration</span>
                            <span style="display: block; font-size: 1.1rem; color: #065f46; font-weight: 800; margin-top: 0.25rem;">⏱️ {{ $durationDisplay }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($order->image)
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <strong>Issue Photo Attachment:</strong>
                    <div style="margin-top: 0.75rem;">
                        <a href="{{ asset('storage/' . $order->image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $order->image) }}" alt="Issue Photo WO" style="max-width: 100%; max-height: 350px; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); object-fit: cover;">
                        </a>
                    </div>
                    <small style="color: #64748b; display: block; margin-top: 0.5rem;">*Click image to enlarge</small>
                </div>
                @endif
            </div>

            @if($order->status !== 'Completed')
            <div class="card" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <h2>Follow Up</h2>
                
                @if($order->status === 'Pending')
                    <p style="color: #64748b; margin-bottom: 1.5rem;">This order is <strong>Pending</strong>. Please accept this ticket to start working on it.</p>
                    
                    <form method="POST" action="/admin/order/{{ $order->id }}/update-status">
                        @csrf
                        <input type="hidden" name="status" value="On Progress">
                        
                        <button type="submit" style="background: #f59e0b; color: white; border: none; padding: 0.85rem 1.5rem; border-radius: 0.75rem; font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2); transition: 0.2s;">
                            Accept WO
                        </button>
                    </form>

                @elseif($order->status === 'On Progress')
                    <p style="color: #64748b; margin-bottom: 1.5rem;">This order is <strong>On Progress</strong>. If completed, please fill in the resolution notes and complete the ticket.</p>
                    
                    <form method="POST" action="/admin/order/{{ $order->id }}/update-status">
                        @csrf
                        <input type="hidden" name="status" value="Completed">
                        
                        <div style="margin-bottom: 1.25rem;">
                            <label for="resolution_note" style="display: block; font-weight: 700; margin-bottom: 0.5rem; color: #334155;">Resolution Note (Required)</label>
                            <textarea name="resolution_note" id="resolution_note" rows="4" placeholder="List the repairs or replacements made..." style="width: 100%; padding: 0.85rem; border: 1px solid #cbd5e1; border-radius: 0.75rem; font-family: inherit;" required></textarea>
                        </div>

                        <button type="submit" style="background: #10b981; color: white; border: none; padding: 0.85rem 1.5rem; border-radius: 0.75rem; font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2); transition: 0.2s;">
                            Complete WO
                        </button>
                    </form>
                @endif
            </div>
            @endif

            @include('partials.footer')
        </main>
    </div>
</body>
</html>
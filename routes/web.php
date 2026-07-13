<?php

use App\Models\WorkOrder;
use App\Models\User;
use App\Models\Department;
use App\Models\IssueType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SettingsController;

Route::get('/', function (Request $request) {
    $query = WorkOrder::orderBy('created_at', 'desc');

    if ($request->filled('department')) {
        $query->where('department', $request->input('department'));
    }

    if ($request->filled('issue_type')) {
        $query->where('issue_type', $request->input('issue_type'));
    }

    if ($request->filled('status')) {
        $query->where('status', $request->input('status'));
    }

    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->input('from_date'));
    }

    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->input('to_date'));
    }

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($sub) use ($search) {
            $sub->where('wo_number', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%")
                ->orWhere('issue_type', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    $workOrders = $query->get();

    return view('dashboard', [
        'workOrders' => $workOrders,
        'filters' => $request->only(['department', 'issue_type', 'status', 'search', 'from_date', 'to_date']),
    ]);
});

Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/orders', [AdminController::class, 'orders']);
Route::get('/admin/order/{id}', [AdminController::class, 'show'])->name('admin.detail');
Route::post('/admin/order/{id}/update-status', [AdminController::class, 'updateStatus']);
Route::get('/admin/order/{id}/edit', [AdminController::class, 'edit']);
Route::post('/admin/order/{id}/update', [AdminController::class, 'update']);
Route::post('/admin/order/{id}/delete', [AdminController::class, 'delete']);
Route::get('/admin/report', [AdminController::class, 'report']);
Route::get('/admin/report/pdf', [AdminController::class, 'downloadPdf']);
Route::get('/admin/report/excel', [AdminController::class, 'downloadExcel']);
Route::get('/admin/orders/excel', [AdminController::class, 'downloadOrdersExcel']);
Route::get('/admin/order/{id}/pdf', [AdminController::class, 'downloadWorkOrderPdf']);

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/add', function () {
// Ambil semua data dari database
    $departments = Department::orderBy('name')->get();
    $issueTypes = IssueType::orderBy('name')->get();

    // Kirim data ke view
    return view('add', compact('departments', 'issueTypes'));
});

Route::post('/add', function (Request $request) {
// Gunakan rule 'exists:nama_tabel,nama_kolom' untuk memvalidasi
    $data = $request->validate([
        'department' => 'required|exists:departments,name',
        'issue_type' => 'required|exists:issue_types,name',
        'location' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
    ]);

    // Jika user mengunggah gambar, simpan ke folder 'storage/app/public/work_orders'
    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('work_orders', 'public');
    }

    $workOrder = WorkOrder::create($data);

    \Log::info('WO created', [
        'wo_number' => $workOrder->wo_number,
        'department' => $workOrder->department,
        'issue_type' => $workOrder->issue_type,
        'location' => $workOrder->location,
        'description' => $workOrder->description,
    ]);

    // Cari admin yang status is_wa_active nya true (sedang bertugas)
    $activeAdmin = User::where('is_wa_active', true)->first();

    $chatId = $activeAdmin && $activeAdmin->phone_number
        ? $activeAdmin->phone_number
        : env('TELEGRAM_CHAT_ID');
    $botToken = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');

    $message = "New work order created:\n\nWO Number: {$workOrder->wo_number}\nDepartment: {$workOrder->department}\nLocation: {$workOrder->location}\nIssue Type: {$workOrder->issue_type}\nDescription: {$workOrder->description}\n\nPlease check the details in the dashboard.";

    if ($botToken && $chatId) {
        try {
            $response = Http::withOptions([
                'connect_timeout' => 5,
                'timeout' => 8,
                'verify' => true,
            ])->asForm()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if (! $response->successful()) {
                \Log::warning('Telegram notification failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('Telegram notification failed: ' . $e->getMessage());
        }
    }

    return redirect('/')->with('success', 'Work order created successfully.');
});

// ============ SETTINGS ROUTES ============
Route::get('/admin/settings/departments', [SettingsController::class, 'departmentIndex']);
Route::post('/admin/settings/departments', [SettingsController::class, 'departmentStore']);
Route::post('/admin/settings/departments/{id}/update', [SettingsController::class, 'departmentUpdate']);
Route::post('/admin/settings/departments/{id}/delete', [SettingsController::class, 'departmentDelete']);

Route::get('/admin/settings/issue-types', [SettingsController::class, 'issueTypeIndex']);
Route::post('/admin/settings/issue-types', [SettingsController::class, 'issueTypeStore']);
Route::post('/admin/settings/issue-types/{id}/update', [SettingsController::class, 'issueTypeUpdate']);
Route::post('/admin/settings/issue-types/{id}/delete', [SettingsController::class, 'issueTypeDelete']);


Route::get('/admin/settings/users', [SettingsController::class, 'userIndex']);
Route::post('/admin/settings/users', [SettingsController::class, 'userStore']);
Route::post('/admin/settings/users/{id}/update', [SettingsController::class, 'userUpdate']);
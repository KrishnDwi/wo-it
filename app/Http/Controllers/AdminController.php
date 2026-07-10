<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Department;
use App\Models\IssueType;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    // 1. Admin Dashboard Home Page
    public function index()
    {
        $workOrders = WorkOrder::all();
        
        // Get 5 oldest 'Pending' Work Orders that haven't been worked on yet
        $urgentOrders = WorkOrder::where('status', 'Pending')
                                 ->orderBy('created_at', 'asc')
                                 ->take(5)
                                 ->get();

        return view('admin', compact('workOrders', 'urgentOrders'));
    }

    // 2. Work Orders List Page (+ Filter & Search Features)
    public function orders(Request $request)
    {
        $filters = $request->only(['search', 'department', 'issue_type', 'status', 'from_date', 'to_date']);
        
        $query = WorkOrder::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('wo_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('issue_type')) {
            $query->where('issue_type', $request->issue_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date . ' 23:59:59']);
        }

        $workOrders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin-orders', compact('workOrders', 'filters'));
    }

    // 3. Work Order Detail Page
    public function show($id)
    {
        $order = WorkOrder::findOrFail($id);
        return view('admin-detail', compact('order'));
    }

    // 4. Update Work Order Status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,On Progress,Completed',
            'resolution_note' => 'required_if:status,Completed|nullable|string'
        ]);

        $order = WorkOrder::findOrFail($id);
        $order->status = $request->status;

        if ($request->status === 'On Progress') {
            // Catat waktu mulai pengerjaan, hanya jika belum pernah di-set
            if (!$order->started_at) {
                $order->started_at = \Carbon\Carbon::now();
            }

        } elseif ($request->status === 'Completed') {
            $order->resolution_note = $request->resolution_note;

            if (!$order->completed_at) {
                $order->completed_at = \Carbon\Carbon::now();
            }

            // Hitung durasi dari started_at ke completed_at
            // Fallback ke created_at jika WO langsung di-complete tanpa On Progress
            $startTime = $order->started_at ?? $order->created_at;
            $order->duration_minutes = (int) \Carbon\Carbon::parse($startTime)
                                            ->diffInMinutes($order->completed_at);

        } else {
            // Status kembali ke Pending - reset semua
            $order->started_at = null;
            $order->completed_at = null;
            $order->resolution_note = null;
            $order->duration_minutes = null;
        }

        $order->save();

        return redirect()->back()->with('status', 'Work order status and notes updated successfully!');
    }

    // 5. Report Analysis Page (Web View)
    public function report(Request $request)
    {
        $query = \App\Models\WorkOrder::query();

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $workOrders = $query->get();

        $totalOrders = $workOrders->count();
        $completedOrders = $workOrders->where('status', 'Completed')->count();
        $onProgressOrders = $workOrders->where('status', 'On Progress')->count();
        $pendingOrders = $workOrders->where('status', 'Pending')->count();

        // ==========================================
        // LOGIC TO CALCULATE AVERAGE WORK DURATION
        // ==========================================
        $completedWithTime = $workOrders->where('status', 'Completed')->whereNotNull('completed_at');
        $totalMinutes = 0;
        
        foreach ($completedWithTime as $order) {
            $totalMinutes += \Carbon\Carbon::parse($order->created_at)->diffInMinutes(\Carbon\Carbon::parse($order->completed_at));
        }

        $avgResolutionTime = '0 Menit';
        if ($completedWithTime->count() > 0) {
            $avgMinutes = $totalMinutes / $completedWithTime->count();
            if ($avgMinutes >= 1440) {
                $avgResolutionTime = round($avgMinutes / 1440, 1) . " Hari";
            } elseif ($avgMinutes >= 60) {
                $avgResolutionTime = round($avgMinutes / 60, 1) . " Jam";
            } else {
                $avgResolutionTime = round($avgMinutes) . " Menit";
            }
        }
        // ==========================================

        $departmentStats = $workOrders->groupBy('department')->map->count();
        $issueStats = $workOrders->groupBy('issue_type')->map->count();

        return view('admin-report', compact(
            'totalOrders', 'completedOrders', 'onProgressOrders', 'pendingOrders',
            'departmentStats', 'issueStats', 'avgResolutionTime' // Send new variables to view
        ));
    }

    // 6. Generate & Download Report PDF
    public function downloadPdf(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date']);
        
        $query = WorkOrder::query();
        
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date . ' 23:59:59']);
        }

        $workOrders = $query->get();

        $data = [
            'filters' => $filters,
            'totalOrders' => $workOrders->count(),
            'pendingOrders' => $workOrders->where('status', 'Pending')->count(),
            'onProgressOrders' => $workOrders->where('status', 'On Progress')->count(),
            'completedOrders' => $workOrders->where('status', 'Completed')->count(),
            'departmentStats' => $workOrders->groupBy('department')->map->count(),
            'issueStats' => $workOrders->groupBy('issue_type')->map->count(),
        ];

        // Load special PDF template from resources/views/pdf/report.blade.php
        $pdf = Pdf::loadView('pdf.report', $data)->setPaper('a4', 'portrait');
        
        $fileName = 'Work_Order_Report_' . date('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    // 7. Download PDF for Single Work Order
    public function downloadWorkOrderPdf($id)
    {
        $order = WorkOrder::findOrFail($id);
        $pdf = Pdf::loadView('pdf.work-order', compact('order'))->setPaper('a4', 'portrait');
        $fileName = 'WO_' . $order->wo_number . '.pdf';
        return $pdf->download($fileName);
    }

    // 8. Export Report to Excel (CSV format)
    public function downloadExcel(Request $request)
    {
        $query = WorkOrder::query();
        
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date . ' 23:59:59']);
        }

        $workOrders = $query->orderBy('created_at', 'desc')->get();

        // Create CSV headers
        $headers = [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Work_Order_Report_' . date('Ymd_His') . '.csv"',
        ];

        // Create callback for CSV output
        $callback = function() use ($workOrders) {
            $file = fopen('php://output', 'w');
            
            // Set BOM for Excel to read correctly (UTF-8)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Column headers
            fputcsv($file, [
                'No. WO',
                'Departemen',
                'Jenis Masalah',
                'Deskripsi',
                'Lokasi',
                'Status',
                'Tanggal Dibuat',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Durasi (Menit)',
                'Catatan Penyelesaian'
            ], ';');
            
            // Data
            foreach ($workOrders as $order) {
                $duration = '';
                if ($order->status === 'Completed' && $order->completed_at) {
                    if ($order->duration_minutes) {
                        $duration = (int) $order->duration_minutes; // pastikan bilangan bulat
                    } else {
                        $duration = (int) \Carbon\Carbon::parse($order->created_at)
                                        ->diffInMinutes(\Carbon\Carbon::parse($order->completed_at));
                    }
                }
                
                fputcsv($file, [
                    $order->wo_number,
                    $order->department,
                    $order->issue_type,
                    $order->description,
                    $order->location,
                    $order->status,
                    \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i'),
                    $order->started_at ? \Carbon\Carbon::parse($order->started_at)->format('d/m/Y H:i') : '-',
                    $order->completed_at ? \Carbon\Carbon::parse($order->completed_at)->format('d/m/Y H:i') : '-',
                    $duration,
                    $order->resolution_note ?? '-'
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    // 9. Export Orders to Excel (CSV format) - for orders page
    public function downloadOrdersExcel(Request $request)
    {
        $query = WorkOrder::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('wo_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('issue_type')) {
            $query->where('issue_type', $request->issue_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date . ' 23:59:59']);
        }

        $workOrders = $query->orderBy('created_at', 'desc')->get();

        // Create CSV headers
        $headers = [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Work_Orders_' . date('Ymd_His') . '.csv"',
        ];

        // Create callback for CSV output
        $callback = function() use ($workOrders) {
            $file = fopen('php://output', 'w');
            
            // Set BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Column headers
            fputcsv($file, [
                'No. WO',
                'Departemen',
                'Jenis Masalah',
                'Deskripsi',
                'Lokasi',
                'Status',
                'Tanggal Dibuat',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Durasi (Menit)',
                'Catatan Penyelesaian'
            ], ';');
            
            // Data
            foreach ($workOrders as $order) {
                $duration = '';
                if ($order->status === 'Completed' && $order->completed_at) {
                    if ($order->duration_minutes) {
                        $duration = (int) $order->duration_minutes; // pastikan bilangan bulat
                    } else {
                        $duration = (int) \Carbon\Carbon::parse($order->created_at)
                                        ->diffInMinutes(\Carbon\Carbon::parse($order->completed_at));
                    }
                }
                
                fputcsv($file, [
                    $order->wo_number,
                    $order->department,
                    $order->issue_type,
                    $order->description,
                    $order->location,
                    $order->status,
                    \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i'),
                    $order->started_at ? \Carbon\Carbon::parse($order->started_at)->format('d/m/Y H:i') : '-',
                    $order->completed_at ? \Carbon\Carbon::parse($order->completed_at)->format('d/m/Y H:i') : '-',
                    $duration,
                    $order->resolution_note ?? '-'
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    // 10. Edit Work Order Page
    public function edit($id)
    {
        $order = WorkOrder::findOrFail($id);
        return view('admin-edit', compact('order'));
    }

    // 11. Save Work Order Updates
    public function update(Request $request, $id)
    {
        $request->validate([
            'department' => 'required|exists:departments,name',
            'issue_type' => 'required|exists:issue_types,name',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,On Progress,Completed',
            'resolution_note' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'completed_at' => 'nullable|date_format:Y-m-d H:i',
        ]);

        $order = WorkOrder::findOrFail($id);
        
        $order->department = $request->department;
        $order->issue_type = $request->issue_type;
        $order->location = $request->location;
        $order->description = $request->description;
        $order->status = $request->status;
        $order->resolution_note = $request->resolution_note;
        $order->duration_minutes = $request->duration_minutes;
        
        // Update completed_at if changed
        if ($request->filled('completed_at')) {
            $order->completed_at = $request->completed_at;
        }
        
        $order->save();

        return redirect()->route('admin.detail', $order->id)->with('success', 'Work order updated successfully!');
    }

    // 12. Delete Work Order
    public function delete($id)
    {
        $order = WorkOrder::findOrFail($id);
        $order->delete();

        return redirect('/admin/orders')->with('success', 'Work order deleted successfully!');
    }
}
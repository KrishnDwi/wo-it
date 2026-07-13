<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\IssueType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    // ============ DEPARTMENTS ============
    
    public function departmentIndex()
    {
        $departments = Department::all();
        return view('admin-departments', compact('departments'));
    }

    public function departmentStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        return redirect('/admin/settings/departments')->with('success', 'Department added successfully.');
    }

    public function departmentUpdate(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect('/admin/settings/departments')->with('success', 'Department updated successfully.');
    }

    public function departmentDelete($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return redirect('/admin/settings/departments')->with('success', 'Department deleted successfully.');
    }

    // ============ ISSUE TYPES ============
    
    public function issueTypeIndex()
    {
        $issueTypes = IssueType::all();
        return view('admin-issue-types', compact('issueTypes'));
    }

    public function issueTypeStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:issue_types,name',
            'description' => 'nullable|string',
        ]);

        IssueType::create($validated);

        return redirect('/admin/settings/issue-types')->with('success', 'Issue type added successfully.');
    }

    public function issueTypeUpdate(Request $request, $id)
    {
        $issueType = IssueType::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:issue_types,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $issueType->update($validated);

        return redirect('/admin/settings/issue-types')->with('success', 'Issue type updated successfully.');
    }

    public function issueTypeDelete($id)
    {
        $issueType = IssueType::findOrFail($id);
        $issueType->delete();

        return redirect('/admin/settings/issue-types')->with('success', 'Issue type deleted successfully.');
    }

    // ============ USERS (ADMIN TELEGRAM SETTINGS) ============
    
    public function userIndex()
    {
        $users = User::all();
        return view('admin-users', compact('users'));
    }

    public function userStore(Request $request)
    {
        // Hanya perlu validasi Nama dan Chat ID Telegram
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $isActive = $request->has('is_wa_active');

        // Jika user baru ini langsung diaktifkan Telegram-nya, matikan Telegram admin lain
        if ($isActive) {
            User::query()->update(['is_wa_active' => false]);
        }

        // --- TRIK: Generate Dummy Email & Password otomatis ---
        // Membuat email unik otomatis dari nama admin + angka acak
        $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $validated['name']));
        $dummyEmail = $cleanName . rand(1000, 9999) . '@wa-admin.local';
        $dummyPassword = Hash::make('password123'); 

        // Simpan data ke database
        User::create([
            'name' => $validated['name'],
            'email' => $dummyEmail,
            'password' => $dummyPassword,
            'phone_number' => $validated['phone_number'],
            'is_wa_active' => $isActive,
        ]);

        return redirect('/admin/settings/users')->with('success', 'Admin Telegram berhasil ditambahkan.');
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'phone_number' => 'nullable|string|max:20',
        ]);

        // Cek apakah admin ini dicentang sebagai Admin Aktif Telegram
        $isActive = $request->has('is_wa_active');

        // Opsional cerdas: Jika admin ini diaktifkan, maka sistem otomatis mematikan 
        // status admin lainnya, agar notifikasi Telegram hanya dikirim ke 1 admin saja.
        if ($isActive) {
            User::where('id', '!=', $id)->update(['is_wa_active' => false]);
        }

        $user->update([
            'phone_number' => $request->phone_number,
            'is_wa_active' => $isActive,
        ]);

        return redirect('/admin/settings/users')->with('success', 'Admin Telegram settings updated successfully.');
    }
}

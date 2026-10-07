<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    public function __construct(protected AdminManagementService $adminService)
    {
    }

    private function authorizeMasterAdmin()
    {
        if (!Auth::check() || !Auth::user()->isMasterAdmin()) {
            abort(403, 'Akses terbatas! Hanya Master Admin yang memiliki hak mengelola data Admin.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeMasterAdmin();

        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status');

        $admins = $this->adminService->getPaginatedAdmins($search, $role, $status);

        return view('admin.admins.index', compact('admins', 'search', 'role', 'status'));
    }

    public function create()
    {
        $this->authorizeMasterAdmin();
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $this->authorizeMasterAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,master_admin'],
            'status' => ['required', 'in:active,inactive'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $admin = $this->adminService->createAdmin(
            data: $validated,
            photo: $request->file('photo'),
            actor: Auth::user()
        );

        return redirect()->route('admin.admins.index')->with('success', "Akun " . ($admin->role === 'master_admin' ? 'Master Admin' : 'Admin') . " {$admin->name} berhasil dibuat!");
    }

    public function edit(User $admin)
    {
        $this->authorizeMasterAdmin();

        if (!$admin->isAdmin()) {
            abort(404);
        }

        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        $this->authorizeMasterAdmin();

        if (!$admin->isAdmin()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:admin,master_admin'],
            'status' => ['required', 'in:active,inactive'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($admin->id === Auth::id()) {
            if ($validated['status'] === 'inactive') {
                return back()->withErrors(['status' => 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif.'])->withInput();
            }
            if ($validated['role'] !== 'master_admin') {
                return back()->withErrors(['role' => 'Anda tidak dapat menurunkan peran (demote) akun Anda sendiri dari Master Admin.'])->withInput();
            }
        }

        $this->adminService->updateAdmin(
            admin: $admin,
            data: $validated,
            photo: $request->file('photo'),
            actor: Auth::user()
        );

        return redirect()->route('admin.admins.index')->with('success', "Data admin {$admin->name} berhasil diperbarui.");
    }

    public function toggleStatus(User $admin)
    {
        $this->authorizeMasterAdmin();

        if (!$admin->isAdmin()) {
            abort(404);
        }

        if ($admin->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $newStatus = $this->adminService->toggleStatus($admin, Auth::user());

        return back()->with('success', "Status admin {$admin->name} kini menjadi {$newStatus}.");
    }

    public function destroy(User $admin)
    {
        $this->authorizeMasterAdmin();

        if (!$admin->isAdmin()) {
            abort(404);
        }

        if ($admin->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $admin->name;
        $this->adminService->deleteAdmin($admin, Auth::user());

        return redirect()->route('admin.admins.index')->with('success', "Akun admin {$name} berhasil dihapus.");
    }
}

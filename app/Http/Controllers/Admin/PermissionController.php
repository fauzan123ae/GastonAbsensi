<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionRequest;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function __construct(protected PermissionService $permissionService)
    {
    }

    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $permissions = $this->permissionService->getAdminPermissions($status);

        return view('admin.permissions.index', compact('permissions', 'status'));
    }

    public function update(Request $request, PermissionRequest $permission)
    {
        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);

        $this->permissionService->reviewPermission(
            permission: $permission,
            newStatus: $request->status,
            reviewer: Auth::user(),
            reviewNote: $request->review_note
        );

        $actionText = $request->status === 'approved' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Permohonan izin {$permission->user->name} berhasil {$actionText}.");
    }
}

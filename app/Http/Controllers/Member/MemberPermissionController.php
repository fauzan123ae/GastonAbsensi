<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberPermissionController extends Controller
{
    public function __construct(protected PermissionService $permissionService)
    {
    }

    public function index()
    {
        $data = $this->permissionService->getMemberPermissionData(Auth::user());

        return view('member.permissions.index', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['required', 'exists:sport_sessions,id'],
            'reason' => ['required', 'string', 'max:500'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $this->permissionService->submitPermission(
            user: Auth::user(),
            data: $validated,
            proof: $request->file('proof')
        );

        return back()->with('success', 'Permohonan izin berhasil dikirimkan dan menunggu peninjauan admin.');
    }

    public function destroy(\App\Models\PermissionRequest $permission)
    {
        $this->permissionService->cancelPendingPermission($permission, Auth::user());

        return back()->with('success', 'Permohonan izin berhasil dibatalkan.');
    }
}

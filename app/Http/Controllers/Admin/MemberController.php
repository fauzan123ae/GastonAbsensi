<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function __construct(protected MemberService $memberService)
    {
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $members = $this->memberService->getPaginatedMembers($search, $status);

        return view('admin.members.index', compact('members', 'search', 'status'));
    }

    public function create()
    {
        $suggestedMemberId = User::generateNextMemberId();
        return view('admin.members.create', compact('suggestedMemberId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $member = $this->memberService->createMember(
            data: $validated,
            photo: $request->file('photo'),
            actor: Auth::user()
        );

        $msg = "Member {$member->name} ({$member->member_id}) berhasil ditambahkan!";
        if (!empty($member->generated_password)) {
            $msg .= " Password acak yang dibuat: {$member->generated_password} (Simpan atau sampaikan ke member sekarang).";
        }

        return redirect()->route('admin.members.index')->with('success', $msg);
    }

    public function show(User $member)
    {
        if (!$member->isMember()) {
            abort(404);
        }

        $member->load([
            'sessionParticipants.session.sport',
            'sessionParticipants.session.location',
            'attendances.session.sport',
            'attendances.session.location',
            'permissionRequests.session.sport',
        ]);

        return view('admin.members.show', compact('member'));
    }

    public function edit(User $member)
    {
        if (!$member->isMember()) {
            abort(404);
        }

        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, User $member)
    {
        if (!$member->isMember()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($member->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $this->memberService->updateMember(
            member: $member,
            data: $validated,
            photo: $request->file('photo'),
            actor: Auth::user()
        );

        return redirect()->route('admin.members.index')->with('success', "Member {$member->name} berhasil diperbarui.");
    }

    public function toggleStatus(User $member)
    {
        if (!$member->isMember()) {
            abort(404);
        }

        $newStatus = $this->memberService->toggleStatus($member, Auth::user());

        return back()->with('success', "Status member {$member->name} diubah menjadi {$newStatus}.");
    }

    public function qr(User $member)
    {
        if (!$member->isMember()) {
            abort(404);
        }

        return view('admin.members.qr', compact('member'));
    }
}

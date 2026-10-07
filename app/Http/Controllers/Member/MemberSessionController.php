<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PermissionRequest;
use App\Models\SportSession;
use Illuminate\Support\Facades\Auth;

class MemberSessionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $participations = $user->sessionParticipants()
            ->with(['session.sport', 'session.location'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        $sessionIds = $participations->pluck('session_id')->filter()->unique();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereIn('session_id', $sessionIds)
            ->get()
            ->keyBy('session_id');

        $permissions = PermissionRequest::where('user_id', $user->id)
            ->whereIn('session_id', $sessionIds)
            ->get()
            ->keyBy('session_id');

        return view('member.sessions.index', compact('participations', 'attendances', 'permissions'));
    }
}

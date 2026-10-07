<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class MemberAttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $attendances = Attendance::with(['session.sport', 'session.location'])
            ->where('user_id', $user->id)
            ->orderBy('checked_in_at', 'desc')
            ->paginate(15);

        return view('member.attendance.index', compact('attendances'));
    }
}

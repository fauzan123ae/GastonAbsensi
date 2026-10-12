<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MemberQrController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // Cari sesi kegiatan yang diselenggarakan hari ini di mana member berpartisipasi atau terbuka
        $todaySessions = \App\Models\SportSession::with(['sport', 'location'])
            ->whereDate('date', $today)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();

        $sessionIds = $todaySessions->pluck('id');

        $attendances = \App\Models\Attendance::where('user_id', $user->id)
            ->whereIn('session_id', $sessionIds)
            ->get()
            ->keyBy('session_id');

        $permissions = \App\Models\PermissionRequest::where('user_id', $user->id)
            ->whereIn('session_id', $sessionIds)
            ->get()
            ->keyBy('session_id');

        return view('member.qr', compact('user', 'todaySessions', 'attendances', 'permissions'));
    }
}

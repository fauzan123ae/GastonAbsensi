<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\PermissionRequest;
use App\Models\SessionParticipant;
use App\Models\SportSession;
use App\Models\User;

class DashboardService
{
    /**
     * Compile summary data and metrics for Admin Dashboard.
     */
    public function getAdminDashboardData(): array
    {
        $today = now()->toDateString();

        // 1. Sessions today
        $todaySessions = SportSession::with(['sport', 'location'])
            ->withCount([
                'participants as registered_count' => function ($q) {
                    $q->where('status', 'registered');
                },
                'attendances as present_count' => function ($q) {
                    $q->where('status', 'present');
                },
            ])
            ->whereDate('date', $today)
            ->orderBy('start_time')
            ->get();

        $todaySessionIds = $todaySessions->pluck('id');

        // Metrics for today
        $totalSessionsToday = $todaySessions->count();
        $totalParticipantsToday = SessionParticipant::whereIn('session_id', $todaySessionIds)
            ->where('status', 'registered')
            ->count();

        $totalPresentToday = Attendance::whereIn('session_id', $todaySessionIds)
            ->where('status', 'present')
            ->count();

        $pendingPermissionsCount = PermissionRequest::where('status', 'pending')->count();
        $totalMembers = User::where('role', 'member')->count();

        $notYetPresentToday = max(0, $totalParticipantsToday - $totalPresentToday);

        // Upcoming sessions
        $upcomingSessions = SportSession::with(['sport', 'location'])
            ->whereDate('date', '>=', $today)
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Recent Attendances for Dashboard Table
        $recentAttendances = Attendance::with(['user', 'session.sport', 'session.location'])
            ->latest('checked_in_at')
            ->take(5)
            ->get();

        return compact(
            'todaySessions',
            'totalSessionsToday',
            'totalParticipantsToday',
            'totalPresentToday',
            'notYetPresentToday',
            'pendingPermissionsCount',
            'totalMembers',
            'upcomingSessions',
            'recentAttendances'
        );
    }

    /**
     * Compile summary data and metrics for Member Dashboard.
     */
    public function getMemberDashboardData(User $user): array
    {
        // 1. Upcoming session for this member
        $upcomingSession = SportSession::with(['sport', 'location'])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'registered');
            })
            ->whereDate('date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('date')
            ->orderBy('start_time')
            ->first();

        // 2. Attendance stats (rate denominator counts only previously registered participants)
        $totalRegistered = SessionParticipant::where('session_id', '!=', 0)
            ->where('user_id', $user->id)
            ->where('status', 'registered')
            ->whereNotExists(function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('attendances')
                    ->whereColumn('attendances.session_id', 'session_participants.session_id')
                    ->whereColumn('attendances.user_id', 'session_participants.user_id')
                    ->where('attendances.is_walk_in', true);
            })
            ->count();
        $totalPresent = Attendance::where('user_id', $user->id)->where('status', 'present')->count();
        $registeredPresent = Attendance::where('user_id', $user->id)
            ->where('status', 'present')
            ->where('is_walk_in', false)
            ->count();

        // Approved permissions: only count if member is registered participant AND has NO 'present' attendance in that session
        $totalPermission = PermissionRequest::where('permission_requests.user_id', $user->id)
            ->where('permission_requests.status', 'approved')
            ->whereExists(function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('session_participants')
                    ->whereColumn('session_participants.session_id', 'permission_requests.session_id')
                    ->whereColumn('session_participants.user_id', 'permission_requests.user_id')
                    ->where('session_participants.status', 'registered');
            })
            ->whereNotExists(function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('attendances')
                    ->whereColumn('attendances.session_id', 'permission_requests.session_id')
                    ->whereColumn('attendances.user_id', 'permission_requests.user_id')
                    ->where('attendances.status', 'present');
            })
            ->count();

        $totalAbsent = max(0, $totalRegistered - $registeredPresent - $totalPermission);

        $attendanceRate = 0;
        $effectiveRegistered = $totalRegistered - $totalPermission;
        if ($effectiveRegistered > 0) {
            $attendanceRate = round(($registeredPresent / $effectiveRegistered) * 100, 1);
        }

        return compact(
            'user',
            'upcomingSession',
            'totalRegistered',
            'totalPresent',
            'totalPermission',
            'totalAbsent',
            'attendanceRate'
        );
    }
}

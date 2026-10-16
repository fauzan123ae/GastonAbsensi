<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\PermissionRequest;
use App\Models\SessionParticipant;
use App\Models\Sport;
use App\Models\SportSession;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * Build report metrics and paginated attendance records based on filters.
     */
    public function getReportData(array $filters): array
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $sportId = $filters['sport_id'] ?? null;
        $locationId = $filters['location_id'] ?? null;
        $memberId = $filters['member_id'] ?? null;
        $method = $filters['method'] ?? null;

        $sessionQuery = SportSession::query();

        if ($startDate) {
            $sessionQuery->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $sessionQuery->whereDate('date', '<=', $endDate);
        }
        if ($sportId) {
            $sessionQuery->where('sport_id', $sportId);
        }
        if ($locationId) {
            $sessionQuery->where('location_id', $locationId);
        }

        $sessionIds = $sessionQuery->pluck('id');

        $totalSessions = $sessionIds->count();

        // Base registered participants query (excluding walk-in attendees)
        $registeredQuery = SessionParticipant::whereIn('session_id', $sessionIds)
            ->where('status', 'registered')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('attendances')
                    ->whereColumn('attendances.session_id', 'session_participants.session_id')
                    ->whereColumn('attendances.user_id', 'session_participants.user_id')
                    ->whereRaw('attendances.is_walk_in = TRUE');
            });
        if ($memberId) {
            $registeredQuery->where('user_id', $memberId);
        }
        $totalRegistered = $registeredQuery->count();

        // Total present attendances
        $totalPresentQuery = Attendance::whereIn('session_id', $sessionIds)->where('status', 'present');
        if ($method) {
            $totalPresentQuery->where('method', $method);
        }
        if ($memberId) {
            $totalPresentQuery->where('user_id', $memberId);
        }
        $totalPresent = $totalPresentQuery->count();

        // Registered present (non-walk-in)
        $registeredPresentQuery = Attendance::whereIn('session_id', $sessionIds)
            ->where('status', 'present')
            ->where('is_walk_in', false);
        if ($method) {
            $registeredPresentQuery->where('method', $method);
        }
        if ($memberId) {
            $registeredPresentQuery->where('user_id', $memberId);
        }
        $registeredPresentCount = $registeredPresentQuery->count();

        // Approved permissions: only count if member is registered participant AND has NO 'present' attendance in that session
        // Note: if filtered by method, permission has no method so totalPermission should be 0 if filtering by method
        if ($method) {
            $totalPermission = 0;
        } else {
            $permQuery = PermissionRequest::whereIn('permission_requests.session_id', $sessionIds)
                ->where('permission_requests.status', 'approved')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('session_participants')
                        ->whereColumn('session_participants.session_id', 'permission_requests.session_id')
                        ->whereColumn('session_participants.user_id', 'permission_requests.user_id')
                        ->where('session_participants.status', 'registered');
                })
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('attendances')
                        ->whereColumn('attendances.session_id', 'permission_requests.session_id')
                        ->whereColumn('attendances.user_id', 'permission_requests.user_id')
                        ->where('attendances.status', 'present');
                });

            if ($memberId) {
                $permQuery->where('permission_requests.user_id', $memberId);
            }
            $totalPermission = $permQuery->count();
        }

        $totalAbsent = max(0, $totalRegistered - $registeredPresentCount - $totalPermission);

        // Attendance rate: walk-in does NOT increase rate (numerator = registered present, denominator = registered - permission)
        $attendanceRate = 0;
        $effectiveRegistered = $totalRegistered - $totalPermission;
        if ($effectiveRegistered > 0) {
            $attendanceRate = round(($registeredPresentCount / $effectiveRegistered) * 100, 1);
        }

        // Attendance listing for report table
        $attendancesQuery = Attendance::with(['session.sport', 'session.location', 'user', 'checkedInBy'])
            ->whereIn('session_id', $sessionIds);

        if ($method) {
            $attendancesQuery->where('method', $method);
        }
        if ($memberId) {
            $attendancesQuery->where('user_id', $memberId);
        }

        $attendances = $attendancesQuery->orderBy('checked_in_at', 'desc')->paginate(15)->withQueryString();

        $sports = Sport::all();
        $locations = Location::all();

        // Limit member dropdown to avoid loading all users at once
        $membersQuery = User::where('role', 'member');
        if ($memberId) {
            $membersQuery->where(function ($q) use ($memberId) {
                $q->where('id', $memberId)->orWhere('status', 'active');
            });
        }
        $members = $membersQuery->orderBy('name')->take(50)->get();

        return compact(
            'totalSessions',
            'totalRegistered',
            'totalPresent',
            'totalPermission',
            'totalAbsent',
            'attendanceRate',
            'attendances',
            'sports',
            'locations',
            'members',
            'startDate',
            'endDate',
            'sportId',
            'locationId',
            'memberId',
            'method'
        );
    }

    /**
     * Stream CSV attendance export file.
     */
    public function exportCsv(array $filters): StreamedResponse
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $sportId = $filters['sport_id'] ?? null;
        $locationId = $filters['location_id'] ?? null;
        $memberId = $filters['member_id'] ?? null;
        $method = $filters['method'] ?? null;

        $sessionQuery = SportSession::query();
        if ($startDate) $sessionQuery->whereDate('date', '>=', $startDate);
        if ($endDate) $sessionQuery->whereDate('date', '<=', $endDate);
        if ($sportId) $sessionQuery->where('sport_id', $sportId);
        if ($locationId) $sessionQuery->where('location_id', $locationId);

        $sessionIds = $sessionQuery->pluck('id');

        $attendancesQuery = Attendance::with(['session.sport', 'session.location', 'user', 'checkedInBy'])
            ->whereIn('session_id', $sessionIds)
            ->orderBy('checked_in_at', 'desc');

        if ($method) {
            $attendancesQuery->where('method', $method);
        }
        if ($memberId) {
            $attendancesQuery->where('user_id', $memberId);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="gaston_attendance_report_' . date('Ymd_His') . '.csv"',
        ];

        return response()->stream(function () use ($attendancesQuery) {
            $handle = fopen('php://output', 'w');

            // Prepend UTF-8 BOM so Excel opens CSV with proper encoding
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Session ID',
                'Date',
                'Sport',
                'Location / GOR',
                'Member ID',
                'Member Name',
                'Member Email',
                'Status',
                'Method',
                'Check-in Time',
                'Checked In By',
                'Notes',
            ]);

            // Helper to sanitize CSV formula injection (=, +, -, @)
            $sanitize = function ($value) {
                if ($value === null || $value === '') {
                    return '-';
                }
                $str = (string) $value;
                if (in_array(substr($str, 0, 1), ['=', '+', '-', '@'], true)) {
                    return "'" . $str;
                }
                return $str;
            };

            // Use cursor() instead of get() to handle large datasets efficiently
            foreach ($attendancesQuery->cursor() as $att) {
                fputcsv($handle, [
                    $sanitize($att->session_id),
                    $sanitize($att->session?->date ? $att->session->date->format('Y-m-d') : '-'),
                    $sanitize($att->session?->sport?->name ?? '-'),
                    $sanitize($att->session?->location?->name ?? '-'),
                    $sanitize($att->user?->member_id ?? '-'),
                    $sanitize($att->user?->name ?? '-'),
                    $sanitize($att->user?->email ?? '-'),
                    $sanitize($att->status),
                    $sanitize(strtoupper($att->method)),
                    $sanitize($att->checked_in_at ? $att->checked_in_at->format('Y-m-d H:i:s') : '-'),
                    $sanitize($att->checkedInBy?->name ?? 'System'),
                    $sanitize($att->notes ?? '-'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}

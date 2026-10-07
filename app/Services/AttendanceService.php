<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\SessionParticipant;
use App\Models\SportSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class AttendanceService
{
    /**
     * Look up and validate a member for a session by QR token or Member ID
     * Returns array with:
     * - success: bool
     * - message: string
     * - code: string (SUCCESS, ALREADY_CHECKED_IN, NOT_REGISTERED, INACTIVE, SESSION_CLOSED, NOT_FOUND)
     * - member: User|null
     * - is_registered: bool
     * - attendance: Attendance|null
     */
    public function verifyMemberForSession(SportSession $session, string $identifier): array
    {
        $session->loadMissing(['sport', 'location']);

        if (!$session->canAcceptAttendance()) {
            $msg = match ($session->status) {
                'completed' => 'Sesi sudah selesai. Absensi tidak dapat dilakukan.',
                'cancelled' => 'Sesi dibatalkan. Absensi tidak dapat dilakukan.',
                'draft' => 'Sesi masih berstatus draft. Absensi belum dibuka.',
                default => 'Absensi hanya dapat dilakukan pada tanggal pelaksanaan sesi hari ini.',
            };

            return [
                'success' => false,
                'code' => 'SESSION_CLOSED',
                'message' => $msg,
                'member' => null,
                'is_registered' => false,
                'attendance' => null,
            ];
        }

        $trimmed = trim($identifier);
        $member = User::where('role', 'member')->where('qr_token', $trimmed)->first()
            ?? User::where('role', 'member')->where('member_id', $trimmed)->first()
            ?? User::where('role', 'member')->where('email', $trimmed)->first();

        if (!$member) {
            return [
                'success' => false,
                'code' => 'NOT_FOUND',
                'message' => 'Member tidak ditemukan. Pastikan QR code atau Member ID valid.',
                'member' => null,
                'is_registered' => false,
                'attendance' => null,
            ];
        }

        if (!$member->isActive()) {
            return [
                'success' => false,
                'code' => 'INACTIVE',
                'message' => 'Member tidak aktif. Absensi ditolak.',
                'member' => $member,
                'is_registered' => false,
                'attendance' => null,
            ];
        }

        // Check if already checked in
        $existingAttendance = Attendance::where('session_id', $session->id)
            ->where('user_id', $member->id)
            ->where('status', 'present')
            ->first();

        if ($existingAttendance) {
            return [
                'success' => false,
                'code' => 'ALREADY_CHECKED_IN',
                'message' => 'Member sudah melakukan absensi pada sesi ini.',
                'member' => $member,
                'is_registered' => true,
                'attendance' => $existingAttendance,
            ];
        }

        // Check if registered participant
        $isRegistered = SessionParticipant::where('session_id', $session->id)
            ->where('user_id', $member->id)
            ->where('status', 'registered')
            ->exists();

        if (!$isRegistered) {
            return [
                'success' => true, // Allows admin prompt for walk-in
                'code' => 'NOT_REGISTERED',
                'message' => 'Member tidak terdaftar pada sesi ini. Tambahkan sebagai Walk-in?',
                'member' => $member,
                'is_registered' => false,
                'attendance' => null,
            ];
        }

        return [
            'success' => true,
            'code' => 'READY_FOR_CONFIRMATION',
            'message' => 'Member terdaftar. Silakan konfirmasi kehadiran.',
            'member' => $member,
            'is_registered' => true,
            'attendance' => null,
        ];
    }

    /**
     * Confirm attendance for an already registered participant
     */
    public function confirmAttendance(
        SportSession $session,
        User $member,
        User $admin,
        string $method = 'qr',
        ?string $notes = null
    ): Attendance {
        if (!$session->canAcceptAttendance()) {
            throw new Exception('Sesi tidak dapat menerima absensi karena status ' . $session->status);
        }

        if (!$member->isActive()) {
            throw new Exception('Member tidak aktif.');
        }

        return DB::transaction(function () use ($session, $member, $admin, $method, $notes) {
            // Lock or check existing attendance
            $existing = Attendance::where('session_id', $session->id)
                ->where('user_id', $member->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                if ($existing->status === 'present') {
                    throw new Exception('Member sudah hadir sebelumnya.');
                }
                $existing->update([
                    'checked_in_at' => now(),
                    'checked_in_by' => $admin->id,
                    'method' => $method,
                    'status' => 'present',
                    'notes' => $notes,
                ]);
                $attendance = $existing;
            } else {
                try {
                    $attendance = Attendance::create([
                        'session_id' => $session->id,
                        'user_id' => $member->id,
                        'checked_in_at' => now(),
                        'checked_in_by' => $admin->id,
                        'method' => $method,
                        'status' => 'present',
                        'notes' => $notes,
                    ]);
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    $attendance = Attendance::where('session_id', $session->id)
                        ->where('user_id', $member->id)
                        ->first();
                    if ($attendance && $attendance->status === 'present') {
                        throw new Exception('Member sudah hadir sebelumnya.');
                    }
                    $attendance->update([
                        'checked_in_at' => now(),
                        'checked_in_by' => $admin->id,
                        'method' => $method,
                        'status' => 'present',
                        'notes' => $notes,
                    ]);
                }
            }

            AuditLogService::log(
                action: 'confirm_attendance',
                targetType: 'Attendance',
                targetId: $attendance->id,
                description: "Admin {$admin->name} mengonfirmasi kehadiran {$member->name} ({$member->member_id}) via {$method}",
                newValues: [
                    'session_id' => $session->id,
                    'user_id' => $member->id,
                    'method' => $method,
                    'status' => 'present',
                ],
                userId: $admin->id
            );

            return $attendance;
        });
    }

    /**
     * Add member as Walk-in and mark as attended
     */
    public function recordWalkIn(
        SportSession $session,
        User $member,
        User $admin,
        string $method = 'qr',
        ?string $notes = 'Walk-in participant'
    ): Attendance {
        if (!$session->canAcceptAttendance()) {
            throw new Exception('Sesi tidak dapat menerima walk-in karena status ' . $session->status);
        }

        if (!$member->isActive()) {
            throw new Exception('Member tidak aktif.');
        }

        return DB::transaction(function () use ($session, $member, $admin, $method, $notes) {
            // 1. Ensure participant record exists
            $participant = SessionParticipant::where('session_id', $session->id)
                ->where('user_id', $member->id)
                ->first();

            if (!$participant) {
                SessionParticipant::create([
                    'session_id' => $session->id,
                    'user_id' => $member->id,
                    'registration_source' => 'manual',
                    'status' => 'registered',
                    'registered_at' => now(),
                ]);
            }

            // 2. Create attendance
            $existing = Attendance::where('session_id', $session->id)
                ->where('user_id', $member->id)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->status === 'present') {
                throw new Exception('Member sudah hadir sebelumnya.');
            }

            if ($existing) {
                $existing->update([
                    'checked_in_at' => now(),
                    'checked_in_by' => $admin->id,
                    'method' => $method,
                    'is_walk_in' => true,
                    'status' => 'present',
                    'notes' => $notes ?? 'Walk-in',
                ]);
                $attendance = $existing;
            } else {
                try {
                    $attendance = Attendance::create([
                        'session_id' => $session->id,
                        'user_id' => $member->id,
                        'checked_in_at' => now(),
                        'checked_in_by' => $admin->id,
                        'method' => $method,
                        'is_walk_in' => true,
                        'status' => 'present',
                        'notes' => $notes ?? 'Walk-in',
                    ]);
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    $attendance = Attendance::where('session_id', $session->id)
                        ->where('user_id', $member->id)
                        ->first();
                    if ($attendance && $attendance->status === 'present') {
                        throw new Exception('Member sudah hadir sebelumnya.');
                    }
                    $attendance->update([
                        'checked_in_at' => now(),
                        'checked_in_by' => $admin->id,
                        'method' => $method,
                        'is_walk_in' => true,
                        'status' => 'present',
                        'notes' => $notes ?? 'Walk-in',
                    ]);
                }
            }

            AuditLogService::log(
                action: 'walk_in_attendance',
                targetType: 'Attendance',
                targetId: $attendance->id,
                description: "Admin {$admin->name} menambahkan walk-in & kehadiran {$member->name} ({$member->member_id})",
                newValues: [
                    'session_id' => $session->id,
                    'user_id' => $member->id,
                    'method' => $method,
                    'walk_in' => true,
                ],
                userId: $admin->id
            );

            return $attendance;
        });
    }

    /**
     * Reconcile and calculate session summary stats
     */
    public function getSessionSummary(SportSession $session): array
    {
        // 1. Registered participants (excluding walk-ins who were not previously registered)
        $walkInUserIds = Attendance::where('session_id', $session->id)
            ->where('is_walk_in', true)
            ->pluck('user_id');

        $participantsCount = SessionParticipant::where('session_id', $session->id)
            ->where('status', 'registered')
            ->whereNotIn('user_id', $walkInUserIds)
            ->count();

        // 2. Attendances
        $presentCount = Attendance::where('session_id', $session->id)
            ->where('status', 'present')
            ->count();

        $registeredPresentCount = Attendance::where('session_id', $session->id)
            ->where('status', 'present')
            ->where('is_walk_in', false)
            ->count();

        // 3. Approved permissions: only count if member is registered participant AND has NO 'present' attendance in this session
        $registeredUserIds = SessionParticipant::where('session_id', $session->id)
            ->where('status', 'registered')
            ->whereNotIn('user_id', $walkInUserIds)
            ->pluck('user_id');

        $presentUserIds = Attendance::where('session_id', $session->id)
            ->where('status', 'present')
            ->pluck('user_id');

        $approvedPermissionsCount = \App\Models\PermissionRequest::where('session_id', $session->id)
            ->where('status', 'approved')
            ->whereIn('user_id', $registeredUserIds)
            ->whereNotIn('user_id', $presentUserIds)
            ->count();

        // 4. Absent count (from registered participants who neither attended nor have approved permission)
        $absentCount = max(0, $participantsCount - $registeredPresentCount - $approvedPermissionsCount);

        // 5. Attendance rate: Walk-in does NOT increase attendance rate.
        // Numerator: registered attendees who are present
        // Denominator: registered participants minus approved permissions (who didn't attend)
        $attendanceRate = 0;
        $effectiveRegistered = $participantsCount - $approvedPermissionsCount;
        if ($effectiveRegistered > 0) {
            $attendanceRate = round(($registeredPresentCount / $effectiveRegistered) * 100, 1);
        }

        return [
            'total_registered' => $participantsCount,
            'present' => $presentCount,
            'permission' => $approvedPermissionsCount,
            'absent' => $absentCount,
            'attendance_rate' => $attendanceRate,
        ];
    }

    /**
     * Get reconciled list of participants and walk-ins with attendance status
     */
    public function getReconciledAttendanceList(SportSession $session, string $statusFilter = 'all'): array
    {
        $participants = SessionParticipant::with('user')
            ->where('session_id', $session->id)
            ->get();

        $attendances = Attendance::with(['user', 'checkedInBy'])
            ->where('session_id', $session->id)
            ->get()
            ->keyBy('user_id');

        $permissions = \App\Models\PermissionRequest::where('session_id', $session->id)
            ->where('status', 'approved')
            ->get()
            ->keyBy('user_id');

        $reconciled = [];

        // 1. Registered participants
        foreach ($participants as $p) {
            $user = $p->user;
            if (!$user) continue;

            $att = $attendances->get($user->id);
            $perm = $permissions->get($user->id);

            $derivedStatus = 'Tidak Hadir';
            $method = '-';
            $checkedInAt = '-';

            if ($att && $att->status === 'present') {
                $derivedStatus = $att->is_walk_in ? 'Walk-in' : 'Hadir';
                $method = strtoupper($att->method);
                $checkedInAt = $att->checked_in_at->format('H:i');
            } elseif ($perm) {
                $derivedStatus = 'Izin';
            }

            $reconciled[] = (object)[
                'user' => $user,
                'status' => $derivedStatus,
                'method' => $method,
                'checked_in_at' => $checkedInAt,
                'attendance_id' => $att?->id,
            ];
        }

        // 2. Walk-in attendances that were not originally planned participants
        foreach ($attendances as $userId => $att) {
            if ($att->status === 'present' && !$participants->firstWhere('user_id', $userId)) {
                $reconciled[] = (object)[
                    'user' => $att->user,
                    'status' => 'Walk-in',
                    'method' => strtoupper($att->method),
                    'checked_in_at' => $att->checked_in_at->format('H:i'),
                    'attendance_id' => $att->id,
                ];
            }
        }

        if ($statusFilter !== 'all') {
            $normalizedFilter = strtolower(str_replace(['-', ' '], '', $statusFilter));
            $reconciled = array_values(array_filter($reconciled, function ($row) use ($normalizedFilter) {
                $rowStatus = strtolower(str_replace(['-', ' '], '', $row->status));
                return $rowStatus === $normalizedFilter;
            }));
        }

        return $reconciled;
    }

    /**
     * Cancel attendance with reason and audit log
     */
    public function cancelAttendance(Attendance $attendance, string $reason, ?User $actor = null): Attendance
    {
        $session = $attendance->session;
        if ($session->isCompleted()) {
            throw new Exception('Kehadiran pada sesi yang sudah selesai tidak dapat dibatalkan.');
        }

        if ($attendance->status === 'cancelled') {
            throw new Exception('Kehadiran sudah dalam status dibatalkan.');
        }

        return DB::transaction(function () use ($attendance, $reason, $actor) {
            $oldStatus = $attendance->status;
            $attendance->update([
                'status' => 'cancelled',
                'notes' => 'Dibatalkan: ' . $reason,
            ]);

            AuditLogService::log(
                action: 'cancel_attendance',
                targetType: 'Attendance',
                targetId: $attendance->id,
                description: "Admin membatalkan kehadiran {$attendance->user->name}. Alasan: {$reason}",
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => 'cancelled', 'reason' => $reason],
                userId: $actor?->id
            );

            return $attendance;
        });
    }

    /**
     * Mark session as completed
     */
    public function closeSession(SportSession $session, ?User $actor = null): SportSession
    {
        if (in_array($session->status, ['completed', 'cancelled'])) {
            throw new Exception("Sesi sudah berstatus '{$session->status}' dan tidak dapat diselesaikan lagi.");
        }

        return DB::transaction(function () use ($session, $actor) {
            $oldStatus = $session->status;
            $session->status = 'completed';
            $session->save();

            AuditLogService::log(
                action: 'close_session',
                targetType: 'SportSession',
                targetId: $session->id,
                description: "Admin menyelesaikan sesi olahraga #{$session->id}",
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => 'completed'],
                userId: $actor?->id
            );

            return $session;
        });
    }
}

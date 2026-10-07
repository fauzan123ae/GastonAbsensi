<?php

namespace App\Services;

use App\Models\PermissionRequest;
use App\Models\SportSession;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionService
{
    /**
     * Get paginated permission requests for Admin with optional status filter.
     */
    public function getAdminPermissions(?string $status = 'all', int $perPage = 15): LengthAwarePaginator
    {
        $query = PermissionRequest::with(['session.sport', 'session.location', 'user', 'reviewer']);

        if ($status !== 'all' && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
    }

    /**
     * Review / Update permission status by admin.
     */
    public function reviewPermission(
        PermissionRequest $permission,
        string $newStatus,
        User $reviewer,
        ?string $reviewNote = null
    ): PermissionRequest {
        $oldStatus = $permission->status;
        $permission->status = $newStatus;
        $permission->reviewed_by = $reviewer->id;
        $permission->reviewed_at = now();
        $permission->review_note = $reviewNote;
        $permission->save();

        AuditLogService::log(
            action: ($newStatus === 'approved' ? 'approve_permission' : 'reject_permission'),
            targetType: 'PermissionRequest',
            targetId: $permission->id,
            description: "Admin " . ($newStatus === 'approved' ? 'menyetujui' : 'menolak') . " izin {$permission->user->name} untuk sesi #{$permission->session_id}" . ($reviewNote ? " (Catatan: {$reviewNote})" : ''),
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $permission->status, 'review_note' => $permission->review_note],
            userId: $reviewer->id
        );

        return $permission;
    }

    /**
     * Get a member's permission history and list of upcoming registered sessions eligible for requesting leave.
     */
    public function getMemberPermissionData(User $user, int $perPage = 10): array
    {
        $permissions = PermissionRequest::with(['session.sport', 'session.location'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Eligible sessions: registered, not cancelled, not completed, date >= today, and no pending or approved permission
        // (Sessions with rejected permission CAN be re-applied)
        $eligibleSessions = SportSession::with(['sport', 'location'])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'registered');
            })
            ->whereDate('date', '>=', now()->toDateString())
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->whereDoesntHave('permissionRequests', function ($q) use ($user) {
                $q->where('user_id', $user->id)->whereIn('status', ['pending', 'approved']);
            })
            ->get();

        return compact('permissions', 'eligibleSessions');
    }

    /**
     * Submit or re-submit a permission request by member.
     */
    public function submitPermission(User $user, array $data, ?UploadedFile $proof = null): PermissionRequest
    {
        $session = SportSession::findOrFail($data['session_id']);

        // (a) Validate member is registered with status 'registered' in this session
        $isRegistered = \App\Models\SessionParticipant::where('session_id', $session->id)
            ->where('user_id', $user->id)
            ->where('status', 'registered')
            ->exists();

        if (!$isRegistered) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'session_id' => 'Anda tidak terdaftar sebagai peserta pada sesi olahraga ini.',
            ]);
        }

        // (b) Validate session is not past and not cancelled or completed
        if (in_array($session->status, ['cancelled', 'completed'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'session_id' => 'Sesi olahraga sudah selesai atau dibatalkan.',
            ]);
        }

        if ($session->date->isPast() && !$session->date->isToday()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'session_id' => 'Sesi olahraga sudah lewat tanggal pelaksanaannya.',
            ]);
        }

        // (c) Check existing permission requests for the same session
        $existing = PermissionRequest::where('session_id', $session->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            if (in_array($existing->status, ['pending', 'approved'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'session_id' => 'Anda sudah memiliki permohonan izin dengan status ' . ($existing->status === 'approved' ? 'disetujui' : 'menunggu peninjauan') . ' untuk sesi ini.',
                ]);
            }

            // Existing status is 'rejected' -> allow resubmission by updating existing record
            $proofPath = $existing->proof;
            if ($proof) {
                $proofPath = $proof->store('permissions', 'local');
            }

            $existing->update([
                'reason' => $data['reason'],
                'proof' => $proofPath,
                'status' => 'pending',
                'review_note' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);

            AuditLogService::log(
                action: 'request_permission_resubmit',
                targetType: 'PermissionRequest',
                targetId: $existing->id,
                description: "Member {$user->name} mengajukan ulang izin untuk sesi #{$session->id}",
                userId: $user->id
            );

            return $existing;
        }

        $proofPath = null;
        if ($proof) {
            $proofPath = $proof->store('permissions', 'local');
        }

        try {
            $permission = PermissionRequest::create([
                'session_id' => $session->id,
                'user_id' => $user->id,
                'reason' => $data['reason'],
                'proof' => $proofPath,
                'status' => 'pending',
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'session_id' => 'Permohonan izin untuk sesi ini sudah ada.',
            ]);
        }

        AuditLogService::log(
            action: 'request_permission',
            targetType: 'PermissionRequest',
            targetId: $permission->id,
            description: "Member {$user->name} mengajukan izin untuk sesi #{$permission->session_id}",
            userId: $user->id
        );

        return $permission;
    }

    /**
     * Cancel a pending permission request by member.
     */
    public function cancelPendingPermission(PermissionRequest $permission, User $user): bool
    {
        if ($permission->user_id !== $user->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        if ($permission->status !== 'pending') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'permission' => 'Hanya permohonan izin berstatus pending yang dapat dibatalkan.',
            ]);
        }

        AuditLogService::log(
            action: 'cancel_permission_request',
            targetType: 'PermissionRequest',
            targetId: $permission->id,
            description: "Member {$user->name} membatalkan pengajuan izin untuk sesi #{$permission->session_id}",
            userId: $user->id
        );

        return (bool) $permission->delete();
    }
}

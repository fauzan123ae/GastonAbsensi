<?php

namespace App\Services;

use App\Models\SportSession;
use App\Models\User;

class SessionService
{
    /**
     * Get sessions list with participant & attendance aggregates and filters.
     */
    public function getPaginatedSessions(array $filters, int $perPage = 10)
    {
        $sportId = $filters['sport_id'] ?? null;
        $locationId = $filters['location_id'] ?? null;
        $status = $filters['status'] ?? null;
        $date = $filters['date'] ?? null;

        $query = SportSession::with(['sport', 'location', 'creator'])
            ->withCount([
                'participants as registered_count' => function ($q) {
                    $q->where('status', 'registered');
                },
                'attendances as present_count' => function ($q) {
                    $q->where('status', 'present');
                },
            ]);

        if ($sportId) {
            $query->where('sport_id', $sportId);
        }
        if ($locationId) {
            $query->where('location_id', $locationId);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($date) {
            $query->whereDate('date', $date);
        }

        return $query->orderBy('date', 'desc')->orderBy('start_time', 'desc')->paginate($perPage)->withQueryString();
    }

    /**
     * Create a new sport session with audit log.
     */
    public function createSession(array $data, User $creator): SportSession
    {
        $data['created_by'] = $creator->id;
        $session = SportSession::create($data);

        AuditLogService::log(
            action: 'create_session',
            targetType: 'SportSession',
            targetId: $session->id,
            description: "Admin membuat sesi olahraga: {$session->sport->name} di {$session->location->name} ({$session->date->format('d M Y')})",
            newValues: $data,
            userId: $creator->id
        );

        return $session;
    }

    /**
     * Update an existing sport session.
     */
    public function updateSession(SportSession $session, array $data, ?User $actor = null): SportSession
    {
        $hasAttendances = $session->attendances()->exists();

        // If session has attendances, disallow modifying date or setting status to completed via edit form
        if ($hasAttendances) {
            $newDate = isset($data['date']) ? \Illuminate\Support\Carbon::parse($data['date'])->toDateString() : null;
            $currentDate = $session->date ? $session->date->toDateString() : null;
            if ($newDate && $newDate !== $currentDate) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'date' => 'Tanggal sesi tidak dapat diubah karena sudah memiliki catatan kehadiran.',
                ]);
            }

            if (isset($data['status']) && $data['status'] === 'completed' && $session->status !== 'completed') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' => 'Status sesi tidak dapat diubah menjadi completed lewat form edit. Gunakan alur selesai sesi.',
                ]);
            }
        }

        // Status change must follow updateStatus rules if status is being updated
        if (isset($data['status']) && $data['status'] !== $session->status) {
            $this->updateStatus($session, $data['status'], $actor);
            unset($data['status']);
        }

        $oldValues = $session->only(['sport_id', 'location_id', 'date', 'start_time', 'end_time', 'status', 'notes']);
        $session->update($data);

        AuditLogService::log(
            action: 'update_session',
            targetType: 'SportSession',
            targetId: $session->id,
            description: "Admin memperbarui data sesi #{$session->id}",
            oldValues: $oldValues,
            newValues: $data,
            userId: $actor?->id
        );

        return $session;
    }

    /**
     * Update session status with valid transitions:
     * - draft -> open -> ongoing -> completed
     * - cancelled only from draft, open, ongoing
     * - completed and cancelled cannot be reverted
     */
    public function updateStatus(SportSession $session, string $newStatus, ?User $actor = null): string
    {
        $oldStatus = $session->status;

        if ($oldStatus === $newStatus) {
            return $session->status;
        }

        $validTransitions = [
            'draft' => ['open', 'cancelled'],
            'open' => ['ongoing', 'cancelled'],
            'ongoing' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        $allowed = $validTransitions[$oldStatus] ?? [];
        if (!in_array($newStatus, $allowed, true)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'status' => "Transisi status dari '{$oldStatus}' ke '{$newStatus}' tidak diizinkan.",
            ]);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($session, $oldStatus, $newStatus, $actor) {
            $session->status = $newStatus;
            $session->save();

            // When session is cancelled, auto-reject pending permission requests
            if ($newStatus === 'cancelled') {
                $session->permissionRequests()->where('status', 'pending')->update([
                    'status' => 'rejected',
                    'review_note' => 'Sesi olahraga dibatalkan oleh admin.',
                    'reviewed_by' => $actor?->id,
                    'reviewed_at' => now(),
                ]);
            }

            AuditLogService::log(
                action: 'change_session_status',
                targetType: 'SportSession',
                targetId: $session->id,
                description: "Admin mengubah status sesi #{$session->id} dari {$oldStatus} menjadi {$session->status}",
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => $session->status],
                userId: $actor?->id
            );
        });

        return $session->status;
    }
}

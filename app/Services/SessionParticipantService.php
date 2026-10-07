<?php

namespace App\Services;

use App\Models\SessionParticipant;
use App\Models\SportSession;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class SessionParticipantService
{
    /**
     * Add multiple members to a sport session.
     */
    public function addParticipants(SportSession $session, array $userIds, string $source = 'manual', ?User $actor = null): int
    {
        $addedCount = 0;

        DB::transaction(function () use ($session, $userIds, $source, &$addedCount) {
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user && $user->isActive()) {
                    $participant = SessionParticipant::firstOrCreate(
                        ['session_id' => $session->id, 'user_id' => $userId],
                        ['registration_source' => $source, 'status' => 'registered', 'registered_at' => now()]
                    );

                    if ($participant->wasRecentlyCreated) {
                        $addedCount++;
                    }
                }
            }
        });

        AuditLogService::log(
            action: 'add_participants',
            targetType: 'SportSession',
            targetId: $session->id,
            description: "Admin menambahkan {$addedCount} peserta ke sesi #{$session->id}",
            userId: $actor?->id
        );

        return $addedCount;
    }

    /**
     * Remove a participant from a sport session if they have not checked in.
     */
    public function removeParticipant(SportSession $session, SessionParticipant $participant, ?User $actor = null): array
    {
        $hasAttendance = $session->attendances()
            ->where('user_id', $participant->user_id)
            ->where('status', 'present')
            ->exists();

        if ($hasAttendance) {
            return [
                'success' => false,
                'message' => 'Peserta sudah melakukan absensi hadir, tidak dapat dihapus dari daftar peserta.',
            ];
        }

        $userName = $participant->user->name ?? 'User';
        $participant->delete();

        AuditLogService::log(
            action: 'remove_participant',
            targetType: 'SportSession',
            targetId: $session->id,
            description: "Admin menghapus peserta {$userName} dari sesi #{$session->id}",
            userId: $actor?->id
        );

        return [
            'success' => true,
            'message' => "Peserta {$userName} berhasil dihapus dari sesi.",
        ];
    }

    /**
     * Import participants from CSV / TXT file.
     */
    public function importFromCsv(SportSession $session, UploadedFile $file, string $source = 'whatsapp', ?User $actor = null): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $importedCount = 0;
        $failedRows = [];
        $rowNum = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNum++;
            if ($rowNum === 1 && (strtolower(trim($row[0])) === 'member_id' || strtolower(trim($row[0])) === 'email')) {
                continue; // Skip header
            }

            $rawIdentifier = trim($row[0] ?? '');
            if (empty($rawIdentifier)) {
                continue;
            }

            $user = User::where('member_id', $rawIdentifier)
                ->orWhere('email', $rawIdentifier)
                ->first();

            if (!$user) {
                $failedRows[] = "{$rawIdentifier} → Member tidak ditemukan";
                continue;
            }

            if (!$user->isActive()) {
                $failedRows[] = "{$rawIdentifier} ({$user->name}) → Member tidak aktif";
                continue;
            }

            $existing = SessionParticipant::where('session_id', $session->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                continue; // Already registered
            }

            SessionParticipant::create([
                'session_id' => $session->id,
                'user_id' => $user->id,
                'registration_source' => $source,
                'status' => 'registered',
                'registered_at' => now(),
            ]);

            $importedCount++;
        }

        fclose($handle);

        AuditLogService::log(
            action: 'import_participants',
            targetType: 'SportSession',
            targetId: $session->id,
            description: "Admin mengimpor {$importedCount} peserta ke sesi #{$session->id}. Gagal: " . count($failedRows),
            userId: $actor?->id
        );

        return [
            'imported_count' => $importedCount,
            'failed_rows' => $failedRows,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberService
{
    /**
     * Get paginated members with search and status filters.
     */
    public function getPaginatedMembers(?string $search = null, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = User::where('role', 'member');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status && in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

    /**
     * Create a new member with generated ID, QR token, and audit log.
     */
    public function createMember(array $data, ?UploadedFile $photo = null, ?User $actor = null): User
    {
        $photoPath = null;
        if ($photo) {
            $photoPath = $photo->store('members', 'public');
        }

        $plainPassword = null;
        if (!empty($data['password'])) {
            $password = Hash::make($data['password']);
        } else {
            $plainPassword = Str::password(12);
            $password = Hash::make($plainPassword);
        }

        $maxAttempts = 3;
        $attempt = 0;
        $member = null;

        while ($attempt < $maxAttempts) {
            $attempt++;
            $memberId = User::generateNextMemberId();
            $qrToken = User::generateQrToken();

            try {
                $member = User::create([
                    'member_id' => $memberId,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => $password,
                    'photo' => $photoPath,
                    'qr_token' => $qrToken,
                    'role' => 'member',
                    'status' => $data['status'] ?? 'active',
                ]);
                break;
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                if ($attempt >= $maxAttempts || !str_contains($e->getMessage(), 'member_id')) {
                    throw $e;
                }
                usleep(50000); // 50ms jitter
            }
        }

        if ($plainPassword) {
            $member->generated_password = $plainPassword;
        }

        AuditLogService::log(
            action: 'create_member',
            targetType: 'User',
            targetId: $member->id,
            description: "Admin membuat member baru: {$member->name} ({$member->member_id})",
            newValues: [
                'member_id' => $member->member_id,
                'name' => $member->name,
                'email' => $member->email,
            ],
            userId: $actor?->id
        );

        return $member;
    }

    /**
     * Update an existing member.
     */
    public function updateMember(User $member, array $data, ?UploadedFile $photo = null, ?User $actor = null): User
    {
        $oldValues = $member->only(['name', 'email', 'phone', 'status']);

        if ($photo) {
            if ($member->photo && Storage::disk('public')->exists($member->photo)) {
                Storage::disk('public')->delete($member->photo);
            }
            $member->photo = $photo->store('members', 'public');
        }

        $member->name = $data['name'];
        $member->email = $data['email'];
        $member->phone = $data['phone'] ?? null;
        $member->status = $data['status'];

        if (!empty($data['password'])) {
            $member->password = Hash::make($data['password']);
        }

        $member->save();

        AuditLogService::log(
            action: 'update_member',
            targetType: 'User',
            targetId: $member->id,
            description: "Admin memperbarui data member {$member->name} ({$member->member_id})",
            oldValues: $oldValues,
            newValues: $member->only(['name', 'email', 'phone', 'status']),
            userId: $actor?->id
        );

        return $member;
    }

    /**
     * Toggle active/inactive status of a member.
     */
    public function toggleStatus(User $member, ?User $actor = null): string
    {
        $oldStatus = $member->status;
        $member->status = ($oldStatus === 'active') ? 'inactive' : 'active';
        $member->save();

        AuditLogService::log(
            action: 'toggle_member_status',
            targetType: 'User',
            targetId: $member->id,
            description: "Admin mengubah status member {$member->name} dari {$oldStatus} menjadi {$member->status}",
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $member->status],
            userId: $actor?->id
        );

        return $member->status;
    }
}

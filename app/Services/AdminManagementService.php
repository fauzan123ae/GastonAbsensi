<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminManagementService
{
    /**
     * Get paginated admin and master admin accounts.
     */
    public function getPaginatedAdmins(?string $search = null, ?string $role = null, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = User::whereIn('role', ['admin', 'master_admin']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role && in_array($role, ['admin', 'master_admin'])) {
            $query->where('role', $role);
        }

        if ($status && in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }

        return $query->orderByRaw("CASE WHEN role = 'master_admin' THEN 1 WHEN role = 'admin' THEN 2 ELSE 3 END")
            ->orderBy('name', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new admin or master admin user.
     */
    public function createAdmin(array $data, ?UploadedFile $photo = null, ?User $actor = null): User
    {
        $photoPath = null;
        if ($photo) {
            $photoPath = $photo->store('admins', 'public');
        }

        $admin = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'status' => $data['status'],
            'photo' => $photoPath,
        ]);

        AuditLogService::log(
            action: 'create_admin',
            targetType: 'User',
            targetId: $admin->id,
            description: "Master Admin (" . ($actor?->name ?? 'System') . ") menambahkan " . ($admin->role === 'master_admin' ? 'Master Admin' : 'Admin') . " baru: {$admin->name} ({$admin->email})",
            newValues: [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => $admin->role,
                'status' => $admin->status,
            ],
            userId: $actor?->id
        );

        return $admin;
    }

    /**
     * Update an admin user with role and audit logging.
     */
    public function updateAdmin(User $admin, array $data, ?UploadedFile $photo = null, ?User $actor = null): User
    {
        $oldValues = $admin->only(['name', 'email', 'phone', 'role', 'status']);

        if ($photo) {
            if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
                Storage::disk('public')->delete($admin->photo);
            }
            $admin->photo = $photo->store('admins', 'public');
        }

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->phone = $data['phone'] ?? null;
        $admin->role = $data['role'];
        $admin->status = $data['status'];

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        AuditLogService::log(
            action: 'update_admin',
            targetType: 'User',
            targetId: $admin->id,
            description: "Master Admin (" . ($actor?->name ?? 'System') . ") memperbarui akun admin {$admin->name} ({$admin->email})",
            oldValues: $oldValues,
            newValues: $admin->only(['name', 'email', 'phone', 'role', 'status']),
            userId: $actor?->id
        );

        return $admin;
    }

    /**
     * Toggle active/inactive status for an admin user.
     */
    public function toggleStatus(User $admin, ?User $actor = null): string
    {
        $oldStatus = $admin->status;
        $admin->status = ($oldStatus === 'active') ? 'inactive' : 'active';
        $admin->save();

        AuditLogService::log(
            action: 'toggle_admin_status',
            targetType: 'User',
            targetId: $admin->id,
            description: "Master Admin (" . ($actor?->name ?? 'System') . ") mengubah status admin {$admin->name} dari {$oldStatus} ke {$admin->status}",
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $admin->status],
            userId: $actor?->id
        );

        return $admin->status;
    }

    /**
     * Delete an admin account.
     */
    public function deleteAdmin(User $admin, ?User $actor = null): void
    {
        $name = $admin->name;
        $email = $admin->email;

        if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
            Storage::disk('public')->delete($admin->photo);
        }

        $admin->delete();

        AuditLogService::log(
            action: 'delete_admin',
            targetType: 'User',
            targetId: $admin->id,
            description: "Master Admin (" . ($actor?->name ?? 'System') . ") menghapus akun admin: {$name} ({$email})",
            oldValues: ['name' => $name, 'email' => $email],
            userId: $actor?->id
        );
    }
}

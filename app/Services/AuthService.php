<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthService
{
    /**
     * Authenticate user credentials and validate active status.
     * Returns array with:
     * - success: bool
     * - error_field: string|null ('email' or null)
     * - error_message: string|null
     * - user: User|null
     */
    public function attemptLogin(string $identifier, string $password, bool $remember = false): array
    {
        $loginField = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'member_id';

        if (Auth::attempt([$loginField => $identifier, 'password' => $password], $remember)) {
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();

                return [
                    'success' => false,
                    'error_field' => 'email',
                    'error_message' => 'Akun Anda berstatus nonaktif. Silakan hubungi admin.',
                    'user' => null,
                ];
            }

            request()->session()->regenerate();

            return [
                'success' => true,
                'error_field' => null,
                'error_message' => null,
                'user' => $user,
            ];
        }

        return [
            'success' => false,
            'error_field' => 'email',
            'error_message' => 'Kombinasi email/Member ID dan password tidak sesuai.',
            'user' => null,
        ];
    }

    /**
     * Perform user logout and invalidate session.
     */
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Self-register a new member account with automatic Member ID & permanent QR token.
     */
    public function registerMember(array $data, ?UploadedFile $photo = null): User
    {
        $photoPath = null;
        if ($photo) {
            $photoPath = $photo->store('members', 'public');
        }

        $maxAttempts = 3;
        $attempt = 0;
        $user = null;

        while ($attempt < $maxAttempts) {
            $attempt++;
            $memberId = User::generateNextMemberId();
            $qrToken = User::generateQrToken();

            try {
                $user = User::create([
                    'member_id' => $memberId,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => $data['password'], // hashed by User model casts
                    'photo' => $photoPath,
                    'qr_token' => $qrToken,
                    'role' => 'member',
                    'status' => 'active',
                ]);
                break;
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // If it's member_id violation and we have attempts left, retry; otherwise rethrow
                if ($attempt >= $maxAttempts || !str_contains($e->getMessage(), 'member_id')) {
                    throw $e;
                }
                usleep(50000); // 50ms jitter
            }
        }

        AuditLogService::log(
            action: 'member_self_register',
            targetType: 'User',
            targetId: $user->id,
            description: "Member baru mendaftar mandiri: {$user->name} ({$user->member_id})",
            userId: $user->id
        );

        Auth::login($user);
        request()->session()->regenerate();

        return $user;
    }

    /**
     * Update member's own profile.
     */
    public function updateProfile(User $user, array $data, ?UploadedFile $photo = null): User
    {
        $oldValues = $user->only(['name', 'email', 'phone']);

        if ($photo) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $photo->store('members', 'public');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        AuditLogService::log(
            action: 'update_own_profile',
            targetType: 'User',
            targetId: $user->id,
            description: "Member {$user->name} memperbarui profil pribadi",
            oldValues: $oldValues,
            newValues: $user->only(['name', 'email', 'phone']),
            userId: $user->id
        );

        return $user;
    }
}

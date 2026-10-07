<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'member_id',
        'name',
        'email',
        'phone',
        'password',
        'photo',
        'qr_token',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isMasterAdmin(): bool
    {
        return $this->role === 'master_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'master_admin']);
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function createdSessions()
    {
        return $this->hasMany(SportSession::class, 'created_by');
    }

    public function sessionParticipants()
    {
        return $this->hasMany(SessionParticipant::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function checkedInAttendances()
    {
        return $this->hasMany(Attendance::class, 'checked_in_by');
    }

    public function permissionRequests()
    {
        return $this->hasMany(PermissionRequest::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Helper to generate standardized Member ID (e.g. GST-00001)
     */
    public static function generateNextMemberId(): string
    {
        // Extract all member_ids matching GST-\d{5} format and compute maximum numeric value
        $users = self::whereNotNull('member_id')
            ->where('member_id', 'LIKE', 'GST-%')
            ->pluck('member_id');

        $maxNumber = 0;
        foreach ($users as $mid) {
            if (preg_match('/^GST-(\d{5})$/', $mid, $matches)) {
                $num = intval($matches[1]);
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        return sprintf('GST-%05d', $maxNumber + 1);
    }

    /**
     * Helper to generate secure opaque QR token (e.g. GST-MBR-X8A92KDK29)
     */
    public static function generateQrToken(): string
    {
        do {
            $token = 'GST-MBR-' . strtoupper(\Illuminate\Support\Str::random(10));
        } while (self::where('qr_token', $token)->exists());

        return $token;
    }
}

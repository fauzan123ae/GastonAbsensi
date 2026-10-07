<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportSession extends Model
{
    use HasFactory;

    protected $table = 'sport_sessions';

    protected $fillable = [
        'sport_id',
        'location_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(SessionParticipant::class, 'session_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    public function permissionRequests()
    {
        return $this->hasMany(PermissionRequest::class, 'session_id');
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canAcceptAttendance(): bool
    {
        $todayJakarta = now('Asia/Jakarta')->toDateString();
        $sessionDate = $this->date ? $this->date->toDateString() : null;

        return in_array($this->status, ['open', 'ongoing']) && $sessionDate === $todayJakarta;
    }
}

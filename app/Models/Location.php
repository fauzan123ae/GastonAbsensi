<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'description',
        'status',
    ];

    public function sessions()
    {
        return $this->hasMany(SportSession::class, 'location_id');
    }
}

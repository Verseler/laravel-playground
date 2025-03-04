<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'check_in',
        'check_out',
        'total_male',
        'total_female',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    /** @use HasFactory<\Database\Factories\GuestFactory> */
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    /** @use HasFactory<\Database\Factories\GuestFactory> */
    use HasFactory;

    protected $fillable = ['display_name', 'gender', 'reservation_id', 'bed_id'];

    public function reservation()
    {
        $this->belongsTo(Reservation::class);
    }

    public function bed()
    {
        $this->belongsTo(Bed::class);
    }
}

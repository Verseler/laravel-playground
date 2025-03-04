<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Bed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function store(Request $request)
    {

        // Validate both guest and reservation details
        $validated = $request->validate([
            // Guest details
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:guests,email',
            'phone' => 'required|string|max:50',
            // Reservation details
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'total_male' => 'required|integer|min:0',
            'total_female' => 'required|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Create Guest record
                $guest = Guest::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ]);

                // Create Reservation record
                $reservation = Reservation::create([
                    'guest_id' => $guest->id,
                    'check_in' => $validated['check_in'],
                    'check_out' => $validated['check_out'],
                    'total_male' => $validated['total_male'],
                    'total_female' => $validated['total_female'],
                ]);

                // --- Assign Beds for Male Guests ---
                for ($i = 0; $i < $validated['total_male']; $i++) {
                    $bed = Bed::whereNull('reservation_id')
                        ->whereHas('room', function ($query) {
                            $query->whereNull('designated_gender')
                                ->orWhere('designated_gender', 'male');
                        })->first();

                    if (!$bed) {
                        throw new \Exception("No available bed for a male guest.");
                    }

                    // If the room's gender is not set, designate it as male.
                    if ($bed->room->designated_gender == null) {
                        $bed->room->update(['designated_gender' => 'male']);
                    }

                    // Assign the bed to this reservation.
                    $bed->update(['reservation_id' => $reservation->id]);
                }

                // --- Assign Beds for Female Guests ---
                for ($i = 0; $i < $validated['total_female']; $i++) {
                    $bed = Bed::whereNull('reservation_id')
                        ->whereHas('room', function ($query) {
                            $query->whereNull('designated_gender')
                                ->orWhere('designated_gender', 'female');
                        })->first();

                    if (!$bed) {
                        throw new \Exception("No available bed for a female guest.");
                    }

                    if ($bed->room->designated_gender == null) {
                        $bed->room->update(['designated_gender' => 'female']);
                    }

                    $bed->update(['reservation_id' => $reservation->id]);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return redirect()->back()->with('success', 'Reservation successfully created and beds assigned!');
    }
}

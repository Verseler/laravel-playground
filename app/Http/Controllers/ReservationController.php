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
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'total_male' => 'required|integer|min:0',
            'total_female' => 'required|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Create Reservation
                $reservation = Reservation::create([
                    'check_in_date' => $validated['check_in_date'],
                    'check_out_date' => $validated['check_out_date'],
                    'total_male' => $validated['total_male'],
                    'total_female' => $validated['total_female'],
                    'status' => 'pending',
                    //'total_amount' => 0, // Will be calculated later
                    //'current_balance' => 0,
                    //'first_name' => $validated['first_name'],
                    //'middle_initial' => $validated['middle_initial'] ?? null,
                    //'last_name' => $validated['last_name'],
                    //'phone' => $validated['phone'],
                    //'email' => $validated['email'] ?? null,
                    //'office' => $validated['office'],
                    //'purpose_of_stay' => $validated['purpose_of_stay'],
                ]);

                // --- Assign Guests and Beds ---
                $this->assignBeds($validated['total_male'], 'male', $reservation);
                $this->assignBeds($validated['total_female'], 'female', $reservation);

            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
        dd('success');
        return redirect()->back()->with('success', 'Reservation successfully created and beds assigned!');
    }

    private function assignBeds(int $count, string $gender, Reservation $reservation): void
    {
        if ($count === 0) {
            return;
        }


        $beds = Bed::where('status', 'available')
            ->whereHas('room', function ($query) use ($gender) {
                $query->whereIn('eligible_gender', ['any', $gender]);
            })
            ->take($count)
            ->get();

        if ($beds->count() < $count) {
            throw new \Exception("Not enough available beds for {$gender} guests.");
        }

        foreach ($beds as $bed) {
            $room = $bed->room;

            if ($room->eligible_gender === 'any') {
                $room->update(['eligible_gender' => $gender]);
            }

            Guest::create([
                'display_name' => "{$reservation->first_name} {$reservation->last_name}",
                'gender' => $gender,
                'reservation_id' => $reservation->id,
                'bed_id' => $bed->id,
                'office_id' => null,
            ]);

            $bed->update([
                'reservation_id' => $reservation->id,
                'status' => 'occupied',
            ]);
        }
    }
}

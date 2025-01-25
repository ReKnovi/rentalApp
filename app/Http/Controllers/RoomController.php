<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
        public function highlightRoom(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        // Check if the authenticated user owns the room
        if ($room->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:30', // Allow highlighting for 1 to 30 days
        ]);

        $highlightDuration = now()->addDays($validated['days']);

        $room->highlighted_until = $highlightDuration;
        $room->save();

        return response()->json(['message' => 'Room highlighted successfully', 'highlighted_until' => $highlightDuration]);
    }

    public function updateRentalRules(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        // Ensure the authenticated user owns the room
        if ($room->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'rental_rules' => 'required|array', // Expect an array of rules
            'rental_rules.*' => 'string|max:255', // Each rule must be a string
        ]);

        $room->rental_rules = $validated['rental_rules'];
        $room->save();

        return response()->json(['message' => 'Rental rules updated successfully', 'rental_rules' => $room->rental_rules]);
    }


}

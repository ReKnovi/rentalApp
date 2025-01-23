<?php

// app/Http/Controllers/LandlordController.php
namespace App\Http\Controllers;

use App\Models\Room;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use Exception;

class LandlordController extends Controller
{
    // Add a new room
    public function store(StoreRoomRequest $request)
    {
        try {
            $validated = $request->validated();

            $room = Room::create(array_merge(
                $validated,
                ['user_id' => auth()->id()]
            ));


            return response()->json(['message' => 'Room created successfully', 'room' => $room], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create room.', 'message' => $e->getMessage()], 500);
        }
    }

    // View landlord's rooms
    public function index()
    {
        try {
            $rooms = Room::where('user_id', auth()->id())->get();
            return response()->json(['rooms' => $rooms]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve rooms.', 'message' => $e->getMessage()], 500);
        }
    }

    // Update a room
    public function update(UpdateRoomRequest $request, $id)
    {
        try {
            $room = Room::where('id', $id)->where('user_id', auth()->id())->first();

            if (!$room) {
                return response()->json(['error' => 'Room not found'], 404);
            }

            $room->update($request->validated());

            return response()->json(['message' => 'Room updated successfully', 'room' => $room]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update room.', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a room
    public function destroy($id)
    {
        try {
            $room = Room::where('id', $id)->where('user_id', auth()->id())->first();

            if (!$room) {
                return response()->json(['error' => 'Room not found'], 404);
            }

            $room->delete();

            return response()->json(['message' => 'Room deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete room.', 'message' => $e->getMessage()], 500);
        }
    }
}

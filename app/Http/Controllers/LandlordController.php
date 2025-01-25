<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\RoomImage;
use Illuminate\Support\Facades\DB;
use Exception;

class LandlordController extends Controller
{
    // Add a new room
    public function store(StoreRoomRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $room = Room::create(array_merge(
                $validated,
                ['user_id' => auth()->id()]
            ));

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('room_images', 'public'); // Store in the "public" disk
                    RoomImage::create([
                        'room_id' => $room->id,
                        'image_path' => $path,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Room created successfully', 'room' => $room], 201);
        } catch (Exception $e) {
            DB::rollBack();
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
        DB::beginTransaction();
        try {
            $room = Room::where('id', $id)->where('user_id', auth()->id())->first();

            if (!$room) {
                return response()->json(['error' => 'Room not found'], 404);
            }

            $room->update($request->validated());

            DB::commit();
            return response()->json(['message' => 'Room updated successfully', 'room' => $room]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update room.', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a room
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $room = Room::where('id', $id)->where('user_id', auth()->id())->first();

            if (!$room) {
                return response()->json(['error' => 'Room not found'], 404);
            }

            $room->delete();

            DB::commit();
            return response()->json(['message' => 'Room deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete room.', 'message' => $e->getMessage()], 500);
        }
    }
}

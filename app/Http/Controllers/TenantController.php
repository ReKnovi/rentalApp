<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class TenantController extends Controller
{
    // Browse Rooms
    public function browseRooms(Request $request)
    {
        try {
            $filters = $request->only([
                'location', 'price_min', 'price_max', 'amenities', 'rent_type',
                'bedrooms', 'bathrooms', 'shared_space', 'furnished', 'parking_available',
                'pets_allowed', 'floor', 'utilities_included', 'condition'
            ]);

            $query = Room::query()->where('status', 'available');

            // Location Filter
            if (isset($filters['location'])) {
                $query->where('location', 'like', '%' . $filters['location'] . '%');
            }

            // Price Filters
            if (isset($filters['price_min'])) {
                $query->where('price', '>=', $filters['price_min']);
            }
            if (isset($filters['price_max'])) {
                $query->where('price', '<=', $filters['price_max']);
            }

            // Rent Type Filter
            if (isset($filters['rent_type'])) {
                $query->where('rent_type', $filters['rent_type']);
            }

            // Amenities Filter
            if (isset($filters['amenities'])) {
                foreach (json_decode($filters['amenities'], true) as $amenity) {
                    $query->whereJsonContains('amenities', $amenity);
                }
            }

            // Bedrooms and Bathrooms Filters
            if (isset($filters['bedrooms'])) {
                $query->where('bedrooms', '>=', $filters['bedrooms']);
            }
            if (isset($filters['bathrooms'])) {
                $query->where('bathrooms', '>=', $filters['bathrooms']);
            }

            // Shared Space Filter
            if (isset($filters['shared_space'])) {
                $query->where('shared_space', $filters['shared_space']);
            }

            // Furnished Filter
            if (isset($filters['furnished'])) {
                $query->where('furnished', $filters['furnished']);
            }

            // Parking Available Filter
            if (isset($filters['parking_available'])) {
                $query->where('parking_available', $filters['parking_available']);
            }

            // Pets Allowed Filter
            if (isset($filters['pets_allowed'])) {
                $query->where('pets_allowed', $filters['pets_allowed']);
            }

            // Floor Filter
            if (isset($filters['floor'])) {
                $query->where('floor', '>=', $filters['floor']);
            }

            // Utilities Included Filter
            if (isset($filters['utilities_included'])) {
                $query->where('utilities_included', $filters['utilities_included']);
            }

            // Room Condition Filter
            if (isset($filters['condition'])) {
                $query->where('condition', 'like', '%' . $filters['condition'] . '%');
            }

            // Prioritize highlighted rooms
            $query->orderByRaw("highlighted_until IS NOT NULL DESC, highlighted_until DESC");

            return response()->json($query->paginate(10));
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to browse rooms.', 'message' => $e->getMessage()], 500);
        }
    }

    // Apply for a Room
    public function applyForRoom(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'message' => 'nullable|string',
            ]);

            $tenantId = Auth::id();
            $room = Room::findOrFail($request->room_id);

            if ($room->is_sharable && $room->freeSpots() <= 0) {
                return response()->json(['error' => 'No free spots available for this room'], 400);
            }

            $application = Application::create([
                'tenant_id' => $tenantId,
                'room_id' => $room->id,
                'message' => $request->message,
                'is_approved' => false, // Default to pending
            ]);

            DB::commit();
            return response()->json(['message' => 'Application submitted successfully', 'application' => $application]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to apply for room.', 'message' => $e->getMessage()], 500);
        }
    }

    // Get Applications
    public function getApplications()
    {
        try {
            $tenantId = Auth::id();
            $applications = Application::with('room')->where('tenant_id', $tenantId)->get();
            return response()->json($applications);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve applications.', 'message' => $e->getMessage()], 500);
        }
    }

    // Get Saved Rooms
    public function getSavedRooms()
    {
        try {
            $tenantId = Auth::id();
            $savedRooms = Auth::user()->savedRooms; // Assuming a relationship is defined
            return response()->json($savedRooms);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve saved rooms.', 'message' => $e->getMessage()], 500);
        }
    }

    // Save Room
    public function saveRoom(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'room_id' => 'required|exists:rooms,id',
            ]);

            $user = Auth::user();
            $user->savedRooms()->attach($request->room_id); // Assuming a pivot table

            DB::commit();
            return response()->json(['message' => 'Room saved successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to save room.', 'message' => $e->getMessage()], 500);
        }
    }

    // Remove Saved Room
    public function removeSavedRoom(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'room_id' => 'required|exists:rooms,id',
            ]);

            $user = Auth::user();
            $user->savedRooms()->detach($request->room_id); // Assuming a pivot table

            DB::commit();
            return response()->json(['message' => 'Room removed from saved list']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to remove saved room.', 'message' => $e->getMessage()], 500);
        }
    }
}

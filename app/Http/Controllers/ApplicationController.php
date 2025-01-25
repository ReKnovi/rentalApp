<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ApplicationController extends Controller
{
    public function accept($applicationId)
    {
        DB::beginTransaction();
        try {
            $application = Application::findOrFail($applicationId);

            // Check if the application belongs to the landlord's room
            $room = $application->room;
            if ($room->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Ensure room is sharable and has free spots
            if ($room->is_sharable && $room->freeSpots() <= 0) {
                return response()->json(['error' => 'No free spots available'], 400);
            }

            // Approve the application
            $application->is_approved = true;
            $application->save();

            DB::commit();
            return response()->json(['message' => 'Application accepted successfully', 'application' => $application]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to accept application.', 'message' => $e->getMessage()], 500);
        }
    }

    public function reject($applicationId)
    {
        DB::beginTransaction();
        try {
            $application = Application::findOrFail($applicationId);

            // Check if the application belongs to the landlord's room
            $room = $application->room;
            if ($room->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Reject the application
            $application->delete();

            DB::commit();
            return response()->json(['message' => 'Application rejected and removed']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to reject application.', 'message' => $e->getMessage()], 500);
        }
    }
}

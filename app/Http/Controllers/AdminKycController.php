<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kyc;
use Exception;

class AdminKycController extends Controller
{
    // Get all pending KYC requests
    public function getPending()
    {
        try {
            $pendingKycs = Kyc::where('status', 'pending')->with('user')->get();
            return response()->json(['pending_kycs' => $pendingKycs]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve pending KYC requests.', 'message' => $e->getMessage()], 500);
        }
    }

    // Approve KYC
    public function approve($kyc_id)
    {
        try {
            $kyc = Kyc::findOrFail($kyc_id);

            $kyc->status = 'approved';
            $kyc->save();

            return response()->json(['message' => 'KYC approved successfully.', 'kyc' => $kyc]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to approve KYC.', 'message' => $e->getMessage()], 500);
        }
    }

    // Reject KYC
    public function reject(Request $request, $kyc_id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:255',
            ]);

            $kyc = Kyc::findOrFail($kyc_id);

            $kyc->status = 'rejected';
            $kyc->rejection_reason = $request->reason; // Add a rejection reason
            $kyc->save();

            return response()->json(['message' => 'KYC rejected.', 'kyc' => $kyc]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to reject KYC.', 'message' => $e->getMessage()], 500);
        }
    }
}

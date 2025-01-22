<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kyc;

class AdminKycController extends Controller
{
    // Get all pending KYC requests
    public function getPending()
    {
        $pendingKycs = Kyc::where('status', 'pending')->with('user')->get();

        return response()->json(['pending_kycs' => $pendingKycs]);
    }

    // Approve KYC
    public function approve($kyc_id)
    {
        $kyc = Kyc::findOrFail($kyc_id);

        $kyc->status = 'approved';
        $kyc->save();

        return response()->json(['message' => 'KYC approved successfully.', 'kyc' => $kyc]);
    }

    // Reject KYC
    public function reject(Request $request, $kyc_id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $kyc = Kyc::findOrFail($kyc_id);

        $kyc->status = 'rejected';
        $kyc->rejection_reason = $request->reason; // Add a rejection reason
        $kyc->save();

        return response()->json(['message' => 'KYC rejected.', 'kyc' => $kyc]);
    }
}

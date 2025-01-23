<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Kyc;
use Exception;

class KycController extends Controller
{
    // Upload KYC document
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'document_type' => 'required|string|max:255', // e.g., Citizenship, Passport
                'document_number' => 'required|string|max:255',
                'document_file' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            ]);

            $user = auth()->user();

            // Store the file
            $filePath = $request->file('document_file')->store('kyc-documents', 'public');

            // Create a new KYC record
            $kyc = Kyc::create([
                'user_id' => $user->id,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'document_file' => $filePath,
                'status' => 'pending',
            ]);

            return response()->json(['message' => 'KYC document uploaded successfully.', 'kyc' => $kyc]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to upload KYC document.', 'message' => $e->getMessage()], 500);
        }
    }

    // Check KYC status
    public function status()
    {
        try {
            $user = auth()->user();
            $kyc = $user->kyc()->latest()->first();

            if (!$kyc) {
                return response()->json(['message' => 'No KYC records found.'], 404);
            }

            return response()->json(['kyc_status' => $kyc->status, 'kyc' => $kyc]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve KYC status.', 'message' => $e->getMessage()], 500);
        }
    }
}

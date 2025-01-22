<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Kyc;

class KycController extends Controller
{
    // Upload KYC document
    public function upload(Request $request)
    {
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
    }

    // Check KYC status
    public function status()
    {
        $user = auth()->user();
        $kyc = $user->kyc()->latest()->first();

        if (!$kyc) {
            return response()->json(['message' => 'No KYC records found.'], 404);
        }

        return response()->json(['kyc_status' => $kyc->status, 'kyc' => $kyc]);
    }
}

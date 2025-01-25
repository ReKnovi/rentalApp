<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ContractController extends Controller
{
    public function createContract(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'tenant_id' => 'required|exists:users,id',
                'contract_content' => 'required|string',
            ]);

            $room = Room::withCount(['contracts' => function ($query) {
                $query->where('status', '!=', 'completed');
            }])->findOrFail($validated['room_id']);

            // Check if room has free spots
            if ($room->is_sharable && $room->contracts_count >= $room->max_occupancy) {
                return response()->json(['error' => 'No free spots available'], 400);
            }

            // Create the contract
            $contract = Contract::create([
                'room_id' => $validated['room_id'],
                'landlord_id' => auth()->id(),
                'tenant_id' => $validated['tenant_id'],
                'contract_content' => $validated['contract_content'],
                'status' => 'pending',
            ]);

            DB::commit();
            return response()->json(['message' => 'Contract created successfully', 'contract' => $contract], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create contract.', 'message' => $e->getMessage()], 500);
        }
    }

    public function signContractAsTenant(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $contract = Contract::findOrFail($id);

            // Check if the authenticated user is the tenant
            if ($contract->tenant_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'signature' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Signature file
            ]);

            $path = $request->file('signature')->store('signatures', 'public');

            $contract->tenant_signature = $path;

            // Mark the contract as signed if the landlord has also signed
            if ($contract->landlord_signature) {
                $contract->status = 'signed';
            }

            $contract->save();

            DB::commit();
            return response()->json(['message' => 'Contract signed successfully', 'contract' => $contract]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to sign contract as tenant.', 'message' => $e->getMessage()], 500);
        }
    }

    public function signContractAsLandlord(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $contract = Contract::findOrFail($id);

            // Check if the authenticated user is the landlord
            if ($contract->landlord_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'signature' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Signature file
            ]);

            $path = $request->file('signature')->store('signatures', 'public');

            $contract->landlord_signature = $path;

            // Mark the contract as signed if the tenant has also signed
            if ($contract->tenant_signature) {
                $contract->status = 'signed';
            }

            $contract->save();

            DB::commit();
            return response()->json(['message' => 'Contract signed successfully', 'contract' => $contract]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to sign contract as landlord.', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $contract = Contract::with('room', 'landlord', 'tenant')->findOrFail($id);

            // Check if the user is involved in the contract
            if (!in_array(auth()->id(), [$contract->landlord_id, $contract->tenant_id])) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            return response()->json($contract);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve contract.', 'message' => $e->getMessage()], 500);
        }
    }
}

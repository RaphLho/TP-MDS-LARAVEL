<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use Illuminate\Support\Facades\Auth;
use App\Models\Boxes;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class ContractController extends Controller
{
    /**
     * Store a new contract template or update existing one.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'content' => 'required|string',
                'contract_id' => 'nullable|exists:contracts,id'
            ]);

            if ($request->contract_id) {
                $contract = Contract::findOrFail($request->contract_id);
                
                // Check if user owns this contract
                if ($contract->user_id !== Auth::id()) {
                    throw new \Exception('Unauthorized to update this contract');
                }

                $contract->update([
                    'name' => $request->name,
                    'content' => json_decode($request->content, true)
                ]);
            } else {
                $contract = Contract::create([
                    'user_id' => Auth::id(),
                    'name' => $request->name,
                    'content' => json_decode($request->content, true)
                ]);
            }

            if (!$contract) {
                throw new \Exception('Failed to save contract');
            }

            return response()->json([
                'success' => true,
                'message' => $request->contract_id ? 'Contract template updated successfully' : 'Contract template saved successfully',
                'contract' => $contract
            ]);

        } catch (\Exception $e) {
            Log::error('Contract operation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save contract template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a contract template.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $contract = Contract::findOrFail($id);

            // Check if user owns this contract
            if ($contract->user_id !== Auth::id()) {
                throw new \Exception('Unauthorized to delete this contract');
            }

            $contract->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contract template deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Contract deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete contract template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the contract form.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show()
    {
        $user = Auth::user();
        
        // Get the user's rented box(es)
        if ($user->locataire) {
            $boxes = Boxes::where('status', 1)
                         ->where('reserve_par', $user->email)
                         ->get();
        } else {
            $boxes = Boxes::where('user_id', $user->id)
                         ->where('status', 1)
                         ->whereNotNull('reserve_par')
                         ->get();
        }

        // Get all contract templates for the user
        $contracts = Contract::select('id', 'user_id', 'name', 'content', 'created_at', 'updated_at')
                           ->where('user_id', $user->id)
                           ->get();
        
        return view('contract', compact('boxes', 'contracts'));
    }
}

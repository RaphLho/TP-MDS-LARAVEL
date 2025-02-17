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
     * Store a new contract template.
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
            ]);

            $contract = Contract::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'content' => $request->content
            ]);

            if (!$contract) {
                throw new \Exception('Failed to save contract');
            }

            return response()->json([
                'success' => true,
                'message' => 'Contract template saved successfully',
                'contract' => $contract
            ]);

        } catch (\Exception $e) {
            Log::error('Contract creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save contract template',
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

        // Get existing contract template if any
        $existingContract = Contract::where('user_id', $user->id)
                                  ->latest()
                                  ->first();
        
        return view('contract', compact('boxes', 'existingContract'));
    }
}

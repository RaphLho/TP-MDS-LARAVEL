<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boxes;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class BoxesController extends Controller
{
    /**
     * Display a listing of the boxes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        
        // Check if user is a tenant (locataire) or the owner
        $isAllowed = User::where(function($query) use ($user) {
            $query->where('locataire', 1)
                  ->orWhere('id', $user->id);
        })->exists();

        if ($isAllowed) {
            // For tenants, show only available boxes (status = 0)
            if ($user->locataire) {
                $boxes = Boxes::where('status', 0)->paginate(6);
            } else {
                // For owner, show all boxes
                $boxes = Boxes::where('user_id', $user->id)->paginate(6);
            }
        } else {
            // Show only user's boxes
            $boxes = Boxes::where('user_id', Auth::id())->paginate(6);
        }

        return view('locations', compact('boxes'));
    }

    /**
     * Store a newly created box in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'address' => 'required',
            'price' => 'required|numeric'
        ]);

        // Create data array
        $data = [
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'price' => $request->price,
            'status' => 0, // Default to available
            'reserve_par' => null
        ];

        // Create new box with auto-incrementing ID
        $box = Boxes::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Box created successfully',
            'box' => $box
        ]);
    }

    public function edit(Request $request, Boxes $box)
    {
        $data = $request->all();
        if (isset($data['status']) && $data['status'] == 0) {
            $data['reserve_par'] = null;
        }
        $box->update($data);

        return response()->json(['success' => true, 'message' => 'Box updated successfully']);
    }

    public function delete(Request $request, Boxes $box)
    {
        // Delete box
        $box->delete();

        return response()->json([
            'success' => true,
            'message' => 'Box deleted successfully'
        ]);
    }

    public function toggleStatus(Request $request, Boxes $box)
    {
        // Toggle the status between 0 and 1
        $newStatus = $box->status == 0 ? 1 : 0;
        
        // Prepare update data
        $updateData = [
            'status' => $newStatus,
            'reserve_par' => $newStatus == 1 ? Auth::user()->email : null
        ];
        
        $box->update($updateData);

        $message = $newStatus == 1 ? 'Box réservée avec succès' : 'Réservation annulée avec succès';

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => $message
        ]);
    }

    /**
     * Display user's reservations.
     *
     * @return \Illuminate\Http\Response
     */
    public function reservations(): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        
        if ($user->locataire) {
            // For tenants, show their reservations
            $boxes = Boxes::where('status', 1)
                         ->where('reserve_par', $user->email)
                         ->paginate(6);
        } else {
            // For owners, show their rented boxes with renter's email
            $boxes = Boxes::where('user_id', $user->id)
                         ->where('status', 1)
                         ->whereNotNull('reserve_par')
                         ->paginate(6);
        }
        
        return view('locations', compact('boxes'));
    }

    public function contract(): \Illuminate\Contracts\View\View
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
        
        return view('contrat', compact('boxes'));
    }
}
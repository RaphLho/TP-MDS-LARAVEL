<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boxes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class BoxesController extends Controller
{
    /**
     * Display a listing of the boxes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): \Illuminate\Contracts\View\View // Specify the return type
    {
        $boxes = Boxes::paginate(6);
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
            'status' => 0 // Default to available
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
        $box->update($request->all());

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
        $box->status = $box->status === 0 ? 1 : 0;
        $box->save();

        return response()->json(['status' => $box->status]);
    }
}

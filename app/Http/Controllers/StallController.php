<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stall;
use Illuminate\Support\Facades\Validator;

class StallController extends Controller
{
    public function stall(){
        $stalls = Stall::all();
        return response()->json(['stalls' => $stalls], 200);
    }

    public function storeStall(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'location' => 'required',
            'owner_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $stall = Stall::create([
            'name'      => $request->input('name'),
            'location'  => $request->input('location'),
            'owner_name'=> $request->input('owner_name'),
        ]);

        return response()->json(['message' => 'Stall added successfully!', 'stall' => $stall], 201);
    }

    public function StallUpdate(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'location' => 'required',
            'owner_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $stall = Stall::find($id);

        if ($stall) {
            $stall->update([
                'name'      => $request->input('name'),
                'location'  => $request->input('location'),
                'owner_name'=> $request->input('owner_name'),
            ]);

            return response()->json(['message' => 'Stall updated successfully!', 'stall' => $stall], 200);
        } else {
            return response()->json(['message' => 'Stall not found'], 404);
        }
    }

    public function StallDestroy($id){
        $stall = Stall::find($id);

        if ($stall) {
            $stall->delete();
            return response()->json(['message' => 'Stall deleted successfully!'], 200);
        } else {
            return response()->json(['message' => 'Stall not found'], 404);
        }
    }
}

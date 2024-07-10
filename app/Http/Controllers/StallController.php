<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stall;
use Illuminate\Support\Facades\Validator;

class StallController extends Controller
{
    public function stall(){
        $stalls = Stall::all();
        return response()->json([
            'success' => true,
            'message' => 'Store data successfully',
            'result' => $stalls
        ], 200);
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

        return response()->json(['sucess'=> true,'message' => 'Store added successfully!', 'result' => $stall], 201);
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

            return response()->json(['sucess'=> true ,'message' => 'Store updated successfully!', 'result' => $stall], 200);
        } else {
            return response()->json(['sucess'=> false ,'message' => 'Store not found'], 404);
        }
    }

    public function StallDestroy($id){
        $stall = Stall::find($id);

        if ($stall) {
            $stall->delete();
            return response()->json(['sucess'=> true,'message' => 'Store deleted successfully!', 'result' => $stall], 200);
        } else {
            return response()->json(['sucess' =>false,'message' => 'Store not found'], 404);
        }
    }
}

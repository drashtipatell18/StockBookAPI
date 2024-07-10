<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Scrap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScrapController extends Controller
{
    public function scrap()
    {
        $scraps = Scrap::all();
        return response()->json([
            'success' => true,
            'message' => 'Scrap data successfully',
            'result' => $scraps
        ], 200);
    }

    public function scrapInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'name' => 'required',
            'scrap_weight' => 'required',
            'by_date' => 'required|date',
            'price' => 'required',
            'to_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $scrap = Scrap::create([
            'customer_name'    => $request->input('customer_name'),
            'name'             => $request->input('name'),
            'scrap_weight'     => $request->input('scrap_weight'),
            'by_date'          => $request->input('by_date'),
            'price'            => $request->input('price'),
            'to_date'          => $request->input('to_date'),
        ]);

        return response()->json(['sucess'=>true,'message' => 'Scrap added successfully!', 'result' => $scrap], 201);
    }

    public function scrapUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'name' => 'required',
            'scrap_weight' => 'required',
            'by_date' => 'required|date',
            'price' => 'required',
            'to_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }


        $scrap = Scrap::find($id);

        if ($scrap) {
            $scrap->update([
                'customer_name'    => $request->input('customer_name'),
                'name'             => $request->input('name'),
                'scrap_weight'     => $request->input('scrap_weight'),
                'by_date'          => $request->input('by_date'),
                'price'            => $request->input('price'),
                'to_date'          => $request->input('to_date'),
            ]);

            return response()->json(['sucess'=>true,'message' => 'Scrap updated successfully!', 'result' => $scrap], 200);
        } else {
            return response()->json(['sucess'=>false,'message' => 'Scrap not found'], 404);
        }
    }

    public function scrapDestroy($id)
    {
        $scrap = Scrap::find($id);

        if ($scrap) {
            $scrap->delete();
            return response()->json(['sucess' =>true,'message' => 'Scrap deleted successfully!', 'result' => $scrap], 200);
        } else {
            return response()->json(['sucess'=> false,'message' => 'Scrap not found'], 404);
        }
    }
}

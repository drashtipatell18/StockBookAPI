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
        return response()->json(['scraps' => $scraps], 200);
    }

    public function scrapInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'scrap_weight' => 'required',
            'by_date' => 'required|date',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }


        $scrap = Scrap::create([
            'name'             => $request->input('name'),
            'scrap_weight'     => $request->input('scrap_weight'),
            'by_date'          => $request->input('by_date'),
            'price'            => $request->input('price'),
            'to_date'          => $request->input('to_date'),
        ]);

        return response()->json(['message' => 'Scrap added successfully!', 'scrap' => $scrap], 201);
    }

    public function scrapUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'scrap_weight' => 'required',
            'by_date' => 'required|date',
            'price' => 'required',
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
                'name'             => $request->input('name'),
                'scrap_weight'     => $request->input('scrap_weight'),
                'by_date'          => $request->input('by_date'),
                'price'            => $request->input('price'),
                'to_date'          => $request->input('to_date'),
            ]);

            return response()->json(['message' => 'Scrap updated successfully!', 'scrap' => $scrap], 200);
        } else {
            return response()->json(['message' => 'Scrap not found'], 404);
        }
    }

    public function scrapDestroy($id)
    {
        $scrap = Scrap::find($id);

        if ($scrap) {
            $scrap->delete();
            return response()->json(['message' => 'Scrap deleted successfully!'], 200);
        } else {
            return response()->json(['message' => 'Scrap not found'], 404);
        }
    }
}

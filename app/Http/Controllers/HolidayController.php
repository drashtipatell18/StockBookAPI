<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
{
    public function Holiday()
    {
        $holidays = Holiday::all();
        return response()->json([
            'success' => true,
            'message' => 'Holiday data successfully',
            'result' => $holidays
        ], 200);
    }

    public function holidayInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'date' => 'required|date',
            'day' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $holiday = Holiday::create([
            'name' => $request->input('name'),
            'date' => $request->input('date'),
            'day' => $request->input('day'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Holiday added successfully!',
            'result' => $holiday
        ], 201);
    }

    
    public function holidayUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:holidays,id',
            'name' => 'required',
            'date' => 'required|date',
            'day' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $holiday = Holiday::find($request->input('id'));
        if (!$holiday) {
            return response()->json([
                'success' => false,
                'message' => 'Holiday not found'
            ], 404);
        }

        $holiday->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Holiday updated successfully!',
            'result' => $holiday
        ], 200);
    }

    public function holidayDestroy(Request $request)
    {
        $holiday = Holiday::find($request->input('id'));
        if (!$holiday) {
            return response()->json([
                'success' => false,
                'message' => 'Holiday not found'
            ], 404);
        }

        $holiday->delete();
        return response()->json([
            'success' => true,
            'message' => 'Holiday deleted successfully!',
            'result' => $holiday
        ], 200);
    }
}

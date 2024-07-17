<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public function stock()
    {
        $stocks = Stock::all();
        return response()->json([
            'success' => true,
            'message' => 'Stock data successfully',
            'result' => $stocks
        ], 200);
    }

    public function stockStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required',
            'quantity' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $stock = Stock::where('book_id', $request->input('book_id'))->first();
        if($stock)
        {
            $stock->quantity = $request->input('quantity');
            $stock->price = $request->input('price');

            $stock->update([
                'quantity' => $request->input('quantity') + $stock->quantity,
                'price' => $request->input('price')
            ]);
        }
        else
        {
            $stock = Stock::create([
                'book_id'      => $request->input('book_id'),
                'quantity'  => $request->input('quantity'),
                'price'     => $request->input('price'),
            ]);
        }

        return response()->json(['sucess'=> true, 'message' => 'Stock added successfully!', 'result' => $stock], 201);
    }

    public function stockUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:stocks,id',
            'book_id' => 'required',
            'quantity' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $stock = Stock::find($request->input('id'));

        if ($stock) {
            $stock->update([
                'id'        => $request->input('id'),
                'book_id'   => $request->input('book_id'),
                'quantity'  => $request->input('quantity'),
                'price'     => $request->input('price'),
            ]);

            return response()->json(['sucess'=> true,'message' => 'Stock updated successfully!', 'result' => $stock], 200);
        } else {
            return response()->json(['error'=> true,'message' => 'Stock not found'], 404);
        }
    }

    public function stockDestroy(Request $request)
    {
        $stock = Stock::find($request->input('id'));

        if ($stock) {
            $stock->delete();
            return response()->json(['sucess'=> true ,'message' => 'Stock deleted successfully!', 'result' => $stock], 200);
        } else {
            return response()->json(['error'=> false ,'message' => 'Stock not found'], 404);
        }
    }
}

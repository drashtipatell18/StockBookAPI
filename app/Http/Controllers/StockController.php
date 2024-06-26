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
        return response()->json(['stocks' => $stocks], 200);
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
                'name'      => $request->input('name'),
                'quantity'  => $request->input('quantity'),
                'price'     => $request->input('price'),
            ]);
        }

        return response()->json(['message' => 'Stock added successfully!', 'stock' => $stock], 201);
    }

    public function stockUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'quantity' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $stock = Stock::find($id);

        if ($stock) {
            $stock->update([
                'name'      => $request->input('name'),
                'quantity'  => $request->input('quantity'),
                'price'     => $request->input('price'),
            ]);

            return response()->json(['message' => 'Stock updated successfully!', 'stock' => $stock], 200);
        } else {
            return response()->json(['message' => 'Stock not found'], 404);
        }
    }

    public function stockDestroy($id)
    {
        $stock = Stock::find($id);

        if ($stock) {
            $stock->delete();
            return response()->json(['message' => 'Stock deleted successfully!'], 200);
        } else {
            return response()->json(['message' => 'Stock not found'], 404);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Stall;
use App\Models\Book;

class SalesOrderController extends Controller
{
    public function salesorder()
    {
        $salesorders = SalesOrder::with(['stall', 'book'])->get();
        return response()->json(['salesorders' => $salesorders], 200);
    }

    public function salesorderInsert(Request $request)
    {
        $validateRequest = Validator::make($request->all(), [
            'stall_id'    => 'required|exists:stalls,id',
            'location'    => 'required',
            'book_id'     => 'required|exists:books,id',
            'sales_price' => 'required|numeric',
            'quantity'    => 'required|integer',
            'total_price' => 'required|numeric',
        ]);

        if($validateRequest->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails.',
                'errors' => $validateRequest->errors()
            ], 403);
        }

        $stock = Stock::where('book_id', $request->input('book_id'))->first();
        if(!$stock)
        {
            $customError = "*Stock not available";
            $stalls = Stall::pluck('name', 'id')->unique();
            $books = Book::pluck('name', 'id')->unique();
            return response()->json([
                'success' => false,
                'message' => 'Validation fails.',
                'errors' => $customError
            ], 403);
        }

        if($stock->quantity < $request->input('quantity'))
        {
            $customError = "*Stock not available";
            $stalls = Stall::pluck('name', 'id')->unique();
            $books = Book::pluck('name', 'id')->unique();
            return response()->json([
                'success' => false,
                'message' => 'Validation fails.',
                'errors' => $customError
            ], 403);
        }

        $salesorder = SalesOrder::create([
            'stall_id'    => $request->input('stall_id'),
            'location'    => $request->input('location'),
            'book_id'     => $request->input('book_id'),
            'sales_price' => $request->input('sales_price'),
            'quantity'    => $request->input('quantity'),
            'total_price' => $request->input('total_price'),
        ]);

        $stock->update([
            'quantity' => $stock->quantity - $request->input('quantity')
        ]);

        return response()->json(['message' => 'Sales Order added successfully!', 'salesorder' => $salesorder], 200);
    }

    public function salesorderUpdate(Request $request, $id)
    {
        $validateRequest = Validator::make($request->all(), [
            'stall_id'    => 'required|exists:stalls,id',
            'location'    => 'required',
            'book_id'     => 'required|exists:books,id',
            'sales_price' => 'required|numeric',
            'quantity'    => 'required|integer',
            'total_price' => 'required|numeric',
        ]);

        if($validateRequest->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails.',
                'errors' => $validateRequest->errors()
            ], 403);
        }

        $salesorder = SalesOrder::find($id);

        if ($salesorder) {
            $salesorder->update([
                'stall_id'    => $request->input('stall_id'),
                'location'    => $request->input('location'),
                'book_id'     => $request->input('book_id'),
                'sales_price' => $request->input('sales_price'),
                'quantity'    => $request->input('quantity'),
                'total_price' => $request->input('total_price'),
            ]);

            return response()->json(['message' => 'Sales Order updated successfully!', 'salesorder' => $salesorder], 200);
        } else {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }
    }

    public function salesorderDestroy($id)
    {
        $salesorder = SalesOrder::find($id);

        if ($salesorder) {
            $salesorder->delete();
            return response()->json(['message' => 'Sales Order deleted successfully!'], 200);
        } else {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }
    }
}

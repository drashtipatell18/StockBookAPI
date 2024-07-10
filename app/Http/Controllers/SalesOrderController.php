<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Stock;
use App\Models\Stall;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesOrderController extends Controller
{
    public function salesorder()
    {
        $salesorders = SalesOrder::with(['stall', 'book'])->get();
        return response()->json([
            'success' => true,
            'message' => 'Salesorders data successfully',
            'result' => $salesorders
        ], 200);
    }

    public function salesorderInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'location'     => 'required',
            'sales_price'  => 'required',
            'quantity'     => 'required',
            // 'total_price ' => 'required',
            'book_id' => 'required',
            'stall_id' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ], 400);
        }

        $stock = Stock::where('book_id', $request->input('book_id'))->first();
        if(!$stock)
        {
            $customError = "*Stock not available";
            $stalls = Stall::pluck('name', 'id')->unique();
            $books = Book::pluck('name', 'id')->unique();
            return response()->json(['success' => false, 'message' => 'Stock not available'], 400);
        }

        if($stock->quantity < $request->input('quantity'))
        {
            $customError = "*Stock not available";
            $stalls = Stall::pluck('name', 'id')->unique();
            $books = Book::pluck('name', 'id')->unique();
            return response()->json(['success' => false, 'message' => 'Stock not available'], 400);
        }

        $salesorders = SalesOrder::create([
            'stall_id'         => $request->input('stall_id'),
            'location'         => $request->input('location'),
            'book_id'          => $request->input('book_id'),
            'sales_price'      => $request->input('sales_price'),
            'quantity'         => $request->input('quantity'),
            'total_price'      => $request->input('total_price'),
        ]);

        $stock->update([
            'quantity' => $stock->quantity - $request->input('quantity')
        ]);

        return response()->json(['success' => false,'message' => 'Sales Order added successfully!', 'result' => $salesorders], 200);
    }

    public function salesorderUpdate(Request $request, $id)
    {
        $validateRequest = Validator::make($request->all(), [
            // 'location'     => 'required',
            'sales_price'  => 'required',
            'quantity'     => 'required',
            // 'total_price ' => 'required',
            'book_id' => 'required',
            'stall_id' => 'required'
        ]);

        if($validateRequest->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails.',
                'errors' => $validateRequest->errors()
            ], 403);
        }

        $salesorders = SalesOrder::find($id);

        if($salesorders->quantity == $request->input('quantity'))
        {
            $salesorders->update([
                'book_id'          => $request->input('book_id'),
                'stall_id'         => $request->input('stall_id'),
                'location'         => $request->input('location'),
                'sales_price'      => $request->input('sales_price'),
                'quantity'         => $request->input('quantity'),
                'total_price'      => $request->input('total_price'),
            ]);
        }
        else if($salesorders->quantity > $request->input('quantity'))
        {
            $stock = Stock::where('book_id', $request->input('book_id'))->first();
            $stock->update([
                'quantity' => $stock->quantity + ($salesorders->quantity - $request->input('quantity'))
            ]);
            $salesorders->update([
                'book_id'          => $request->input('book_id'),
                'stall_id'         => $request->input('stall_id'),
                'location'         => $request->input('location'),
                'sales_price'      => $request->input('sales_price'),
                'quantity'         => $request->input('quantity'),
                'total_price'      => $request->input('total_price'),
            ]);
            $stock = Stock::where('book_id', $request->input('book_id'))->first();
        }
        else
        {
            $stock = Stock::where('book_id', $request->input('book_id'))->first();
            if($stock->quantity < ($request->input('quantity') - $salesorders->quantity))
            {
                session()->flash('danger', 'Quantity not available!');
                return back();
            }
            $stock->update([
                'quantity' => $stock->quantity - ($request->input('quantity') - $salesorders->quantity)
            ]);
            $salesorders->update([
                'book_id'          => $request->input('book_id'),
                'stall_id'         => $request->input('stall_id'),
                'location'         => $request->input('location'),
                'sales_price'      => $request->input('sales_price'),
                'quantity'         => $request->input('quantity'),
                'total_price'      => $request->input('total_price'),
            ]);
        }

        return response()->json(['success' => true,'message' => 'Sales Order updated successfully!', 'result' => $salesorders], 200);
    }

    public function salesorderDestroy($id)
    {
        $salesorder = SalesOrder::find($id);

        if ($salesorder) {
            $salesorder->delete();
            return response()->json(['success' => true,'message' => 'Sales Order deleted successfully!', 'result' => $salesorder], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Sales Order not found'], 404);
        }
    }
}

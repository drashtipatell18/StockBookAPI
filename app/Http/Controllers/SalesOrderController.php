<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function salesorder()
    {
        $salesorders = SalesOrder::with(['stall', 'book'])->get();
        return response()->json(['salesorders' => $salesorders], 200);
    }

    public function salesorderInsert(Request $request)
    {
        $request->validate([
            'stall_id'    => 'required|exists:stalls,id',
            'location'    => 'required',
            'book_id'     => 'required|exists:books,id',
            'sales_price' => 'required|numeric',
            'quantity'    => 'required|integer',
            'total_price' => 'required|numeric',
        ]);

        $salesorder = SalesOrder::create([
            'stall_id'    => $request->input('stall_id'),
            'location'    => $request->input('location'),
            'book_id'     => $request->input('book_id'),
            'sales_price' => $request->input('sales_price'),
            'quantity'    => $request->input('quantity'),
            'total_price' => $request->input('total_price'),
        ]);

        return response()->json(['message' => 'Sales Order added successfully!', 'salesorder' => $salesorder], 201);
    }

    public function salesorderUpdate(Request $request, $id)
    {
        $request->validate([
            'stall_id'    => 'required|exists:stalls,id',
            'location'    => 'required',
            'book_id'     => 'required|exists:books,id',
            'sales_price' => 'required|numeric',
            'quantity'    => 'required|integer',
            'total_price' => 'required|numeric',
        ]);

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
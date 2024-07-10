<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function payment()
    {
        $payments = Payment::all();
        return response()->json([
            'success' => true,
            'message' => 'Payment data successfully',
            'data' => $payments
        ], 200);
        return response()->json($payments, 200);
    }

    public function paymentInsert(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'total_price' => 'required',
            'status' => 'required',
            // 'accountno' => 'required|numeric',
            // 'bankname' => 'required',
            // 'ifsccode' => 'required',
            'payment_date' => 'required',
            'salary_type' => 'required',
            'employee_id' => 'required|exists:employees,id',
            'advance_payment' => 'required',
            'advance_payment_date' => 'required',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validation Fails',
                'errors' => $validate->errors()
            ], 403);
        }

        $payment = Payment::create([
            'employee_id'           => $request->input('employee_id'),
            'accountno'             => $request->input('accountno'),
            'bankname'              => $request->input('bankname'),
            'ifsccode'              => $request->input('ifsccode'),
            'payment_date'          => Carbon::parse($request->input('payment_date')),
            'total_price'           => $request->input('total_price'),
            'salary_type'           => $request->input('salary_type'),
            'status'                => $request->input('status'),
            'advance_payment'       => $request->input('advance_payment'),
            'advance_payment_date'  => $request->input('advance_payment_date'),

        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment inserted successfully.',
            'payment' => $payment
        ], 200);

    }

    public function paymentUpdate(Request $request, $id)
    {
        
        $payments = Payment::find($id);

        if($payments == null)
        {
            return response()->json([
                'success' => false,
                'message' => 'Payment ID not found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'total_price' => 'required',
            'status' => 'required',
            // 'accountno' => 'required|numeric',
            // 'bankname' => 'required',
            // 'ifsccode' => 'required',
            'payment_date' => 'required',
            'salary_type' => 'required',
            'advance_payment' => 'required',
            'advance_payment_date' => 'required',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validation Fails',
                'errors' => $validate->errors()
            ], 403);
        }

        $payments->update([
            'employee_id'      => $request->input('employee_id'),
            'accountno'        => $request->input('accountno'),
            'bankname'         => $request->input('bankname'),
            'ifsccode'         => $request->input('ifsccode'),
            'payment_date'     => Carbon::parse($request->input('payment_date')),
            'total_price'      => $request->input('total_price'),
            'salary_type'      => $request->input('salary_type'),
            'status'           => $request->input('status'),
            'advance_payment'       => $request->input('advance_payment'),
            'advance_payment_date'  => $request->input('advance_payment_date'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully.',
            'payment' => $payments
        ], 200);
    }

    public function paymentDestroy($id)
    {
        $payments = Payment::find($id);
        $payments->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully.',     
            'payment' => $payments
        ], 200);
    }
}

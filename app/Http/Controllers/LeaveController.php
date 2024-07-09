<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    public function leave()
    {
        $employee = Employee::where('email', User::find(Auth()->user()->id)->email)->get()->first();
        $leaves = ((Auth()->user()->role_id != 1)?Leave::with('employee')->where('employee_id', $employee->id)->get():Leave::with('employee')->get());
        return response()->json($leaves, 200);
    }

    public function leaveInsert(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'reason' => 'required',
            'startdate' => 'required',
            'enddate' => 'required',
            'leave_type' => 'required',
            'time_from' => 'required',
            'time_to' => 'required',
            'totalhours' => 'required|numeric',
            // 'employee_id' => 'required|exists:employees,id'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails',
                'errors' => $validate->errors()
            ],403);
        }

        $balanceLeave = 22; // Initial balance leave, can be retrieved from the database if needed.
        $startDate = Carbon::parse($request->input('startdate'));
        $endDate = Carbon::parse($request->input('enddate'));

        // Calculate the total leave days from start to end date
        $leaveDays = $startDate->diffInDays($endDate) + 1;

        // Calculate total hours
        $totalHours = $request->input('totalhours');

        // Assuming 9 hours is a full workday
        $fullWorkDayHours = 9;

        if ($totalHours < $fullWorkDayHours) {
            $leaveDays = 0.5; // Half-day leave
        }

        // If the calculated leave days exceed the balance leave, return an error
        if ($leaveDays > $balanceLeave) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient balance leave"
            ], 403);
        }

        // Add leave record
        $leave = Leave::create([
            'user_id' => Auth()->user()->id,
            'employee_id' => $request->input('employee_id'),
            'reason' => $request->input('reason'),
            'startdate' => Carbon::parse($request->input('startdate')),
            'enddate' => Carbon::parse($request->input('enddate')),
            'leave_type' => $request->input('leave_type'),
            'time_from' => Carbon::parse($request->input('time_from'))->format('H:i:s'),
            'time_to' => Carbon::parse($request->input('time_to'))->format('H:i:s'),
            'totalhours' => $request->input('totalhours'),
            'status' =>'pending',
            // 'requestto' => $request->input('requestto'),
        ]);

        // $employee = Employee::find($request->input('employee_id'));
        // $newBalance = $employee->total_leave - $leaveDays; // Subtract half-day
        // dd($newBalance);

        if ($request->input('status') == 'approved' && $request->input('employee_id')) {
            $employee = Employee::find($request->input('employee_id'));
            if ($employee) {
                // Adjust balance leave based on total hours
                if ($totalHours < $fullWorkDayHours) { // Less than a full workday
                    $newBalance = $employee->total_leave - $leaveDays; // Subtract half-day
                } else {
                    $newBalance = $employee->total_leave - $leaveDays; // Subtract full days
                }
                $employee->update(['total_leave' => $newBalance]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Leave added successfully'],200);
    }

    public function changeStatus(Request $request,$id)
    {
        $leave = Leave::findOrFail($id);
        $leave->status = $request->input('status');
        $leave->save();

        // You can return a response if needed
        return response()->json(['message' => 'Leave status updated successfully']);
    }

    public function leaveUpdate(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'reason' => 'required',
            'startdate' => 'required',
            'enddate' => 'required',
            'leave_type' => 'required',
            'time_from' => 'required',
            'time_to' => 'required',
            'totalhours' => 'required',
            // 'requestto' => 'required',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails',
                'errors' => $validate->errors()
            ],403);
        }
        $balanceLeave = 22;

        $startDate = Carbon::parse($request->input('startdate'));
        $endDate = Carbon::parse($request->input('enddate'));

        $leaveDays = $startDate->diffInDays($endDate) + 1;

        if ($leaveDays > $balanceLeave) {
            return response()->json(['success' => false, 'message' => 'Insufficient balance leave'],403);
        }

        $leaves = Leave::find($id);
       
        // Update the balance leave for the employee
        if ($request->input('status') == 'approved' && $leaves->status != 'approved') {
            $employee = Employee::find($leaves->employee_id);
            if ($employee) {
                if($request->totalhours <= 2)
                {
                    $newBalance = $employee->total_leave - 0;
                }
                else if($request->totalhours <= 4)
                {
                    $newBalance = $employee->total_leave - 0.5;
                }
                else
                {
                    $newBalance = $employee->total_leave - $leaveDays;
                }
                $employee->update(['total_leave' => $newBalance]);
            }
        }

        $leaves->update([
            'employee_id'=> $request->input('employee_id'),
            'reason'     => $request->input('reason'),
            'startdate'  => Carbon::parse($request->input('startdate')),
            'enddate'    => Carbon::parse($request->input('enddate')),
            'leave_type' => $request->input('leave_type'),
            'time_from'  => Carbon::parse($request->input('time_from'))->format('H:i:s'),
            'time_to'    => Carbon::parse($request->input('time_to'))->format('H:i:s'),
            'totalhours' => $request->input('totalhours'),
            'status'     => 'pending'
            // 'requestto'  => $request->input('requestto'),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Leave updated successfully'
        ],200);
    }

    public function leaveDestroy($id)
    {
        $leaves = Leave::find($id);
        $leaves->delete();
        return redirect()->back();
        return response()->json([
            'success' => true,
            'message' => "Leave deleted successfully"
        ],200);
    }

    public function updateStatus(Request $request)
    {
        $id = $request->id;
        $leave = Leave::find($id);
        if ($leave) {
            $leave->status = 1;
            $leave->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Entity not found.']);
    }
}

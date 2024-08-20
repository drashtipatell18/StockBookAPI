<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function employees()
    {
        $employees = Employee::all();
        return response()->json([
            'success' => true,
            'message' => 'Employee data successfully',
            'result' => $employees
        ], 200);
    }

     public function getEmail($id)
    {
        return response()->json(["email" => User::find($id)->email]);
    }

    public function employeeInsert(Request $request ){
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'role_id' => 'required',
            'dob' => 'required|date',
            'gender' => 'required',
            'email' => 'required|email',
            // 'address' => 'required',
            'phoneno' => 'required|numeric',
            'salary' => 'required|numeric',
            'joiningdate' => 'required|date',
            'aadhar_number' => 'required',
            'password' => 'required'
        ]);

        $filename = '';
        if ($request->hasFile('profilepic')){
            $image = $request->file('profilepic');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move('images', $filename);
        }

        $employee = Employee::create([
            'role_id'        => $request->input('role_id'),
            'firstname'      => $request->input('firstname'),
            'lastname'       => $request->input('lastname'),
            'dob'            => $request->input('dob'),
            'email'          => $request->input('email'),
            'address'        => $request->input('address'),
            'phoneno'        => $request->input('phoneno'),
            'gender'         => $request->input('gender'),
            'salary'         => $request->input('salary'),
            'profilepic' => $filename,
            'joiningdate'    => $request->input('joiningdate'),
            'aadhar_number'  => $request->input('aadhar_number'),
            'password'       => Hash::make($request->input('password')),

        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee added successfully',
            'result' => $employee
        ], 200);
    }

    public function employeeUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:employees,id',
            'firstname' => 'required',
            'lastname' => 'required',
            'role_id' => 'required',
            'dob' => 'required|date',
            'gender' => 'required',
            'email' => 'required|email',
            // 'address' => 'required',
            'phoneno' => 'required|numeric',
            'salary' => 'required|numeric',
            'joiningdate' => 'required|date',
            'aadhar_number' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ], 400);
        }

        $employee = Employee::find($request->input('id'));

        $employee->update([
            'id'             => $request->input('id'),
            'role_id'        => $request->input('role_id'),
            'firstname'      => $request->input('firstname'),
            'lastname'       => $request->input('lastname'),
            'dob'            => $request->input('dob'),
            'email'          => $request->input('email'),
            'address'        => $request->input('address'),
            'phoneno'        => $request->input('phoneno'),
            'gender'         => $request->input('gender'),
            'salary'         => $request->input('salary'),
            'joiningdate'    => $request->input('joiningdate'),
            'aadhar_number'  => $request->input('aadhar_number')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee Updated Successfully!',
            'result'=> $employee,
        ], 200);

    }

    public function employeeDestroy(Request $request)
    {
        $employee = Employee::find($request->input('id'));
        if ($employee) {
            $employee->delete();
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully!',
                'result'=> $employee,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }
    }

    public function myProfile()
    {
        if (Auth::check()) {
            $userid = Auth::user()->id;
            $employe = Auth::user()->employee;
            if ($employe) {
                $id = $employe->id;
                $employee = Employee::find($id);
                return response()->json([
                    'success' => true,
                    'result' => compact('id', 'employee')
                ], 200);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated or no employee profile'
        ], 401);
    }



    public function Profileupdate(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'phoneno' => 'required',
            'position' => 'required'
        ]);

        $employee = Employee::findOrFail($request->input('id'));

        if ($employee) {
            $employee->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'result' => $employee
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }
    }
}

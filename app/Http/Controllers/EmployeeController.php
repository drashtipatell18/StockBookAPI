<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function employees()
    {
        dd(1);
        $employees = Employee::all();
        return response()->json([
            'success' => true,
            'data' => $employees
        ], 200);
    }

    public function employeeInsert(Request $request)
    {
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
        if ($request->hasFile('image')){
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move('images', $filename);
        }

        $user = User::create([
            'name' => $request->input('firstname'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $request->input('role_id'),
            'image' => $filename
        ]);



        $employee = Employee::create([
            'role_id' => $request->input('role_id'),
            'firstname'      => $request->input('firstname'),
            'lastname'       => $request->input('lastname'),
            'dob'            => $request->input('dob'),
            'email'          => $request->input('email'),
            'password'       => Hash::make($request->input('password')),
            'address'        => $request->input('address'),
            'phoneno'        => $request->input('phoneno'),
            'gender'         => $request->input('gender'),
            'salary'         => $request->input('salary'),
            'joiningdate'    => $request->input('joiningdate'),
            'aadhar_number'  => $request->input('aadhar_number')
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Employee added successfully!',
            'data' => $employee
        ], 201);
    }



    public function employeeUpdate(Request $request, $id)
    {

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
        ]);

          // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        $employees = Employee::find($id);

        if (!$employees) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404); // 404 Not Found
        }

        if ($request->filled('password')) {
            $user = User::find($employees->user_id);
            if ($user) {
                $user->update([
                    'password' => Hash::make($request->input('password'))
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found for this employee',
                ], 404); // 404 Not Found
            }
        }

        $employees->update([
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
            'message' => 'Employee updated successfully!',
            'data' => $employees
        ], 200);

    }

    public function employeeDestroy($id)
    {
        $employee = Employee::find($id);
        if ($employee) {
            $employee->delete();
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully!'
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
                    'data' => compact('id', 'employee')
                ], 200);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated or no employee profile'
        ], 401);
    }



    public function Profileupdate(Request $request, $id)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'phoneno' => 'required',
            'position' => 'required'
        ]);

        $employee = Employee::findOrFail($id);

        if ($employee) {
            $employee->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $employee
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }
    }
}

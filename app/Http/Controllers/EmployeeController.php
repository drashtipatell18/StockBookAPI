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
            'data' => $employees
        ], 200);
    }

     public function getEmail($id)
    {
        return response()->json(["email" => User::find($id)->email]);
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

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ], 400);
        }

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
            'address'        => $request->input('address'),
            'phoneno'        => $request->input('phoneno'),
            'gender'         => $request->input('gender'),
            'salary'         => $request->input('salary'),
            'joiningdate'    => $request->input('joiningdate'),
            'aadhar_number'  => $request->input('aadhar_number')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee added successfully!'
        ], 200);
    }



    public function employeeUpdate(Request $request, $id)
    {
        // echo '<pre>';
        // print_r($request->all());
        // print_r($id);
        // echo '</pre>';exit;
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

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ], 400);
        }

        $employees = Employee::find($id);
        // echo '<pre>';
        // // print_r($request->all());
        // print_r($employees);
        // echo '</pre>';exit;
        // if(!empty($request->input('password')))
        // {
        //     $user = User::find($employees->user_id);
        //     $user->update([
        //         'password' => Hash::make($request->input('password'))
        //     ]);
        // }

        $employees->update([
            'role_id'       => $request->input('role_id'),
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
            'message' => 'Employee Updated Successfully!'
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

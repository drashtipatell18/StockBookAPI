<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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

    public function employeeInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'user_id' => 'required',
            'dob' => 'required|date',
            'email' => 'required|email',
            'address' => 'required',
            'phoneno' => 'required|numeric',
            'gender' => 'required',
            'salary' => 'required|numeric',
            'joiningdate' => 'required|date',
        ]);

        $employeeimg = '';
        if ($request->hasFile('profilepic')) {
            $subimages = [];

            foreach ($request->file('profilepic') as $file) {
                $subImageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $file->move('images', $subImageName);

                // Add the filename to the array
                $subimages[] = $subImageName;
            }
            $employeeimg = json_encode($subimages);
        }

        $employee = Employee::create([
            'user_id' => $request->input('user_id'),
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'dob' => $request->input('dob'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'phoneno' => $request->input('phoneno'),
            'gender' => $request->input('gender'),
            'salary' => $request->input('salary'),
            'joiningdate' => $request->input('joiningdate'),
            'profilepic' => $employeeimg,
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
            'user_id' => 'required',
            'dob' => 'required|date',
            'email' => 'required|email',
            'address' => 'required',
            'phoneno' => 'required|numeric',
            'gender' => 'required',
            'salary' => 'required|numeric',
            'joiningdate' => 'required|date',
        ]);

        $employee = Employee::find($id);

        if ($employee) {
            if ($request->hasFile('profilepic')) {
                $image = $request->file('profilepic');
                $employeeimg = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $employeeimg);

                // Optionally delete the old image
                if ($request->hasFile('profilepic')) {
                    $subimages = [];
                    foreach ($request->file('profilepic') as $file) {
                        $subImageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move('images', $subImageName);
                        $subimages[] = $subImageName;
                    }
                    $employee->profilepic = json_encode($subimages);
                }
            }

            $employee->update([
                'user_id'       => $request->input('user_id'),
                'firstname'      => $request->input('firstname'),
                'lastname'       => $request->input('lastname'),
                'dob'            => $request->input('dob'),
                'email'          => $request->input('email'),
                'address'        => $request->input('address'),
                'phoneno'        => $request->input('phoneno'),
                'gender'         => $request->input('gender'),
                'salary'         => $request->input('salary'),
                'joiningdate'    => $request->input('joiningdate'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully!',
                'data' => $employee
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }
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

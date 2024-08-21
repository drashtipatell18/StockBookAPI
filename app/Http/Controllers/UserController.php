<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function users()
    {
        $users = User::with('role')->get();
        return response()->json([
            'success' => true,
            'message' => 'User data successfully',
            'result' => $users
        ], 200);
    }

    public function login(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails',
                'error' => $validateUser->errors()
            ], 401);
        }

        $user = Employee::withTrashed()->where('email', $request->input('email'))->first();

        // if (!$user || !Hash::check($request->input('password'), $user->password)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Invalid credentials',
        //     ], 401);
        // }

        // Generate a token
        $token = $user->createToken('User Token')->plainTextToken;
        $userImage = $user->profilepic ? $user->profilepic : null;

        // Check if the user has a role and get the role name
        $role = Role::find($user->role_id);
        $roleName = $role ? $role->role_name : 'No role assigned';

        return response()->json([
            'success' => true,
            'message' => 'Login successfully',
            'result' => [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'email' => $user->email,
                'access_token' => $token,
                'role' => $roleName,
                'image' => $userImage,
            ],
        ]);
    }

    public function userInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role_id' => 'required',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails',
                'error' => $validator->errors()
            ], 401);
        }

        $filename = '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $filename);
        }

        $user = User::create([
            'name'      => $request->input('name'),
            'email'     => $request->input('email'),
            'password'  => bcrypt($request->input('password')),
            'role_id'   => $request->input('role_id'),
            'image'     => $filename,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'result' => $user,
        ], 200);
    }

    public function userUpdate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'name' => 'required',
            'email' => 'required',
            'role_id' => 'required',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails',
                'error' => $validator->errors()
            ], 401);
        }

        $users = User::find($request->input('id'));

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move('images', $filename);
            $users->image = $filename;
        }

        $users->update([
            'id' => $request->input('id'),
            'name'      => $request->input('name'),
            'email'     => $request->input('email'),
            'role_id'   => $request->input('role_id'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User Updated successfully',
            'result' => $users,
        ], 200);
    }

    public function userDestroy(Request $request)
    {
        $user = User::find($request->input('id'));
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User Deleted successfully',
            'result' => $user,
        ], 200);
    }

    public function myProfile()
    {
        if (Auth::check()) {
            $userid = Auth::user()->id;
            $users = User::with('role')->find($userid);
        }
        $roles = Role::pluck('role_name', 'id');
        return view('admin.user_profile', compact('users', 'roles'));
    }

    public function Profileupdate(Request $request)
    {
        // $request->validate([
        //     'id' => 'required|exists:users,id',
        //     'name' => 'required|string|max:255',
        // ]);

        $user = Employee::findOrFail($request->input('id'));

        $updateData = [
            'firstname' => $request->input('firstname'),
            'email' => $request->input('email'),
            'role_id' => $request->input('role_id'),
        ];

        if ($request->hasFile('profilepic')) {
            $image = $request->file('profilepic');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $filename);
            $updateData['profilepic'] = $filename;
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'result' => $user->fresh(),
        ], 200);
    }
}

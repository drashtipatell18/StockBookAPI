<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'success' => false,
                'message' => 'Credential does not found in our records',
            ], 401);
        }
        $user = User::where('email', $request->input('email'))->first();
        $token = $user->createToken($user->role_id)->plainTextToken;
        return response()->json([
            'success' => true,
            'message' => 'Login successfully',
            'result' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'access_token' => $token,
                'role' => Role::find($user->role_id)->role_name,
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
        $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->input('id'),
            'role_id' => 'required|exists:roles,id',
        ]);
    
        $user = User::findOrFail($request->input('id'));
    
        $updateData = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role_id' => $request->input('role_id'),
        ];
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $filename);
            $updateData['image'] = $filename;
        }
    
        $user->update($updateData);
    
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'result' => $user->fresh(),
        ], 200);
    }
}

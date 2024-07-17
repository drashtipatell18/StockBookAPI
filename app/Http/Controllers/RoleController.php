<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function role()
    {
        $roles = Role::all();
        return response()->json([
            'success' => true,
            'message' => 'Role data successfully',
            'result' => $roles,
        ], 200);
        // return response()->json($roles, 200);
    }

    public function roleStore(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|unique:roles,role_name',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails',
                'error' => $validator->errors()
            ], 401);
        }

        $role = Role::create([
            'role_name' => $request->input('role_name'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'result' => $role,
        ], 200);
    }

    public function roleUpdate(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:roles,id',
            'role_name' => 'required|string|max:255',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails',
                'error' => $validator->errors()
            ], 401);
        }

        $role = Role::find($request->input('id'));
        $role->update([
            'role_name' => $request->input('role_name'),
            'id' => $request->input('id')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Role Updated successfully',
            'result' => $role,
        ], 200);
    }

    public function roleDestroy(Request $request)
    {
        $role = Role::find($request->input('id'));
        $role->delete();
        return response()->json([
            'success' => true,
            'message' => 'Role Deleted successfully',
            'result'    =>  $role,
        ], 200);
    }
}

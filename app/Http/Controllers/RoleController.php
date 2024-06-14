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
        return response()->json($roles, 200);
    }

    public function roleStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
             'role_name' => 'required',
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
            'role' => $role,
        ], 200);
    }

    public function roleUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
             'role_name' => 'required',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails',
                'error' => $validator->errors()
            ], 401);
        }

        $role = Role::find($id);
        $role->update([
            'role_name' => $request->input('role_name')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Role Updated successfully',
            'role' => $role,
        ], 200); 
    }

    public function roleDestroy($id)
    {
        $role = Role::find($id);
        $role->delete();
        return response()->json([
            'success' => true,
            'message' => 'Role Deleted successfully',
        ], 200);    }
}

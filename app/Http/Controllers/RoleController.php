<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return $this->successResponse($roles, 'Roles retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
            'guard_name' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return $this->createdResponse($role->load('permissions'), 'Role created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return $this->notFoundResponse('Role not found');
        }

        return $this->successResponse($role, 'Role retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->notFoundResponse('Role not found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $id,
            'guard_name' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $role->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? $role->guard_name
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return $this->successResponse($role->load('permissions'), 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->notFoundResponse('Role not found');
        }

        if ($role->name === 'Super Admin') {
            return $this->errorResponse('Cannot delete Super Admin role', 403);
        }

        $role->delete();

        return $this->successResponse(null, 'Role deleted successfully');
    }
}

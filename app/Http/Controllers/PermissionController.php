<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::all();
        return $this->successResponse($permissions, 'Permissions retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name',
            'guard_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web'
        ]);

        return $this->createdResponse($permission, 'Permission created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->notFoundResponse('Permission not found');
        }

        return $this->successResponse($permission, 'Permission retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->notFoundResponse('Permission not found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name,' . $id,
            'guard_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $permission->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? $permission->guard_name
        ]);

        return $this->successResponse($permission, 'Permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->notFoundResponse('Permission not found');
        }

        $permission->delete();

        return $this->successResponse(null, 'Permission deleted successfully');
    }
}

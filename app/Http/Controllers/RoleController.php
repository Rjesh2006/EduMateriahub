<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Crypt;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:view roles', only: ['index']),
            new Middleware('permission:edit roles', only: ['edit']),
            new Middleware('permission:create roles', only: ['create']),
            new Middleware('permission:delete roles', only: ['destroy']),
        ];
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::orderBy('name', 'ASC')->paginate(5);
        return view('roles.list', [
            'roles' => $roles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name', 'ASC')->get();

        return view('roles.create', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3'
        ]);

        if ($validator->passes()) {


            $role = Role::create(['name' => $request->name]);

            if (!empty($request->permissions)) {
                foreach ($request->permissions as $name) {
                    $role->givePermissionTo($name);
                }
            }

            return redirect()->route('roles.index')->with('success', 'Roles Addad successfully');


        } else {

            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($encryptedId)
{
    $id = Crypt::decryptString($encryptedId);
    $role = Role::findOrFail($id);

    $hasPermissions = $role->permissions->pluck('name');
    $permissions = Permission::orderBy('name', 'ASC')->get();

    return view('roles.edit', compact('role', 'permissions', 'hasPermissions'));
}


    /**
     * Update the specified resource in storage.
     */

     public function update(Request $request, $encryptedId)
     {
         $id = Crypt::decryptString($encryptedId);
         $role = Role::findOrFail($id);
     
         $validator = Validator::make($request->all(), [
             'name' => 'required|unique:roles,name,' . $id . ',id',
         ]);
     
         if ($validator->passes()) {
             $role->name = $request->name;
             $role->save();
     
             $role->syncPermissions($request->permissions ?? []);
             return redirect()->route('roles.index')->with('success', 'Role updated successfully');
         } else {
             return redirect()->route('roles.edit', $encryptedId)->withInput()->withErrors($validator);
         }
     }
     


    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        // Fetch the role
        $role = Role::findOrFail($id);

        try {
            // Delete the role
            $role->delete();

            // Return success response for SweetAlert
            return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            // Handle any errors during deletion
            return redirect()->route('roles.index')->with('error', 'Error deleting the role: ' . $e->getMessage());
        }
    }


}
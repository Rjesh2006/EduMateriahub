<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Crypt;

class PermissionController extends Controller implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     */

    public static function middleware()
    {
        return [
            new Middleware('permission:view permissions', only: ['index']),
            new Middleware('permission:edit permissions', only: ['edit']),
            new Middleware('permission:create permissions', only: ['create']),
            new Middleware('permission:delete permissions', only: ['destroy']),
        ];
    }

    public function index()
    {
        $permissions = Permission::orderBy('created_at', 'ASC')->paginate(200);
        return view('permissions.list', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions|min:3'
        ]);

        if ($validator->passes()) {

            Permission::create(['name' => $request->name]);

            return redirect()->route('permissions.index')->with('success', 'Permission created successfully');


        } else {

            return redirect()->route('permissions.create')->withInput()->withErrors($validator);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $encryptedId)
    {
        // Decrypt the encrypted ID
        $id = Crypt::decrypt($encryptedId);
    
        // Find the permission by decrypted ID
        $permission = Permission::findOrFail($id);
    
        return view('permissions.edit', [
            'permission' => $permission,
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $encryptedId)
{
    $id = Crypt::decrypt($encryptedId); // Decrypt the encrypted ID
    $permission = Permission::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'name' => 'required|unique:permissions,name,' . $id . '|min:3',
    ]);

    if ($validator->passes()) {
        $permission->name = $request->name;
        $permission->save();

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    } else {
        return redirect()->route('permissions.edit', Crypt::encrypt($id))->withInput()->withErrors($validator);
    }
}



    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);

        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully');
    }

}
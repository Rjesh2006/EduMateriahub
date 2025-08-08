<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:view admins', only: ['index']),
            new Middleware('permission:edit admins', only: ['edit']),
            new Middleware('permission:delete admins', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch only users with the "Admin" role
        $admins = User::role('Admin')->get();

        // Encrypt each admin's ID before passing to the view
        foreach ($admins as $admin) {
            $admin->encrypted_id = encrypt($admin->id);
        }

        // Pass admin data to the view
        return view('admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|string|max:15',
            'department' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            
        ]);

        // Store the admin as a user
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
            'password' => Hash::make($request->password),
        ]);

        // Assign the "Admin" role
        $admin->assignRole('Admin');

        return redirect()->route('admins.index')->with('success', 'Admin added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $encryptedId)
    {
        // Decrypt the ID before fetching the user
        $id = decrypt($encryptedId);

        // Fetch the admin by decrypted ID
        $admin = User::findOrFail($id);

        return view('admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $encryptedId)
    {
        // Decrypt the ID before fetching the user
        $id = decrypt($encryptedId);

        $admin = User::findOrFail($id);

        // Fetch all roles
        $roles = Role::all();

        // Get assigned roles for the user
        $hasRoles = $admin->roles;

        return view('admins.edit', compact('admin', 'roles', 'hasRoles'));
    }

    /**
     * Update the specified resource in storage.
     */


     public function update(Request $request, string $encryptedId)
     {
         // Decrypt the ID before fetching the user
         $id = Crypt::decryptString($encryptedId);
     
         $admin = User::findOrFail($id);
     
         // Validate the request
         $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|email|max:255|unique:users,email,' . $id,
             'phone' => 'required|string|max:15',
             'department' => 'required|string|max:255',
            'semester' => $request->has('role') && in_array('student', $request->role)
                ? 'required|integer|min:1|max:8'
                : 'nullable',
            'roll_no' => $request->has('role') && in_array('student', $request->role)
                ? 'required|string|max:255'
                : 'nullable',
         ]);
     
         // Update admin details
         $admin->update([
             'name' => $request->name,
             'email' => $request->email,
             'phone' => $request->phone,
             'department' => $request->department,
         ]);
     
         // Update roles
         if ($request->has('role')) {
             $roles = $request->input('role'); // Array of role names
             $admin->syncRoles($roles); // Sync roles for the admin
         } else {
             // If no roles are selected, remove all roles
             $admin->syncRoles([]);
         }
     
         // Update semester if applicable
         if ($request->has('semester')) {
             $admin->semester = $request->semester;
             $admin->roll_no = $request->roll_no; 
             $admin->save();
         } else {
             // Clear semester if the role is not 'student'
             $admin->semester = null;
             $admin->save();
         }
     
         return redirect()->route('admins.index')->with('success', 'Admin updated successfully!');
     }
     

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $encryptedId)
    {
        // Decrypt the ID before fetching the user
        $id = decrypt($encryptedId);

        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'Admin deleted successfully!');
    }
}

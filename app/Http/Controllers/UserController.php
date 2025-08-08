<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:view users', only: ['index']),
            new Middleware('permission:edit users', only: ['edit']),
            new Middleware('permission:create users', only: ['create']),
            new Middleware('permission:delete users', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::oldest()->get();


        // Encrypt user IDs before passing to the view
        $encryptedUsers = $users->map(function($user) {
            $user->encrypted_id = Crypt::encryptString($user->id); // Encrypt the ID
            return $user;
        });

        return view('users.list', [
            'users' => $encryptedUsers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name', 'ASC')->get(); // Saare roles fetch kar rahe hain
        return view('auth.register', compact('roles')); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone|max:15',
            'department' => 'required|string|max:255',
            'semester' => 'nullable|integer|min:1|max:10',
            'roll_no' => 'nullable|string|min:3|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        // Create User
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->department = $request->department;
        $user->semester = $request->semester;
        $user->roll_no = $request->roll_no;
        $user->password = bcrypt($request->password);
        $user->save();

        // Assign role to the user
        $user->assignRole($request->role);

        // Redirect with success message
        return redirect()->route('users.list')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Handle showing the specific user if needed
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $userId = Crypt::decryptString($id); // Decrypt the user ID
        $user = User::findOrFail($userId); // Fetch the user details
        $roles = Role::all(); // Get all roles
        $hasRoles = $user->roles; // Get the roles assigned to the user

        return view('users.edit', compact('user', 'roles', 'hasRoles'));
    }

    /**
     * Update the user details.
     */
    public function update(Request $request, $id)
    {
        $userId = Crypt::decryptString($id); // Decrypt the user ID
        $user = User::findOrFail($userId);

        // Validate the incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:15',
            'department' => 'required|string',
            'role' => 'required|array', // Role is mandatory
            'semester' => 'nullable|integer|min:1|max:8',
            'roll_no' => 'nullable|string|max:50',
        ]);

        // Update user details
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'department' => $validated['department'],
        ]);

        // Sync roles
        $user->syncRoles($validated['role']);

        // Check if the 'student' role is selected
        if (in_array('student', $validated['role'])) {
            // Update semester and roll number if 'student' role is selected
            $user->semester = $validated['semester'] ?? null;
            $user->roll_no = $validated['roll_no'] ?? null;
        } else {
            // Clear semester and roll number if 'student' role is not selected
            $user->semester = null;
            $user->roll_no = null;
        }

        $user->save(); // Save the changes

        // Redirect back with a success message
        return redirect()->route('users.list')->with('success', 'User updated successfully!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $encryptedId)
    {
        // Decrypt the ID
        $id = Crypt::decryptString($encryptedId);

        // Find the user by ID
        $user = User::findOrFail($id);

        try {
            // Delete the user
            $user->delete();

            // Redirect back with success message
            return redirect()->route('users.list')->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            // Redirect back with error message if deletion fails
            return redirect()->route('users.list')->with('error', 'Failed to delete the user. Please try again later.');
        }
    }
}

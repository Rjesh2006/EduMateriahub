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

class FacultyController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:view faculties', only: ['index']),
            new Middleware('permission:edit faculties', only: ['edit']),
            new Middleware('permission:delete faculties', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch only users with the "Faculty" role
        $faculties = User::role('Faculty')->get();

        // Encrypt the IDs
        foreach ($faculties as $faculty) {
            $faculty->encrypted_id = Crypt::encryptString($faculty->id);
        }

        // Pass faculty data to the view
        return view('faculties.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auth.register');
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

        // Store the faculty as a user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('faculties.index')->with('success', 'Faculty added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $encrypted_id)
    {
        $id = Crypt::decryptString($encrypted_id);
        $faculty = User::findOrFail($id);
        return view('faculties.show', compact('faculty'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $encrypted_id)
    {
        $id = Crypt::decryptString($encrypted_id);
        $faculty = User::findOrFail($id);
        $roles = Role::orderBy('name', 'ASC')->get();
        $hasRoles = $faculty->roles->pluck('id');
        return view('faculties.edit', [
            'faculty' => $faculty,
            'roles' => $roles,
            'hasRoles' => $hasRoles
        ]);
    }

    /**
 * Update the specified resource in storage.
 */
public function update(Request $request, string $encrypted_id)
{
    // Decrypt the encrypted ID
    $id = Crypt::decryptString($encrypted_id);

    // Fetch the faculty user by ID
    $faculty = User::findOrFail($id);

    // Validate the input data
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $id,
        'phone' => 'required|string|max:15|unique:users,phone,' . $id,
        'department' => 'required|string|max:255',
        'semester' => $request->has('role') && in_array('student', $request->role)
            ? 'required|integer|min:1|max:8'
            : 'nullable',
        'roll_no' => $request->has('role') && in_array('student', $request->role)
            ? 'required|string|max:255'
            : 'nullable',
        'role' => 'required|array|min:1', // Ensure at least one role is selected
        'role.*' => 'string|exists:roles,name',
    ]);

    // Update the faculty details
    $faculty->update([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'phone' => $validatedData['phone'],
        'department' => $validatedData['department'],
    ]);

    // Update semester and roll_no if the "student" role is assigned
    if (in_array('student', $request->role)) {
        $faculty->semester = $validatedData['semester'];
        $faculty->roll_no = $validatedData['roll_no'];
    } else {
        $faculty->semester = null; // Clear semester if "student" role is removed
        $faculty->roll_no = null; // Clear roll_no if "student" role is removed
    }
    $faculty->save();

    // Sync roles for the faculty
    $faculty->syncRoles($validatedData['role']);

    // Redirect to the faculty index page with a success message
    return redirect()->route('faculties.index')->with('success', 'Faculty updated successfully!');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $encrypted_id)
    {
        $id = Crypt::decryptString($encrypted_id);
        $faculty = User::findOrFail($id);
        $faculty->delete();

        return redirect()->route('faculties.index')->with('success', 'Faculty deleted successfully!');
    }
}

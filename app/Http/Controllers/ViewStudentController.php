<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class ViewStudentController extends Controller
{
    public static function middleware()
    {
        return [
            'permission:view students' => ['only' => ['index']],
            'permission:edit students' => ['only' => ['edit']],
            'permission:delete students' => ['only' => ['destroy']],
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch students and encrypt IDs
        $students = User::role('student')->get()->map(function ($student) {
            $student->encrypted_id = Crypt::encryptString($student->id);
            return $student;
        });

        return view('students.index', compact('students'));
    }
    
    /**
     * Show the form for creating a new resource (not implemented).
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage (not implemented).
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $decryptedId = Crypt::decryptString($id);
        $student = User::findOrFail($decryptedId);
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $decryptedId = Crypt::decryptString($id);
        $student = User::findOrFail($decryptedId);
        $roles = Role::orderBy('name', 'ASC')->get();
        $hasRoles = $student->roles->pluck('id');

        return view('students.edit', compact('student', 'roles', 'hasRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $decryptedId = Crypt::decryptString($id);

        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $decryptedId,
            'phone' => 'nullable|string|max:15',
            'department' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:10',
            'roll_no' => 'nullable|string|max:50',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // Update student details
        $student = User::findOrFail($decryptedId);
        $student->name = $request->input('name');
        $student->email = $request->input('email');
        $student->phone = $request->input('phone');
        $student->department = $request->input('department');
        $student->semester = $request->semester;
        $student->roll_no = $request->roll_no;
       

         // Sync roles
         $roles = $request->role ?? [];
         $student->syncRoles($roles);
        // Preserve existing roles and add/update roles
        if ($request->has('roles')) {
            $student->syncRoles($request->input('roles'));
        } else {
            // If no roles are selected, keep the existing roles
            $student->syncRoles($student->roles->pluck('name')->toArray());
        }
        $student->save();
        return redirect()->route('students.index')->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $decryptedId = Crypt::decryptString($id);
        $student = User::findOrFail($decryptedId);
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully!');
    }

}

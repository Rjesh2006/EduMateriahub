<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Crypt;


class StudyMaterialController extends Controller implements HasMiddleware
{


    public static function middleware()
    {
        return [
            new Middleware('permission:view study material', only: ['index']),
            new Middleware('permission:edit study material', only: ['edit']),
            new Middleware('permission:create study material', only: ['create']),
            new Middleware('permission:delete study material', only: ['destroy']),

        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $department = auth()->user()->department; // User ka department
        $semester = auth()->user()->semester; // User ka semester
        $faculty_name = auth()->user()->name; // faculty ka name
        $user = auth()->user(); // Currently logged-in user
        $roles = $user->getRoleNames();

        // Agar user ka role 'SuperAdmin' ya 'Admin' hai, to saare study_materials dikhayenge
        if ($user->hasRole('SuperAdmin') || $user->hasRole('Admin')) {
            $study_materials = StudyMaterial::all(); // Saare study_materials
        } elseif ($user->hasRole('Faculty')) {
            $study_materials = StudyMaterial::where(function ($query) use ($department) {
                $query->where('department', $department)
                    ->orWhere('department', 'ELECTIVE');
            })
                ->where('faculty_name', $faculty_name)
                ->get();
        } else {
            // Agar role kuch aur ho, to department-wise filter karein
            $study_materials = StudyMaterial::where('department', $department)->where('semester', $semester)->get();
        }
        return view('study_materials.index', compact('study_materials', 'roles'));
    }

    public function elective()
    {
        return view('study_materials.elective');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user(); // Currently logged-in user
        $roles = $user->getRoleNames(); // Fetch assigned roles for the user

        // Filter faculties based on role
        if ($roles->contains('Admin') || $roles->contains('SuperAdmin')) {
            // If Admin or SuperAdmin, fetch all users with the "Faculty" role
            $faculties = User::role('Faculty')->get(); // Assuming you use Spatie Roles
        } elseif ($roles->contains('Faculty')) {
            // If the user is a faculty, show only their own data
            $faculties = collect([$user]); // Wrap in a collection for consistency
        } else {
            // If the user doesn't have access, return an empty collection
            $faculties = collect();
        }

        // Determine departments
        
            // If Admin or SuperAdmin, show all departments
            $departments = [
                'Applied Psychology',
                'Computer Science',
                'B.voc(Software Development)',
                'Economics',
                'English',
                'Environmental Studies',
                'Commerce',
                'Punjabi',
                'Hindi',
                'History',
                'Management Studies',
                'Mathematics',
                'Philosophy',
                'Physical Education',
                'Political Science',
                'Statistics',
                'B.voc(Banking Operations)',
                'ELECTIVE',
            ];
       

        // Initial empty subject list
        $subjects = [];

        return view('study_materials.create', compact('departments', 'subjects', 'faculties', 'roles'));
    }



    public function filterSubjects(Request $request)
    {
        $validated = $request->validate([
            'subject_type' => 'required|string',
            'department' => 'required|string',
            'semester' => 'required|integer',
        ]);

        // Fetch the subjects
        $subjects = Subject::where('subject_type', $validated['subject_type'])
            ->where('department', $validated['department'])
            ->where('semester', $validated['semester'])
            ->get();

        if ($subjects->isEmpty()) {
            return response()->json(['error' => 'No subjects found'], 404);
        }

        return response()->json($subjects->pluck('subject_name', 'id'));
    }

    /**
     * Fetch study materials for a specific subject.
     */

    public function filterStudy(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'subject_type' => 'required|string',
            'department' => 'required|string',
            'semester' => 'required|string',
            'subject_name' => 'required|string',
        ]);

        // Trim subject_name to remove any extra spaces
        $validated['subject_name'] = trim($validated['subject_name']);

        try {
            $studyMaterials = StudyMaterial::where('subject_type', $validated['subject_type'])
                ->where('department', $validated['department'])
                ->where('semester', $validated['semester'])

                ->get();



            // Log the executed query


            // Check if any data is found
            if ($studyMaterials->isEmpty()) {
                return response()->json(['message' => 'No study materials found for the provided filters.'], 404);
            }

            // Return the filtered data as JSON
            return response()->json(['data' => $studyMaterials], 200);

        } catch (\Exception $e) {
            // Log error details
            return response()->json(['error' => 'No study materials found for the provided filters.', 'details' => $e->getMessage()], 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'subject_type' => 'required|string',
            'department' => 'required|string',
            'semester' => 'required|string',
            'subject_name' => 'required|string',
            'faculty_name' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:2048', // Adjust file types and size as needed
            'description' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('study_materials', 'public'); // Store in the 'public/study_materials' directory
        }

        // Create a new StudyMaterial record
        StudyMaterial::create([
            'subject_type' => $validated['subject_type'],
            'department' => $validated['department'],
            'semester' => $validated['semester'],
            'subject_name' => $validated['subject_name'],
            'faculty_name' => $validated['faculty_name'], // Assuming 'faculty_id' is the column in the database
            'file' => $filePath, // Store the file path
            'description' => $validated['description'],
            'uploaded_by' => auth()->id(), // Save the ID of the currently logged-in user
        ]);

        // Redirect back with success message
        return redirect()->route('study_materials.index')->with('success', 'Study material added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $studyMaterial = StudyMaterial::findOrFail($id); // Find study material or throw 404
        return view('study_materials.show', compact('studyMaterial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $studyMaterial = StudyMaterial::findOrFail($id); // Find study material or throw 404
        $user = auth()->user();
        $roles = $user->getRoleNames();

        // Fetch faculties based on role
        if ($roles->contains('Admin') || $roles->contains('SuperAdmin')) {
            $faculties = User::role('Faculty')->get();
        } elseif ($roles->contains('Faculty')) {
            $faculties = collect([$user]);
        } else {
            $faculties = collect();
        }

        // Determine departments
            $departments = [
                'Applied Psychology',
                'Computer Science',
                'B.voc(Software Development)',
                'Economics',
                'English',
                'Environmental Studies',
                'Commerce',
                'Punjabi',
                'Hindi',
                'History',
                'Management Studies',
                'Mathematics',
                'Philosophy',
                'Physical Education',
                'Political Science',
                'Statistics',
                'B.voc(Banking Operations)',
                'ELECTIVE',
            ];
       

        // Fetch all subjects
        $subjects = Subject::all()->pluck('subject_name', 'id');

        return view('study_materials.edit', compact('studyMaterial', 'departments', 'subjects', 'faculties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $studyMaterial = StudyMaterial::findOrFail($id);

        // Validate the input
        $validated = $request->validate([
            'subject_type' => 'required|string',
            'department' => 'required|string',
            'semester' => 'required|string',
            'subject_name' => 'required|string',
            'faculty_name' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:2048',
            'description' => 'nullable|string',
        ]);

        // Handle file upload if provided
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('study_materials', 'public'); // Store in the 'public/study_materials' directory
            $validated['file'] = $filePath;
        }

        // Update the study material
        $studyMaterial->update($validated);

        // Redirect back with success message
        return redirect()->route('study_materials.index')->with('success', 'Study material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $studyMaterial = StudyMaterial::findOrFail($id);

        // Delete the file from storage if it exists
        if ($studyMaterial->file && Storage::disk('public')->exists($studyMaterial->file)) {
            Storage::disk('public')->delete($studyMaterial->file);
        }

        // Delete the study material record
        $studyMaterial->delete();

        // Redirect back with success message
        return redirect()->route('study_materials.index')->with('success', 'Study material deleted successfully.');
    }
}
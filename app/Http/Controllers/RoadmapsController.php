<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roadmaps;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Crypt;
use App\Models\Subject;

class RoadmapsController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:view roadmaps', only: ['index']),
            new Middleware('permission:create roadmaps', only: ['create']),
            new Middleware('permission:edit roadmaps', only: ['edit']),
            new Middleware('permission:delete roadmaps', only: ['destroy']),
        ];
    }


    /**
     * Display a listing of the roadmaps.
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
            $roadmaps = Roadmaps::all(); // Saare roadmaps
        } elseif ($user->hasRole('Faculty')) {
            $roadmaps = Roadmaps::where('faculty_name', $faculty_name)
                ->get();
        } else {
            // Agar role kuch aur ho, to department-wise filter karein
            $roadmaps = Roadmaps::where('department', $department)->where('semester', $semester)->get();
        }
        return view('roadmaps.index', compact('roadmaps'));
    }

    /**
     * Show the form for creating a new roadmap.
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
        // if ($roles->contains('Admin') || $roles->contains('SuperAdmin')) {
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
        // } else {
             // If the user has other roles, show only their department
          //   $departments = [$user->department, 'ELECTIVE'];
         //}
 
         // Initial empty subject list
         $subjects = [];
 
         return view('roadmaps.create', compact('departments', 'subjects', 'faculties', 'roles'));
     }





    /**
     * Store a newly created roadmap in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'subject_type' => 'required|string|max:255',
            'semester' => 'required|integer',
            'subject_name' => 'required|string|max:255',
            'faculty_name' => 'required|string|max:255', // Fixed typo
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Optional file upload
            'description' => 'required|string',
        ]);
        


        try {
            // Handle file upload
            $filePath = $request->file('file')->store('roadmaps', 'public');

            Roadmaps::create([
                'department' => $validated['department'],
                'subject_type' => $validated['subject_type'],
                'semester' => $validated['semester'],
                'subject_name' => $validated['subject_name'],
                'faculty_name' => $validated['faculty_name'],
                'file' => $filePath,
                'description' => $validated['description'],
                
            ]);

            // Redirect back with a success message
            return redirect()->route('roadmaps.index')->with('success', 'Roadmap added successfully!');
        } catch (\Exception $e) {
            // Handle errors and redirect back with an error message
            return redirect()->back()->withErrors(['error' => 'An error occurred while adding the roadmap. Please try again later.']);
        }
    }


    /**
     * Show the specified roadmap.
     */
    public function show(Roadmaps $roadmap, $encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $roadmap = Roadmaps::find($id);
        // dd($roadmap);
        
        return view('roadmaps.show', compact('roadmap'));
    }


    public function edit(Roadmaps $roadmap, $encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $id = Crypt::decryptString($encryptedId);
        $studyMaterial = Roadmaps::findOrFail($id); // Find study material or throw 404
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
        $roadmap = Roadmaps::find($id);
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
        $subjects = Subject::all()->pluck('subject_name', 'id');
        return view('roadmaps.edit', compact('roadmap','departments','subjects','faculties'));
    }

    public function update(Request $request, Roadmaps $roadmap, $encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $roadmap = Roadmaps::find($id);
    
        // Validate the request
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'subject_type' => 'required|string|max:255',
            'semester' => 'required|integer',
            'subject_name' => 'required|string|max:255',
            'faculty_name' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Optional file upload
            'description' => 'required|string',
        ]);
    
        // Handle file upload (if new file is uploaded)
        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            if ($roadmap->file && Storage::exists('public/' . $roadmap->file)) {
                Storage::delete('public/' . $roadmap->file);
            }
    
            // Store the new file
            $filePath = $request->file('file')->store('roadmaps', 'public');
            $roadmap->file = $filePath;
        }
    
        // Update the roadmap record
        $roadmap->update([
            'department' => $validated['department'],
            'subject_type' => $validated['subject_type'],
            'semester' => $validated['semester'],
            'subject_name' => $validated['subject_name'],
            'faculty_name' => $validated['faculty_name'],
            'file' => $roadmap->file ?? null, // Keep the old file if no new file is uploaded
            'description' => $validated['description'],
        ]);
    
        return redirect()->route('roadmaps.index')->with('success', 'Roadmap updated successfully!');
    }



    public function destroy($id)
    {
        // Find the roadmap by ID
        $roadmap = Roadmaps::find($id);

        // Check if roadmap exists
        if (!$roadmap) {
            return redirect()->route('roadmaps.index')->with('error', 'Roadmap not found!');
        }

        // Delete the roadmap record
        $roadmap->delete();

        // Redirect back with success message
        return redirect()->route('roadmaps.index')->with('success', 'Roadmap deleted successfully!');
    }

}
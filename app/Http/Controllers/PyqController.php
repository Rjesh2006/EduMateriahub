<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Pyq;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Crypt;


class PyqController extends Controller implements HasMiddleware
{



    public static function middleware()
    {
        return [
            new Middleware('permission:view pyq', only: ['index']),
            new Middleware('permission:edit pyq', only: ['edit']),
            new Middleware('permission:create pyq', only: ['create']),
            new Middleware('permission:delete pyq', only: ['destroy']),
           
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $department = auth()->user()->department; // User ka department
        $semester = auth()->user()->semester; // User ka semester
        $faculty_name = auth()->user()->name; // User ka semester
        $user = auth()->user(); // Currently logged-in user
        $roles = $user->getRoleNames();

         // Agar user ka role 'SuperAdmin' ya 'Admin' hai, to saare roadmaps dikhayenge
         if ($user->hasRole('SuperAdmin') || $user->hasRole('Admin')) {
            $pyqs = Pyq::all(); // Saare pyqs
        }
        
        elseif($user->hasRole('Faculty')){
            $pyqs = Pyq::where(function($query) use ($department) {
                $query->where('department', $department)
                      ->orWhere('department', 'ELECTIVE');
            })
            ->where('faculty_name', $faculty_name)
            ->get();
            
            
        }
        
        else {
            // Agar role kuch aur ho, to department-wise filter karein
            $pyqs = Pyq::where('department', $department)->where('semester', $semester)->get();
        }
        // Fetch all PYQs from the database
        // $pyqs = Pyq::all(); // You can use pagination if necessary
        return view('pyq.index', compact('pyqs'));
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

        return view('pyq.create', compact('departments', 'subjects', 'faculties', 'roles'));
    }



    public function elective()
    {
        return view('pyq.elective');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'subject_type' => 'required|string',
            'department' => 'required|string',
            'semester' => 'required|integer',
            'subject_name' => 'required|string',
            'faculty_name' => 'required|string',
            'year' => 'required|integer',
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048', // You can change file types or size as per your requirement
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->storeAs('pyq_files', time() . '_' . $file->getClientOriginalName(), 'public');
        } else {
            return redirect()->back()->with('error', 'No file uploaded');
        }

        // Create the new PYQ record in the database
        $pyq = new Pyq();
        $pyq->subject_type = $validated['subject_type'];
        $pyq->department = $validated['department'];
        $pyq->semester = $validated['semester'];
        $pyq->subject_name = $validated['subject_name'];
        $pyq->faculty_name = $validated['faculty_name'];
        $pyq->year = $validated['year'];
        $pyq->file = $filePath; // Save the file path

        // Save the PYQ record
        $pyq->save();

        // Redirect with success message
        return redirect()->route('pyq.index')->with('success', 'PYQ added successfully');
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
    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $encryptedId)
    {

        $id = Crypt::decryptString($encryptedId);
        // Fetch the PYQ record based on the ID
        $pyq = Pyq::findOrFail($id);

        // Fetch faculties based on roles
        $user = auth()->user(); // Currently logged-in user
        $roles = $user->getRoleNames(); // Fetch assigned roles for the user

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

        // Determine departments based on roles
        if ($roles->contains('Admin') || $roles->contains('SuperAdmin')) {
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
        } else {
            // If the user has other roles, show only their department
            $departments = [$user->department, 'ELECTIVE'];
        }

        // Fetch subjects based on department, subject type, and semester
        $subjects = Subject::where('department', $pyq->department)
            ->where('subject_type', $pyq->subject_type)
            ->where('semester', $pyq->semester)
            ->get();

        // Pass the existing PYQ record, subjects, and other data to the edit view
        return view('pyq.edit', compact('pyq', 'departments', 'faculties', 'roles', 'subjects'));
    }



    public function filter_Subjects(Request $request)
    {
        $validated = $request->validate([
            'subject_type' => 'required|string',
            'department' => 'required|string',
            'semester' => 'required|integer',
        ]);

        // Fetch the subjects based on the provided filters
        $subjects = Subject::where('subject_type', $validated['subject_type'])
            ->where('department', $validated['department'])
            ->where('semester', $validated['semester'])
            ->get();

        if ($subjects->isEmpty()) {
            return response()->json([], 404); // Return an empty array with a 404 status if no subjects found
        }

        // Map the subjects to return only the id and name
        $subjectData = $subjects->map(function ($subject) {
            return [
                'id' => $subject->id,
                'name' => $subject->subject_name,
            ];
        });

        return response()->json($subjectData); // Return the subject data as JSON
    }



    public function filterPyq(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'subject_type' => 'required|string',
            'department' => 'required|string',
            'semester' => 'required|string',
            'subject_name' => 'required|string',
            'year' => 'required|integer', // Ensure year is validated
        ]);

        // Trim subject_name to remove any extra spaces
        $validated['subject_name'] = trim($validated['subject_name']);

        try {
            // Fetch PYQs based on the provided filters
            $pyqs = Pyq::where('subject_type', $validated['subject_type'])
                ->where('department', $validated['department'])
                ->where('semester', $validated['semester'])

                ->where('year', $validated['year']) // Add year filter
                ->get();

            // Log the executed query for debugging

            // Check if any data is found
            if ($pyqs->isEmpty()) {
                return response()->json(['message' => 'No PYQs found for the provided filters.'], 404);
            }

            // Return the filtered data as JSON
            return response()->json(['data' => $pyqs], 200);

        } catch (\Exception $e) {
            // Log error details
            return response()->json(['error' => 'An error occurred while fetching PYQs.', 'details' => $e->getMessage()], 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */

   /**
 * Update the specified resource in storage.
 */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        // Validate incoming data
        $validated = $request->validate([
            'subject_type' => 'required|string',
            'department' => 'required|string',
            'semester' => 'required|integer',
            'subject_name' => 'required|string',
            'faculty_name' => 'required|string',
            'year' => 'required|integer',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // File is optional during update
        ]);


        // Fetch the existing PYQ record
        $pyq = Pyq::findOrFail($id);

        // Handle file upload (only if a new file is uploaded)
        if ($request->hasFile('file')) {
            // Delete the old file from the storage
            if ($pyq->file) {
                Storage::delete('public/' . $pyq->file);
            }

            // Store the new file
            $file = $request->file('file');
            $filePath = $file->storeAs('pyq_files', time() . '_' . $file->getClientOriginalName(), 'public');
        } else {
            // If no new file is uploaded, keep the existing file path
            $filePath = $pyq->file;
        }

        // Update the PYQ record with new data
        $pyq->subject_type = $validated['subject_type'];
        $pyq->department = $validated['department'];
        $pyq->semester = $validated['semester'];
        $pyq->subject_name = $validated['subject_name'];
        $pyq->faculty_name = $validated['faculty_name'];
        $pyq->year = $validated['year'];
        $pyq->file = $filePath; // Update the file path (or keep the old one if no new file is uploaded)

        // Save the updated PYQ record
        $pyq->save();

        // Redirect with success message
        return redirect()->route('pyq.index')->with('success', 'PYQ updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Fetch the PYQ record based on the ID
        $pyq = Pyq::findOrFail($id);

        // Delete the file from storage if it exists
        if ($pyq->file) {
            Storage::delete('public/' . $pyq->file); // Delete the file from the storage
        }

        // Delete the PYQ record from the database
        $pyq->delete();

        // Redirect with success message
        return redirect()->route('pyq.index')->with('success', 'PYQ deleted successfully');
    }
}
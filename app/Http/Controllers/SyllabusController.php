<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Syllabus;
use App\Models\Subject;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Crypt;

class SyllabusController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:view syllabus', only: ['index']),
            new Middleware('permission:create syllabus', only: ['create']),
            new Middleware('permission:edit syllabus', only: ['edit']),
            new Middleware('permission:delete syllabus', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the syllabus.
     */
    public function index()
    {
        $syllabus = Syllabus::all();

        return view('syllabus.index', compact('syllabus'));
    }


    /**
     * Show the form for creating a new syllabus.
     */
    public function create()
    {
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
            
        ];


        return view('syllabus.create', compact('departments'));
    }



    public function filterSubjects(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'subject_type' => 'required|string',
            'department' => 'required|string',
            'semester' => 'required|integer',
        ]);

        // Initialize the query builder for the Subject model
        $query = Subject::query();

        // If the subject type is either 'CORE' or 'DSE', filter by department and semester
        if (in_array($validated['subject_type'], ['CORE', 'DSE'])) {
            $query->where('subject_type', $validated['subject_type'])
                ->where('department', $validated['department'])
                ->where('semester', $validated['semester']);
        } else {
            // If the subject type is 'SEC', 'VAC', 'GE', or 'AEC', only filter by semester
            $query->where('subject_type', $validated['subject_type'])
                ->where('semester', $validated['semester']);
        }

        // Fetch the subjects based on the filters
        $subjects = $query->get();

        // Return subjects as JSON response with id and name
        return response()->json($subjects->pluck('subject_name', 'id'));
    }

    /**
     * Store a newly created syllabus in storage.
     */
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'department' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx',
        ]);

        // Handle file upload and get the path
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('syllabus', 'public');
        } else {
            // Manually throw an error if file is not provided
            return redirect()->back()->with('error', 'File is required!');
        }

        // Create the syllabus record
        Syllabus::create([
            'department' => $request->department,
            'file' => $filePath ?? null,
        ]);

        return redirect()->route('syllabus.index')->with('success', 'Syllabus uploaded successfully!');
    }


    /**
     * Show the specified syllabus.
     */
    public function show($encryptedID)
    {
        $id = Crypt::decryptString($encryptedID);
        $syllabus = Syllabus::find($id);

        if (!$syllabus) {
            return redirect()->route('syllabus.index')->with('error', 'Syllabus not found!');
        }

        return view('syllabus.show', compact('syllabus'));
    }

    /**
     * Show the form for editing the specified syllabus.
     */
    public function edit($encryptedID)
    {
        $id = Crypt::decryptString($encryptedID);
        $syllabus = Syllabus::find($id);

        if (!$syllabus) {
            return redirect()->route('syllabus.index')->with('error', 'Syllabus not found!');
        }

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
            
        ];

        return view('syllabus.edit', compact('syllabus', 'departments'));
    }


    /**
     * Update the specified syllabus in storage.
     */
    public function update(Request $request, $encryptedID)
    {
        $id = Crypt::decryptString($encryptedID);
        $syllabus = Syllabus::find($id);

        if (!$syllabus) {
            return redirect()->route('syllabus.index')->with('error', 'Syllabus not found!');
        }

        $validated = $request->validate([
            'department' => 'required_if:subject_type,Core|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);


        if ($request->hasFile('file')) {
            if (Storage::exists('public/' . $syllabus->file)) {
                Storage::delete('public/' . $syllabus->file);
            }

            $filePath = $request->file('file')->store('syllabus', 'public');
            $syllabus->file = $filePath;
        }

        $syllabus->update([
            'department' => $validated['department'],
            'file' => $syllabus->file,
        ]);

        return redirect()->route('syllabus.index')->with('success', 'Syllabus updated successfully!');
    }

    /**
     * Remove the specified syllabus from storage.
     */
    public function destroy($id)
    {
        $syllabus = Syllabus::find($id);

        if (!$syllabus) {
            return redirect()->route('syllabus.index')->with('error', 'Syllabus not found!');
        }

        if (Storage::exists('public/' . $syllabus->file_path)) {
            Storage::delete('public/' . $syllabus->file_path);
        }

        $syllabus->delete();

        return redirect()->route('syllabus.index')->with('success', 'Syllabus deleted successfully!');
    }
}
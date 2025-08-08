<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\User;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class QuizController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:view quiz', only: ['index']),
            new Middleware('permission:create quiz', only: ['create']),
            new Middleware('permission:edit quiz', only: ['edit']),
            new Middleware('permission:destroy quiz', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user(); // Currently logged-in user
        $department = $user->department;
        $semester = $user->semester;
        $faculty_name = $user->name;
        $roles = $user->getRoleNames();


        if ($roles->contains('SuperAdmin') || $roles->contains('Admin')) {
            $quizzes = Quiz::all();
        } elseif ($roles->contains('Faculty')) {

            $quizzes = Quiz::where('faculty_name', $faculty_name)
                ->get();
        } else {

            $quizzes = Quiz::where('department', $department)
                ->where('semester', $semester)
                ->get();
        }

        return view('quizzes.index', compact('quizzes', 'roles'));
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

        return view('quizzes.create', compact('departments', 'subjects', 'faculties', 'roles'));
        // return view('quizzes.create');
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $currentDate = Carbon::now()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        $request->validate([
            'subject_type' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'subject_name' => 'required|string|max:255',
            'faculty_name' => 'required|string|max:255',
            'date' => "required|date|after_or_equal:$currentDate",
            'start_time' => [
                'required',
                function ($attribute, $value, $fail) use ($currentDate, $currentTime, $request) {
                    if ($request->date === $currentDate && $value < $currentTime) {
                        $fail('Start time must be equal to or after the current time for today.');
                    }
                },
            ],
            'end_time' => [
                'required',
                function ($attribute, $value, $fail) use ($currentDate, $currentTime, $request) {
                    if ($request->date === $currentDate && $value < $currentTime) {
                        $fail('End time must be equal to or after the current time for today.');
                    }
                    if ($value <= $request->start_time) {
                        $fail('End time must be after the start time.');
                    }
                },
            ],

        ]);

        $quiz = Quiz::create($request->all());
        return redirect()->route('questions.index', ['quiz' => $quiz->id])->with('success', 'Quiz created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch the quiz and its related questions
        $quiz = Quiz::findOrFail($id);  // Ensure quiz exists
        $questions = Question::where('quiz_id', $id)->get();  // Get questions related to the quiz

        // Return the view with quiz details and questions
        return view('quizzes.show', compact('quiz', 'questions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $quiz = Quiz::findOrFail($id);  // Fetch quiz by ID
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
       

        // Fetch subjects based on department, subject type, and semester
        $subjects = Subject::where('department', $quiz->department)
            ->where('subject_type', $quiz->subject_type)
            ->where('semester', $quiz->semester)
            ->get();

        // Pass the existing quiz record, subjects, and other data to the edit view
        return view('quizzes.edit', compact('quiz', 'departments', 'faculties', 'roles', 'subjects'));
    }


    public function storeInstructions(Request $request, $id)
    {
        // Validate the incoming data
        $request->validate([
            'total_no_of_question' => 'required|integer|min:1',
            'attempt_no' => 'required|integer|min:1',
            'weightage' => 'required|string|max:255',
        ]);

        // Find the quiz by ID
        $quiz = Quiz::findOrFail($id);

        // Update the quiz with the instructions
        $quiz->update([
            'total_no_of_question' => $request->total_no_of_question,
            'attempt_no' => $request->attempt_no,
            'weightage' => $request->weightage,
        ]);

        return redirect()->route('questions.index', $id)->with('success', 'Instructions Added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $quiz = Quiz::findOrFail($id);  // Fetch quiz by ID

        $currentDate = Carbon::now()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        // Validate incoming data
        $request->validate([
            'subject_type' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'subject_name' => 'required|string|max:255',
            'faculty_name' => 'required|string|max:255',
            'date' => "required|date|after_or_equal:$currentDate",
            'start_time' => [
                'required',
                function ($attribute, $value, $fail) use ($currentDate, $currentTime, $request) {
                    if ($request->date === $currentDate && $value < $currentTime) {
                        $fail('Start time must be equal to or after the current time for today.');
                    }
                },
            ],
            'end_time' => [
                'required',
                function ($attribute, $value, $fail) use ($currentDate, $currentTime, $request) {
                    if ($request->date === $currentDate && $value < $currentTime) {
                        $fail('End time must be equal to or after the current time for today.');
                    }
                    if ($value <= $request->start_time) {
                        $fail('End time must be after the start time.');
                    }
                },
            ],
        ]);

        // Update the quiz
        $quiz->update($request->all());

        return redirect()->route('quizzes.index')->with('success', 'Quiz updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {


        // Fetch the quiz by ID
        $quiz = Quiz::findOrFail($id);
        // $quiz->attempts()->delete();
        // Optionally, delete related questions
        $quiz->questions()->delete(); // Delete related questions

        // Now, delete the quiz itself
        $quiz->delete();



        // Redirect with success message
        return redirect()->route('quizzes.index')->with('success', 'Quiz deleted successfully.');
    }


    public function startTest(Request $request)
    {
        $quizId = $request->input('quiz_id');
        $quiz = Quiz::findOrFail($quizId);

        // Update the quiz status to 1 (indicating test has started)
        $quiz->status = 1;
        $quiz->save();

        $user = auth()->user();
        if ($user->hasRole('Faculty') || $user->hasRole('SuperAdmin') || $user->hasRole('Admin')) {
            return redirect()->route('quizzes.index')->with('success', 'Test has started successfully.');
        }
    }
}
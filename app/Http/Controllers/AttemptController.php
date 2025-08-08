<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttemptDetails;
use App\Models\Quiz;
use App\Models\User;
use App\Models\AttemptQuizDetails;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AttemptController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:view attempts', only: ['index']),
            // new Middleware('permission:view attempts', only: ['elective']),
            // new Middleware('permission:create attempts', only: ['create']),
            // new Middleware('permission:edit attempts', only: ['edit']),
            // new Middleware('permission:destroy attempts', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user(); // Current logged-in student
        $department = $user->department;
        $semester = $user->semester;

        // Fetch quizzes attempted by the student
        $attemptedQuizzes = AttemptDetails::where('student_id', $user->id)
            ->pluck('quiz_id')
            ->toArray();

        // Fetch upcoming quizzes
        $currentDateTime = now();
        $upcomingQuizzes = Quiz::where('status', 1)
            ->where('department', $department)
            ->where('semester', $semester)
            ->where('date', '>=', $currentDateTime->toDateString()) // Upcoming quizzes based on date
            ->pluck('id')
            ->toArray();

        // Merge the attempted quizzes and upcoming quizzes
        $quizIds = array_unique(array_merge($attemptedQuizzes, $upcomingQuizzes));

        // Fetch the filtered quizzes
        $quizzes = Quiz::whereIn('id', $quizIds)
            ->get();

        return view('attempts.index', compact('quizzes'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $quiz = Quiz::findOrFail($id);
        $user = auth()->user();

        $existingAttempt = AttemptDetails::where('quiz_id', $id)
            ->where('student_id', $user->id)
            ->first();

        return view('attempts.create', compact('quiz', 'user', 'existingAttempt'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'roll_no' => 'required|string|max:50',
        ]);

        $existingAttempt = AttemptDetails::where('quiz_id', $request->quiz_id)
            ->where('student_id', auth()->id())
            ->first();

        if ($existingAttempt) {
            return redirect()->route('attempts.show', ['id' => $existingAttempt->id])
                ->with('alert', true)
                ->with('message', 'You have already attempted this quiz.')
                ->with('attempt_id', $existingAttempt->id);
        }

        $attempt = AttemptDetails::create([
            'student_id' => auth()->id(),
            'quiz_id' => $request->quiz_id,
            'roll_no' => $request->roll_no,
            'status' => 0,
        ]);

        return redirect()->route('attempts.show', ['id' => $attempt->id])
            ->with('success', 'Quiz attempt saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $attempt = AttemptDetails::with(['quiz', 'student'])->findOrFail($id);

        return view('attempts.show', [
            'attempt' => $attempt,
            'quiz' => $attempt->quiz,
            'studentDetails' => [
                'name' => $attempt->student->name,
                'roll_no' => $attempt->roll_no,
                'department' => $attempt->student->department,
                'semester' => $attempt->student->semester,
            ],
        ]);
    }

    public function elective(Request $request)
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
        return view('attempts.elective', compact('departments', 'subjects', 'faculties', 'roles'));
    }

    public function filterQuiz(Request $request)
    {
        $user = auth()->user(); // Current logged-in student
        $semester = $user->semester;
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
        // Validate incoming data
        $validated = $request->validate([
            'subject_type' => 'nullable|string',
            'department' => 'nullable|string',
            'semester' => 'nullable|integer',
            'subject_name' => 'nullable|string',
        ]);

        // Query to fetch quizzes
        $query = Quiz::query();

        // Apply filters only if they are provided
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $validated['subject_type']);
        }
        if ($request->filled('subject_name')) {
            $query->where('subject_name', $validated['subject_name']);
        }
        if ($request->filled('department')) {
            $query->where('department', $validated['department']);
        }
        if ($request->filled('semester')) {
            $query->where('semester', $validated['semester']);
        }

        // Get the quizzes with a status of 1 (active)
        $quizzes = $query->where('status', 1)
            ->whereRaw("CONCAT(date, ' ', start_time) > ?", [now()])
            ->get();

        // Check if any quiz is already submitted for the logged-in student
        $alertMessage = '';
        foreach ($quizzes as $quiz) {
            $existingAttempt = AttemptDetails::where('quiz_id', $quiz->id)
                ->where('student_id', auth()->id())
                ->first();

            if ($existingAttempt && $existingAttempt->status == 1) {
                // If the quiz is already submitted, set the alert message
                $alertMessage = 'Quiz already submitted.';
                break;
            }
        }

        if ($alertMessage) {
            return redirect()->route('attempts.index')->with('error', $alertMessage);
        }

        return view('attempts.elective', compact('quizzes', 'departments'));
    }




    // Add a new method to fetch quiz questions
    public function startTest(Request $request, $quizId)
    {
        $quiz = Quiz::find($quizId);
        $questions = Question::where('quiz_id', $quizId)->get();

        // Check if the user has already submitted this test
        $attempt = AttemptDetails::where('quiz_id', $quizId)
            ->where('student_id', auth()->id()) // Ensure the current user's attempt is checked
            ->where('status', 1) // Check for submitted status
            ->first();

        if ($attempt) {
            // Test already submitted, show SweetAlert and redirect
            return response()->json([
                'alert' => true,
                'message' => 'You have already submitted this quiz. Redirecting to attempts list.',
                'redirect' => route('attempts.index'),
            ]);
        }

        // Get answers from session or initialize empty array
        $previousAnswers = session()->get('answers', []);

        // If new answers are submitted, merge them with session
        if ($request->has('answers')) {
            $submittedAnswers = $request->input('answers');
            $previousAnswers = array_merge($previousAnswers, $submittedAnswers);
            session()->put('answers', $previousAnswers);
        }

        // Get the current question ID from the request
        $currentQuestionId = $request->input('question_id') ?? $questions->first()->id;

        return view('attempts.start_test', compact('quiz', 'questions', 'previousAnswers', 'currentQuestionId'));
    }


    public function submitTest(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $submittedAnswers = $request->input('answers', []); // Answers submitted by student
        $currentDateTime = now();
        $quizEndDateTime = \Carbon\Carbon::parse($quiz->date . ' ' . $quiz->end_time);

        // Fetch roll number from the authenticated user
        $rollNo = auth()->user()->roll_no;

        // Check if the attempt is already submitted
        $existingAttempt = AttemptDetails::where('quiz_id', $quizId)
            ->where('roll_no', $rollNo) // Use roll_no from users table
            ->where('status', 1) // Check if already submitted
            ->first();

        if ($existingAttempt) {
            return redirect()->route('attempts.index')
                ->with('error', 'You have already submitted this quiz.');
        }

        // Auto-submit if the quiz end time has passed
        if ($currentDateTime->gt($quizEndDateTime)) {
            $submittedAnswers = []; // Auto-submit without answers
        }

        // Create or fetch the attempt record
        $attempt = AttemptDetails::firstOrCreate(
            [
                'quiz_id' => $quizId,
                'roll_no' => $rollNo, // Use roll_no as unique identifier for student
            ],
            [
                'status' => 0, // Default to not submitted
            ]
        );

        // Save responses
        foreach ($submittedAnswers as $questionId => $selectedOption) {
            AttemptQuizDetails::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => $questionId],
                ['selected_option' => $selectedOption]
            );
        }

        // Mark attempt as submitted
        $attempt->update(['status' => 1]);

        return redirect()->route('attempts.results', ['quizId' => $quizId])
            ->with('success', 'Quiz submitted successfully.');
    }

    public function results(Request $request, $quizId)
    {
        // Fetch quiz with its questions
        $quiz = Quiz::with('questions')->findOrFail($quizId);

        // Get the student's attempt for this quiz
        $attempt = AttemptDetails::where('quiz_id', $quizId)
            ->where('student_id', auth()->id()) // Ensure correct user
            ->first();

        if (!$attempt) {
            return redirect()->route('attempts.index')->with('error', 'Test attempt not found.');
        }

        // Get all responses for this attempt
        $responses = AttemptQuizDetails::where('attempt_id', $attempt->id)->get();

        // Initialize variables
        $correctAnswersCount = 0;
        $attemptedQuestionsCount = $responses->count(); // Count of attempted questions
        $totalAttemptsAllowed = $quiz->attempt_no; // Use `attempt_no` for calculation

        // Only consider the first 'attempt_no' responses
        $filteredResponses = $responses->take($totalAttemptsAllowed);

        // Check each filtered response for correctness
        foreach ($filteredResponses as $response) {
            $question = $quiz->questions->firstWhere('id', $response->question_id);

            if ($question && strtolower($response->selected_option) === strtolower($question->correct_option)) {
                $correctAnswersCount++;
            }
        }

        // Calculate the score
        $score = $correctAnswersCount * ($quiz->weightage ?? 1); // Default weightage = 1

        // Pass data to the results view
        return view('attempts.results', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'totalQuestions' => $totalAttemptsAllowed, // Total questions to consider
            'attemptedQuestions' => $attemptedQuestionsCount, // Actual attempted questions
            'correctAnswersCount' => $correctAnswersCount,
            'score' => $score,
            'responses' => $filteredResponses, // Pass filtered responses to the view
        ]);
    }

    public function responses($attemptId)
    {
        $attempt = AttemptDetails::with(['quiz', 'quiz.questions'])->findOrFail($attemptId);

        // Get all responses for this attempt
        $responses = AttemptQuizDetails::where('attempt_id', $attempt->id)->get();

        // Initialize variables for score calculation
        $correctAnswersCount = 0;

        $questionsWithResponses = $attempt->quiz->questions->map(function ($question) use ($responses, &$correctAnswersCount) {
            $response = $responses->firstWhere('question_id', $question->id);

            $isCorrect = isset($response) && strtolower($response->selected_option) === strtolower($question->correct_option);

            if ($isCorrect) {
                $correctAnswersCount++;
            }

            return [
                'question_text' => $question->question_text,
                'options' => json_decode($question->options),
                'selected_option' => $response->selected_option ?? null,
                'correct_option' => $question->correct_option,
                'is_correct' => $isCorrect,
            ];
        });

        // Calculate score based on correct answers and quiz weightage
        $score = $correctAnswersCount * ($attempt->quiz->weightage ?? 1);

        return view('attempts.responses', [
            'attempt' => $attempt,
            'questionsWithResponses' => $questionsWithResponses,
            'score' => $score,
        ]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
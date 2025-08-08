<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Quiz;
// use Illuminate\Routing\Controllers\HasMiddleware;
// use Illuminate\Routing\Controllers\Middleware;

class QuestionController extends Controller //implements HasMiddleware
{
    // public static function middleware()
    // {
    //     return [
    //         new Middleware('permission:view questions', only: ['index']),
    //         new Middleware('permission:create questions', only: ['create']),
    //         new Middleware('permission:edit questions', only: ['edit']),
    //         new Middleware('permission:destroy questions', only: ['destroy']),
    //     ];
    // }

    /**
     * Display a listing of the resource for a specific quiz.
     */
    public function index($quizId)
    {
        $quiz = Quiz::findOrFail($quizId); // Ensure quiz exists
        $questions = Question::where('quiz_id', $quizId)->get(); // Fetch questions for the quiz
        return view('questions.index', compact('quiz', 'questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($quizId)
    {
        $quiz = Quiz::findOrFail($quizId); // Ensure quiz exists
        return view('questions.create', compact('quiz'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'questions' => 'required|array', // Multiple questions validation
            'questions.*.question_text' => 'required|string|max:255',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.correct_option' => 'required|integer|min:1',
        ]);

        // Loop through each question and save it
        foreach ($request->questions as $questionData) {
            $options = $questionData['options'];
            $correctOptionIndex = $questionData['correct_option'] - 1; // Adjust for 0-based index
            $correctOption = $options[$correctOptionIndex] ?? null;

            if (!$correctOption) {
                return response()->json(['error' => 'Invalid correct option provided.'], 400);
            }

            Question::create([
                'quiz_id' => $request->quiz_id,
                'question_text' => $questionData['question_text'],
                'options' => json_encode($options), // Convert options array to JSON
                'correct_option' => $correctOption, // Save the option name (value) as a string
            ]);
        }

        return response()->json(['message' => 'Questions saved successfully!'], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($quizId, $id)
    {
        $quiz = Quiz::findOrFail($quizId); // Ensure quiz exists
        $question = Question::findOrFail($id); // Find the question by ID

        // Decode the options JSON to be used in the view
        $question->options = json_decode($question->options, true);

        return view('questions.edit', compact('quiz', 'question'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $quizId, $id)
    {
        // Validate the request
        $request->validate([
            'question_text' => 'required|string|max:255',
            'options' => 'required|array|min:2',
            'correct_option' => 'required|integer|min:1',
        ]);

        // Find the question and update it
        $question = Question::findOrFail($id);

        $options = $request->input('options');
        $correctOptionIndex = $request->input('correct_option') - 1; // Adjust for 0-based index
        $correctOption = $options[$correctOptionIndex] ?? null;

        if (!$correctOption) {
            return back()->withErrors(['correct_option' => 'The selected correct option is invalid.']);
        }

        // Update the question
        $question->update([
            'question_text' => $request->question_text,
            'options' => json_encode($options), // Convert options array to JSON
            'correct_option' => $correctOption, // Save the option name (value) as a string
        ]);

        // Redirect to index with success message
        return redirect()->route('questions.index', $quizId)
            ->with('success', 'Question updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the question or fail
        $question = Question::findOrFail($id);

        // Get the quiz ID from the question
        $quizId = $question->quiz_id;

        // Delete the question
        $question->delete();

        // Redirect to the questions.index route with the correct parameter name
        return redirect()->route('questions.index', ['quiz' => $quizId])
            ->with('success', 'Question deleted successfully!');
    }



    public function submitQuestions($quizId)
    {
        // Get all questions for the quiz
        $questions = Question::where('quiz_id', $quizId)->get();

        // Mark all questions as submitted
        foreach ($questions as $question) {
            $question->is_submitted = 1; // Mark as submitted
            $question->save();
        }

        // Add a session flash message for finalized quiz
        session()->flash('finalized_quiz_id', $quizId);

        // Redirect to quizzes index
        return redirect()->route('quizzes.index')->with('success', 'All questions for this quiz have been submitted.');
    }
}
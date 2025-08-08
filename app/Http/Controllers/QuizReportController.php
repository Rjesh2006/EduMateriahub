<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizReport;
use App\Models\Subject;
use App\Models\User;
use App\Models\AttemptDetails;
use App\Models\Quiz;
use App\Models\AttemptQuizDetails;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;
use Dompdf\Dompdf;
// use App\Exports\QuizResultsExport;

class QuizReportController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:view reports', only: ['index']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
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

        return view('quiz_reports.index', compact('departments', 'subjects', 'faculties', 'roles'));
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

    public function fetchQuizzes(Request $request)
    {
        $validated = $request->validate([
            'subject_type' => 'required|string',
            'department' => 'required|string',
            'semester' => 'required|integer',
            'subject_name' => 'required|string',
            'faculty_name' => 'required|string',
        ]);

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

        // Fetch quizzes based on the filters provided
        $quizzes = Quiz::where('subject_type', $validated['subject_type'])
            ->where('department', $validated['department'])
            ->where('semester', $validated['semester'])
            ->where('subject_name', $validated['subject_name'])
            ->where('faculty_name', $validated['faculty_name'])
            ->where('status', 1) // Filter quizzes with status 1
            ->get();

        // Fetch attempt details with status = 1 (filter for quizzes that have been attempted)
        $attemptedQuizzes = AttemptDetails::where('status', 1)
            ->whereIn('quiz_id', $quizzes->pluck('id')) // Ensure the quizzes are filtered based on quiz_id
            ->get();

        // Return the view with filtered quizzes and attempted quizzes
        return view('quiz_reports.index', compact('quizzes', 'attemptedQuizzes', 'faculties', 'roles', 'departments'));
    }

    public function viewResults($quiz_id)
    {
        // Fetch all students who have attempted the quiz
        $attempts = AttemptDetails::where('quiz_id', $quiz_id)
            ->where('status', 1)
            ->get();

        // Fetch the quiz with related questions
        $quiz = Quiz::with('questions')->find($quiz_id);

        if (!$quiz) {
            return redirect()->back()->withErrors('Quiz not found.');
        }

        $studentsResults = [];

        foreach ($attempts as $attempt) {
            // Fetch student details
            $student = User::find($attempt->student_id);

            if (!$student) {
                continue; // Skip if student details are not found
            }

            // Get all responses for this attempt
            $responses = AttemptQuizDetails::where('attempt_id', $attempt->id)->get();

            // Initialize variables
            $correctAnswersCount = 0;
            $totalQuestions = $quiz->questions->count();

            // Check each question's response
            foreach ($quiz->questions as $question) {
                $response = $responses->firstWhere('question_id', $question->id);

                // If response exists and matches the correct option
                if ($response && strtolower($response->selected_option) === strtolower($question->correct_option)) {
                    $correctAnswersCount++;
                }
            }

            // Calculate the score
            $marks = $correctAnswersCount * ($quiz->weightage ?? 1);

            // Add student's result to the results array
            $studentsResults[] = [
                'name' => $student->name,
                'roll_no' => $attempt->roll_no,
                'semester' => $student->semester,
                'department' => $student->department,
                'marks' => $marks,
            ];
        }

        return view('quiz_reports.results', compact('studentsResults', 'quiz_id'));
    }

    // public function exportToExcel($quiz_id)
    // {
    //     $studentsResults = $this->fetchResults($quiz_id); // Helper function to get results

    //     $exportData = collect($studentsResults)->map(function ($result) {
    //         return [
    //             'Name' => $result['name'],
    //             'Roll No' => $result['roll_no'],
    //             'Semester' => $result['semester'],
    //             'Department' => $result['department'],
    //             'Marks' => $result['marks'],
    //         ];
    //     });

    //     return Excel::download(new \App\Exports\QuizResultsExport($exportData), 'quiz_results.xlsx');
    // }

    // public function exportToWord($quiz_id)
    // {
    //     $studentsResults = $this->fetchResults($quiz_id);
    //     $quiz = Quiz::find($quiz_id);

    //     $phpWord = new PhpWord();
    //     $section = $phpWord->addSection([
    //         'borderColor' => '2E75B6',
    //         'borderSize' => 12,
    //     ]);

    //     // Add Quiz Details
    //     $section->addText('Quiz Report', ['bold' => true, 'size' => 20, 'color' => '2E75B6'], ['alignment' => 'center']);
    //     $section->addText("Quiz Name: {$quiz->name}", ['bold' => true, 'size' => 14]);
    //     $section->addText("Subject: {$quiz->subject_name}", ['bold' => true, 'size' => 14]);
    //     $section->addText("Faculty: {$quiz->faculty_name}", ['bold' => true, 'size' => 14]);
    //     $section->addText("Department: {$quiz->department}", ['bold' => true, 'size' => 14]);
    //     $section->addText("Semester: {$quiz->semester}", ['bold' => true, 'size' => 14]);
    //     $section->addTextBreak(1);

    //     // Add table with colorful styles
    //     $tableStyle = [
    //         'borderSize' => 6,
    //         'borderColor' => '2E75B6',
    //         'cellMargin' => 80,
    //     ];
    //     $cellStyle = ['valign' => 'center'];
    //     $fontHeader = ['bold' => true, 'color' => 'FFFFFF', 'size' => 12];
    //     $fontBody = ['size' => 12];
    //     $phpWord->addTableStyle('Quiz Results Table', $tableStyle);
    //     $table = $section->addTable('Quiz Results Table');

    //     // Add Table Header
    //     $table->addRow();
    //     $table->addCell(1000, ['bgColor' => '2E75B6'])->addText('S.N.', $fontHeader, $cellStyle);
    //     $table->addCell(2000, ['bgColor' => '2E75B6'])->addText('Name', $fontHeader, $cellStyle);
    //     $table->addCell(2000, ['bgColor' => '2E75B6'])->addText('Roll No', $fontHeader, $cellStyle);
    //     $table->addCell(2000, ['bgColor' => '2E75B6'])->addText('Semester', $fontHeader, $cellStyle);
    //     $table->addCell(3000, ['bgColor' => '2E75B6'])->addText('Department', $fontHeader, $cellStyle);
    //     $table->addCell(2000, ['bgColor' => '2E75B6'])->addText('Marks', $fontHeader, $cellStyle);

    //     // Add Table Rows
    //     foreach ($studentsResults as $result) {
    //         $table->addRow();
    //         $table->addCell(1000)->addText($index + 1, $fontBody, $cellStyle);
    //         $table->addCell(2000)->addText($result['name'], $fontBody, $cellStyle);
    //         $table->addCell(2000)->addText($result['roll_no'], $fontBody, $cellStyle);
    //         $table->addCell(2000)->addText($result['semester'], $fontBody, $cellStyle);
    //         $table->addCell(3000)->addText($result['department'], $fontBody, $cellStyle);
    //         $table->addCell(2000)->addText($result['marks'], $fontBody, $cellStyle);
    //     }

    //     // Save the Word document
    //     $fileName = 'quiz_reports.docx';
    //     $filePath = storage_path("app/{$fileName}");
    //     $phpWord->save($filePath, 'Word2007');

    //     return response()->download($filePath)->deleteFileAfterSend(true);
    // }

    public function exportToPDF($quiz_id)
    {
        $studentsResults = $this->fetchResults($quiz_id);
        $quiz = Quiz::find($quiz_id);

        $dompdf = new Dompdf();

        // Create HTML with a full-page border
        $html = '
    <html>
        <head>
            <style>
                @page {
                    margin: 0;
                }
                body {
                    margin: 20px;
                    padding: 40px;
                    border: 2px solid #2E75B6;
                    box-sizing: border-box;
                    font-family: Arial, sans-serif;
                }
                h1 {
                    text-align: center;
                    color: #2E75B6;
                }
                p {
                    font-size: 16px;
                    color: #333;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 14px;
                    margin-top: 20px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #2E75B6;
                    color: #FFFFFF;
                }
            </style>
        </head>
        <body>
            <h1>Quiz Report</h1>
            <p><strong>Quiz Name:</strong> ' . $quiz->name . '</p>
            <p><strong>Subject:</strong> ' . $quiz->subject_name . '</p>
            <p><strong>Faculty:</strong> ' . $quiz->faculty_name . '</p>
            <p><strong>Department:</strong> ' . $quiz->department . '</p>
            <p><strong>Semester:</strong> ' . $quiz->semester . '</p>
            <hr>
            <table>
                <thead>
                    <tr>
                     <th>S.N.</th>
                        <th>Name</th>
                        <th>Roll No</th>
                        <th>Semester</th>
                        <th>Department</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($studentsResults as $index => $result) {
            $html .= '
                    <tr>
                    <td>' . ($index + 1) . '</td>
                        <td>' . $result['name'] . '</td>
                        <td>' . $result['roll_no'] . '</td>
                        <td>' . $result['semester'] . '</td>
                        <td>' . $result['department'] . '</td>
                        <td style="text-align: center;">' . $result['marks'] . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
    </html>';

        // Load HTML into Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF
        $dompdf->render();

        // Return the PDF file for download
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="quiz_reports.pdf"');
    }







    private function fetchResults($quiz_id)
    {
        $attempts = AttemptDetails::where('quiz_id', $quiz_id)
            ->where('status', 1)
            ->get();

        $quiz = Quiz::with('questions')->find($quiz_id);
        $studentsResults = [];

        foreach ($attempts as $attempt) {
            $student = User::find($attempt->student_id);

            if (!$student) {
                continue;
            }

            $responses = AttemptQuizDetails::where('attempt_id', $attempt->id)->get();
            $correctAnswersCount = 0;

            foreach ($quiz->questions as $question) {
                $response = $responses->firstWhere('question_id', $question->id);
                if ($response && strtolower($response->selected_option) === strtolower($question->correct_option)) {
                    $correctAnswersCount++;
                }
            }

            $marks = $correctAnswersCount * ($quiz->weightage ?? 1);

            $studentsResults[] = [
                'name' => $student->name,
                'roll_no' => $attempt->roll_no,
                'semester' => $student->semester,
                'department' => $student->department,
                'marks' => $marks,
            ];
        }

        return $studentsResults;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
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
        //
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
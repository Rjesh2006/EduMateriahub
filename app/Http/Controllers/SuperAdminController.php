<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Pyq;
use App\Models\StudyMaterial;
use App\Models\Roadmaps;
use Illuminate\Support\Facades\Hash;


class SuperAdminController extends Controller
{


    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        // Fetch data from the respective tables
        $totalUsers = User::count(); // Count all users
        $totalQuizzes = Quiz::count(); // Count all quizzes
        $totalPYQs = Pyq::count(); // Count all uploaded PYQs
        $totalStudyMaterials = StudyMaterial::count(); // Count all study materials
        $totalRoadmaps = Roadmaps::count(); // Count all study materials
        $totalStudents = User::role('student')->count();
        $totalFaculties = User::role('Faculty')->count();
        $totalAdmins = User::role('Admin')->count();
        $totalSuperadmins = User::role('Superadmin')->count();

        // Pass data to the view
        return view('superadmin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalQuizzes' => $totalQuizzes,
            'totalPYQs' => $totalPYQs,
            'totalStudyMaterials' => $totalStudyMaterials,
            'totalRoadmaps' => $totalRoadmaps,
            'totalStudents' => $totalStudents,
            'totalFaculties' => $totalFaculties,
            'totalAdmins' => $totalAdmins,
            'totalSuperadmins' => $totalSuperadmins,
        ]);
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
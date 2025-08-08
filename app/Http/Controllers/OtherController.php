<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class OtherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $other = Auth::user();
        return view('others.dashboard', compact('other'));
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
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'profilePic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Get the authenticated user
        $other = Auth::user();

        // Update user details
        $other->email = $request->email;
        $other->phone = $request->phone;

        // Profile picture update logic
        if ($request->hasFile('profilePic')) {
            $file = $request->file('profilePic');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile_pictures'), $filename);

            $other->profilePic = 'uploads/profile_pictures/' . $filename;
        }

        // Save other data
        $other->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
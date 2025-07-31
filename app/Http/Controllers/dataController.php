<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class dataController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('userprofile.form', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'office_location' => 'nullable|string|max:255',
            'office_lat' => 'required|numeric',
            'office_lng' => 'required|numeric',
            'home_location' => 'nullable|string|max:255',
            'home_lat' => 'required|numeric',
            'home_lng' => 'required|numeric',
            'office_timings' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'vehicle_number' => 'nullable|string|max:255',
        ]);

        Auth::user()->update($request->only([
            'office_location', 'office_lat', 'office_lng',
            'home_location', 'home_lat', 'home_lng',
            'office_timings', 'vehicle_type', 'vehicle_number',
        ]));

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}

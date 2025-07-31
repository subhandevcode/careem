<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserProfileController extends Controller
{
   public function getNearbyUsers(Request $request)
{
    $lat = $request->query('lat');
    $lng = $request->query('lng');
    $locationType = $request->query('locationType'); // 'home' or 'office'

    if (!$lat || !$lng || !$locationType) {
        return response()->json(['error' => 'Coordinates or location type missing'], 400);
    }

    $currentUserId = auth()->id();

    // Reference to the distance function
    $distanceCalc = function ($lat1, $lng1, $lat2, $lng2) {
        $earthRadius = 6371; // in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    };

    // Step 1: Get users with valid coordinates
    $users = DB::table('users')
        ->select('id', 'name', 'home_lat', 'home_lng', 'office_lat', 'office_lng')
        ->whereNotNull($locationType . '_lat')
        ->whereNotNull($locationType . '_lng')
        ->where('id', '!=', $currentUserId)
        ->get()
        ->filter(function ($user) use ($lat, $lng, $locationType, $distanceCalc) {
            $distance = $distanceCalc($lat, $lng, $user->{$locationType . '_lat'}, $user->{$locationType . '_lng'});
            return $distance <= 1;
        });

    // Step 2: Check if current user is subscribed
    $hasActiveSubscription = auth()->check() && auth()->user()->is_subscribed;

    // Step 3: Prepare API response
    $users = $users->map(function ($user) use ($hasActiveSubscription) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'has_active_subscription' => $hasActiveSubscription,
        ];
    });

    return response()->json($users->values())
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
        ->header('Pragma', 'no-cache');
}

     // Show the user's profile page
    // public function show()
    // {
    //     $user = auth()->user(); // Get the currently l   ogged-in user
    //     return view('userprofile.show', compact('user')); // Pass the user to the view
    // }
}
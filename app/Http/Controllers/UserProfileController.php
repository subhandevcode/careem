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

        $currentUserId = auth()->id(); // exclude current user

        // Query for home or office location matching based on locationType
        $users = DB::table('users')
            ->leftJoin('subscriptions', 'users.id', '=', 'subscriptions.user_id') // Join with subscriptions
            ->select('users.id', 'users.name', 'users.home_lat', 'users.home_lng', 'users.office_lat', 'users.office_lng', 'subscriptions.status')
            ->whereNotNull('users.' . $locationType . '_lat') // home_lat or office_lat
            ->whereNotNull('users.' . $locationType . '_lng') // home_lng or office_lng
            ->where('users.id', '!=', $currentUserId) // exclude current user
            ->where(function ($query) {
                // Ensure that the subscription is active or no subscription (for un-subscribed users)
                $query->where('subscriptions.status', 'active')
                      ->orWhereNull('subscriptions.status');
            })
            ->get()
            ->filter(function ($user) use ($lat, $lng, $locationType) {
                // Filter users within 1 km of the location
                $distance = $this->haversineGreatCircleDistance($lat, $lng, $user->{$locationType . '_lat'}, $user->{$locationType . '_lng'});
                return $distance <= 1; // 1 km radius for the location
            });

        // Add subscription status to the response
        $users = $users->map(function ($user) {
            $user->has_active_subscription = ($user->status === 'active');
            return $user;
        });

        return response()->json($users->values());
    }

    private function haversineGreatCircleDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

     // Show the user's profile page
    public function show()
    {
        $user = auth()->user(); // Get the currently logged-in user
        return view('userprofile.show', compact('user')); // Pass the user to the view
    }
}
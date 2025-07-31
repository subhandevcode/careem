<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class ChatController extends Controller
{
    public function chatWithUser($userId)
    {
        $user = auth()->user();  // Get the logged-in user
        
        // Check if the logged-in user has an active subscription
        if ($user->hasActiveSubscription()) {
            // Allow the user to chat
            return view('chat', ['userId' => $userId]);
        } else {
            // Redirect to subscription page
            return redirect()->route('subscribe.show')->with('error', 'You need an active subscription to chat.');
        }
    }
}

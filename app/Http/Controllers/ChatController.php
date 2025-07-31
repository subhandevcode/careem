<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function chatDashboard()
    {
        $nearbyUsers = User::where('id', '!=', auth()->id())->get(); // Optional: apply location filter
        return view('chat.dashboard', compact('nearbyUsers'));
    }

    public function openChat(User $user)
    {
        $messages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', auth()->id())
              ->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->where('receiver_id', auth()->id());
        })->orderBy('created_at')->get();

        $nearbyUsers = User::where('id', '!=', auth()->id())->get();
        return view('chat.dashboard', compact('messages', 'user', 'nearbyUsers'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return back();
    }
}

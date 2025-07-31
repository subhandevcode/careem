<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Message;

class ChatBox extends Component
{
    public $receiver;
    public $messageText = '';

    public function mount(User $receiver)
    {
        $this->receiver = $receiver;
    }

    public function sendMessage()
    {
        $this->validate([
            'messageText' => 'required|string|max:1000'
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->receiver->id,
            'message' => $this->messageText
        ]);

        $this->messageText = '';
    }

    public function getMessagesProperty()
    {
        return Message::where(function ($q) {
            $q->where('sender_id', auth()->id())
              ->where('receiver_id', $this->receiver->id);
        })->orWhere(function ($q) {
            $q->where('sender_id', $this->receiver->id)
              ->where('receiver_id', auth()->id());
        })->orderBy('created_at')->get();
    }

    public function render()
    {
        return view('livewire.chat-box', [
            'messages' => $this->messages
        ]);
    }
}

<div class="flex flex-col h-[80vh] bg-gray-100 border rounded-lg shadow">
    
    {{-- Messages --}}
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-2">
        @forelse($messages as $msg)
            <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs px-4 py-2 rounded-lg shadow 
                    {{ $msg->sender_id === auth()->id() ? 'bg-green-500 text-white' : 'bg-white text-gray-800 border' }}">
                    <p>{{ $msg->message }}</p>
                    <small class="block mt-1 text-xs opacity-70">
                        {{ $msg->created_at->format('h:i A') }}
                    </small>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 mt-4">No messages yet. Say hello 👋</p>
        @endforelse
    </div>

    {{-- Input --}}
    <form wire:submit.prevent="sendMessage" class="flex border-t p-3 bg-white">
        <input type="text" 
               wire:model.defer="messageText" 
               placeholder="Type a message..."
               class="flex-1 border rounded-full px-4 py-2 focus:outline-none focus:ring focus:border-blue-400" 
               autofocus>
        <button type="submit" 
                class="ml-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full">
            Send
        </button>
    </form>

    {{-- Auto scroll --}}
    <script>
        document.addEventListener('livewire:update', () => {
            let chatBox = document.getElementById('chat-messages');
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    </script>
</div>

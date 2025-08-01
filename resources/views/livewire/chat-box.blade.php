<div class="flex flex-col h-[80vh] bg-gray-50 border rounded-lg shadow-lg overflow-hidden">

    {{-- Header --}}
    <div class="bg-green-600 text-white p-3 flex items-center space-x-3 shadow">
        <div class="bg-white text-green-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">
            {{ strtoupper(substr($receiver->name, 0, 1)) }}
        </div>
        <div>
            <p class="font-semibold">{{ $receiver->name }}</p>
            <p class="text-sm text-green-100">Online</p>
        </div>
    </div>

    {{-- Messages Area (only this part will auto-refresh) --}}
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3" wire:poll.2s>
        @foreach($messages as $msg)
            @if($msg->sender_id === auth()->id())
                {{-- Sent --}}
                <div class="flex justify-end">
                    <div class="bg-green-500 text-white px-4 py-2 rounded-2xl rounded-br-none max-w-xs shadow">
                        {{ $msg->message }}
                        <div class="text-xs text-right opacity-80 mt-1">
                            {{ $msg->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>
            @else
                {{-- Received --}}
                <div class="flex justify-start">
                    <div class="bg-white text-gray-800 px-4 py-2 rounded-2xl rounded-bl-none max-w-xs shadow border">
                        {{ $msg->message }}
                        <div class="text-xs text-right text-gray-500 mt-1">
                            {{ $msg->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Input Box --}}
    <form wire:submit.prevent="sendMessage" class="flex items-center p-3 bg-white border-t space-x-2">
        <input type="text" 
               wire:model.defer="messageText"
               placeholder="Type a message..."
               class="flex-1 border rounded-full px-4 py-2 focus:outline-none focus:ring focus:border-green-500">
        <button type="submit" 
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full shadow">
            Send
        </button>
    </form>

</div>

{{-- Auto Scroll Script --}}
@push('scripts')
<script>
    document.addEventListener('livewire:update', () => {
        let chatBox = document.getElementById('chat-messages');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
@endpush
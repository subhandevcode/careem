<x-app-layout>
    <style>
        /* Scoped styles: conv- prefix so nothing else breaks */
        .conv-wrapper {
            max-width: 700px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .conv-header {
            background: #128C7E;
            color: white;
            padding: 12px 16px;
            font-size: 18px;
            font-weight: bold;
        }
        .conv-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            text-decoration: none;
            color: inherit;
            transition: background 0.2s ease;
        }
        .conv-item:hover {
            background: #f9f9f9;
        }
        .conv-avatar {
            background: #25D366;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .conv-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .conv-name {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 3px;
        }
        .conv-last {
            font-size: 13px;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .conv-empty {
            padding: 20px;
            text-align: center;
            color: #6b7280;
        }
    </style>

    <div class="conv-wrapper">
        <div class="conv-header">Recent Conversations</div>

        @forelse($chats as $partnerId => $messages)
            @php
                $partner = $messages->first()->sender_id == auth()->id()
                    ? $messages->first()->receiver
                    : $messages->first()->sender;
            @endphp

            <a href="{{ url('/chat/user/'.$partner->id) }}" class="conv-item">
                <div class="conv-avatar">{{ strtoupper(substr($partner->name, 0, 1)) }}</div>
                <div class="conv-info">
                    <div class="conv-name">{{ $partner->name }}</div>
                    <div class="conv-last">{{ $messages->last()->message }}</div>
                </div>
            </a>
        @empty
            <div class="conv-empty">No messages yet.</div>
        @endforelse
    </div>
</x-app-layout>
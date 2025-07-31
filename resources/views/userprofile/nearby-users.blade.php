<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Nearby Users</h1>

    <ul>
        @foreach($users as $user)
            <li>
                <strong>{{ $user->name }}</strong><br>

                @if($user->has_active_subscription)
                    <!-- Chat button visible only if the user has an active subscription -->
                    <form action="{{ route('chat.withUser', $user->id) }}" method="GET">
                        @csrf
                        <button type="submit">Chat</button>
                    </form>
                @else
                    <!-- Subscription button visible if the user doesn't have an active subscription -->
                    <form action="{{ route('subscribe.show', $user->id) }}" method="GET">
                        @csrf
                        <button type="submit">Subscribe</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
</body>
</html>
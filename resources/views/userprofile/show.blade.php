<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Email: {{ $user->email }}</p>
    <p>Home Location: {{ $user->home_location }}</p>
    <p>Office Location: {{ $user->office_location }}</p>

    <!-- Display subscription status -->
    <h3>Subscription Status:</h3>
    @if($subscription)
        <p>Plan: {{ $subscription->plan }}</p>
        <p>Status: {{ $subscription->status }}</p>
        <p>Expires on: {{ $subscription->ends_at->format('d-m-Y') }}</p>
    @else
        <p>No active subscription found.</p>
    @endif
</body>
</html>
<x-app-layout>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/style.css">
    <title>Home Page</title>
</head>
<body>
  <div class="container">
            <h1>Welcome to the Dashboard</h1>
            <a class="btn" href="{{ route('user.save.edit') }}">Save User Profile</a>
            <a class="btn" href="{{ route('userprofile.edit') }}">Search User</a>
        </div>

</body>
</html>

</x-app-layout>

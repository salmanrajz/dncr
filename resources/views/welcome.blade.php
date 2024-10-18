<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPA Login</title>
</head>
<body>
    <div id="app">
        <router-view></router-view> <!-- Vue Router will render components here -->
    </div>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- <script src="{{ mix('js/app.js') }}"></script> --}}
</body>
</html>

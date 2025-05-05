<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Aplikasi' }}</title>

    {{-- Import Tailwind & Custom CSS --}}
    @vite('resources/css/app.css')

    {{-- Icon (Optional) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    {{-- Alpine.js --}}
</head>
<body class="bg-white m-0 p-0 w-full min-h-screen overflow-x-hidden">


    {{-- Kontainer fullscreen konten --}}
    <div class="w-full h-full">
        @yield('content')
    </div>

</body>
</html>

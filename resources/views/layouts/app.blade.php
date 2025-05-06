<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/tracer.ico" type="image/x-icon">
    <title>{{ $title ?? 'Tracer Study Polibatam' }}</title>


    {{-- Import Tailwind & Custom CSS --}}
    @vite('resources/css/app.css')

    {{-- Icon (Optional) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    {{-- Font Google Outfit --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/hamburger.css">

    {{-- Alpine.js --}}
</head>
<body class="bg-white m-0 p-0 w-full min-h-screen overflow-x-hidden">


    {{-- Kontainer fullscreen konten --}}
    <div class="w-full h-full">
        @yield('content')
    </div>

</body>
</html>

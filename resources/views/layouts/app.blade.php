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
</head>
<body class="bg-white min-h-screen flex items-center justify-center px-4 py-10">

    {{-- Tempat isi konten halaman --}}
    @yield('content')

</body>
</html>

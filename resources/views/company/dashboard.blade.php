<!DOCTYPE html>
<html>
<head>
    <title>Dashboard perusahaan</title>
</head>
<body>
    <h1>Selamat datang, perusahaan!</h1>

    <p>Halo halo company . Ini adalah dashboard company.</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>

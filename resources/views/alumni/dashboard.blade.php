<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Alumni</title>
</head>
<body>
    <h1>Selamat datang, Alumni!</h1>

    <p>Halo . Ini adalah dashboard alumni.</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>

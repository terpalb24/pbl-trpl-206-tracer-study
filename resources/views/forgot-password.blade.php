@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 py-10">

    <div class="flex flex-col-reverse lg:flex-row items-center w-full max-w-6xl mx-auto rounded-lg shadow-lg overflow-hidden bg-white">
        <!-- Left Side - Login Form -->
        <div class="w-full lg:w-1/2 bg-theme-secondary p-8 sm:p-10 md:p-27">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-black mb-3">Lupa Kata Sandi?</h2>
            <p class="text-sm sm:text-base text-center text-black mb-6">
                Silahkan masukkan akun email yang terhubung, agar kami dapat mengirim email untuk mereset kata sandi anda ^_^
            </p>
            @if(session('error'))
            <div class="mb-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded p-3">
                {{ session('error') }}
            </div>
            @endif
            <form method="POST" action="/forgot-password" class="space-y-5">
                @csrf
                <!-- Email Reset Password -->
                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Masukkan Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-user"></i></span>
                        <input id="email" name="email" type="email" required autofocus class="input-field" required placeholder="Masukkan Email">

                        <!-- Alert untuk berhasil kirim email atau ngga -->
    @if ($errors->any())
<div class="alert alert-danger col-md-6 mt-3" style="max-width: 400px">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

@if (session('status'))
<div class="alert alert-danger col-md-6 mt-3" style="max-width: 400px">
  {{ session('status') }}
</div>
@endif
                    </div>
                </div>

                <!-- Buttons -->
                <div class="space-y-3">
                    <button type="submit" class="btn-primary">Kirim</button>
                    <a href="/" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>

        <!-- Right Side - Image -->
        <div class="w-full lg:w-1/2 flex justify-center items-center p-6">
            <img src="{{ asset('assets/images/amico.png') }}" alt="Login" class="max-w-xs md:max-w-md lg:max-w-lg">
        </div>
    </div>

</div>

<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById('toggleIcon-' + fieldId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 py-10">
    <div class="w-full max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center mb-4">Reset Password Admin</h2>
        <p class="text-center text-gray-600 mb-6">Masukkan password baru untuk akun admin.</p>
        @if(session('error'))
        <div class="mb-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded p-3">
            {{ session('error') }}
        </div>
        @endif
        @if ($errors->any())
        <div class="mb-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded p-3">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="{{ route('password.admin.reset.update') }}" class="space-y-5">
            @csrf
            <div>
                <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Password Baru</label>
                <input id="password" name="password" type="password" required class="input-field outline outline-1 outline-gray-300 focus:outline-2 focus:outline-blue-500" placeholder="Password baru">
            </div>
            <div>
                <label for="password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="input-field outline outline-1 outline-gray-300 focus:outline-2 focus:outline-blue-500" placeholder="Konfirmasi password">
            </div>
            <button type="submit" class="btn-primary w-full">Reset Password</button>
        </form>
    </div>
</div>
@endsection

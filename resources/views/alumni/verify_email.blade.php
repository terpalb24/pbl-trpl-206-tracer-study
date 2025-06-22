@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 py-10">
    <div class="flex flex-col-reverse md:flex-row items-center w-full max-w-4xl mx-auto rounded-lg shadow-lg overflow-hidden bg-white">
        <!-- Left Side - Form -->
        <div class="w-full md:w-1/2 p-8 sm:p-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-black mb-3">Verifikasi Email Alumni</h2>
            <p class="text-sm sm:text-base text-center text-gray-600 mb-6">
                Masukkan email Anda untuk menerima link verifikasi.
            </p>
            @if(session('error'))
                <div class="mb-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded p-3">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('status'))
                <div class="mb-4 text-sm text-green-600 bg-green-100 border border-green-300 rounded p-3">
                    {{ session('status') }}
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
            <form method="POST" action="{{ route('alumni.verify.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-envelope"></i></span>
                        <input id="email" name="email" type="email" required autofocus class="input-field pl-10" placeholder="Masukkan Email">
                    </div>
                    @error('email')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <button
                        type="submit"
                        class="bg-blue-900 text-white py-2 rounded-md hover:bg-blue-800 transition">
                        Kirim Link Verifikasi
                    </button>
                    <button type="button" id="btn-cancel" class="text-center text-sm text-black font-semibold hover:underline">
                        Continue
                    </button>
                </div>
            </form>
        </div>
        <!-- Right Side - Illustration -->
        <div class="w-full md:w-1/2 flex justify-center items-center p-6 bg-gray-50">
            <img src="{{ asset('assets/images/email.png') }}" alt="Verifikasi Email" class="max-w-xs md:max-w-md lg:max-w-lg w-full h-auto">
        </div>
    </div>
</div>
{{-- SweetAlert CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isFirstLogin = @json(session('alumni_is_first_login', 1));
        if (isFirstLogin == 0) {
            Swal.fire({
                icon: 'success',
                title: 'Akun sudah diverifikasi',
                text: 'Akun Anda sudah diverifikasi. Anda akan diarahkan ke dashboard.',
                showConfirmButton: false,
                timer: 2500
            }).then(() => {
                window.location.href = "{{ route('dashboard.alumni') }}";
            });
        }
        document.getElementById('btn-cancel').onclick = function() {
            if (isFirstLogin == 1) {
                // Hapus session dan redirect ke login
                fetch("{{ route('logout') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    }
                }).then(() => {
                    window.location.href = "{{ route('login') }}";
                });
            } else {
                // Sudah diverifikasi, SweetAlert sudah handle redirect di atas
            }
        }
    });
</script>
@endsection

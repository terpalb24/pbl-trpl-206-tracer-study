@extends('layouts.app')
@section('content')
<!-- Navbar -->
<header class="fixed top-0 z-50 left-0 right-0 w-full bg-[#0c2a5b] text-white shadow-lg">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center">
            <img src="assets\images/Group 3.png" alt="Logo" class="h-8 sm:h-10" />
        </div>

        <!-- Desktop Menu -->
        <nav class="hidden lg:flex items-center gap-4 xl:gap-6">
            <a href="#" class="hover:text-[#F2692A] transition-colors duration-200">Beranda</a>
            <a href="{{route('about')}}" class="hover:text-[#F2692A] transition-colors duration-200">Tentang</a>
            <a href="#" class="hover:text-[#F2692A] transition-colors duration-200">Kontak</a>
            <a href="#" class="hover:text-[#F2692A] transition-colors duration-200">Statistik</a>
            <div class="relative group">
                <button class="hover:text-[#F2692A] transition-colors duration-200">Laporan</button>
                <ul class="absolute hidden group-hover:block bg-white text-black mt-2 rounded-md shadow-lg w-64 z-50">
                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 text-sm">Laporan Tracer Study Polibatam 2022</a></li>
                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 text-sm">Laporan Tracer Study Polibatam 2023</a></li>
                </ul>
            </div>
            <a href="{{route('login')}}" class="flex items-center gap-2 hover:text-[#F2692A] transition-colors duration-200">
                <i class="fa-solid fa-user"></i> Login
            </a>
        </nav>

        <!-- Hamburger (Mobile) -->
        <div class="lg:hidden">
            <button id="hamburgerBtn" class="p-2">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="lg:hidden hidden bg-[#0c2a5b] border-t border-blue-800">
        <div class="px-4 py-2 space-y-1">
            <a href="#" class="block py-2 text-sm hover:text-[#F2692A]">Beranda</a>
            <a href="{{route('about')}}" class="block py-2 text-sm hover:text-[#F2692A]">Tentang</a>
            <a href="#" class="block py-2 text-sm hover:text-[#F2692A]">Kontak</a>
            <a href="#" class="block py-2 text-sm hover:text-[#F2692A]">Statistik</a>
            <a href="#" class="block py-2 text-sm hover:text-[#F2692A]">Laporan</a>
            <a href="{{route('login')}}" class="block py-2 text-sm hover:text-[#F2692A]">Login</a>
        </div>
    </div>
</header>

<!-- Include Google Translate Widget Component -->
<x-translate-widget 
    position="bottom-left" 
    :languages="['en', 'id']" 
    theme="light" 
/>
<!-- Google Translate Widget CSS -->
<link rel="stylesheet" href="{{ asset('css/translate-widget.css') }}">

<!-- Hero Section -->
<section class="pt-20 min-h-screen flex items-center">
    <div class="max-w-7xl mx-auto flex flex-col-reverse lg:flex-row items-center justify-between px-4 py-8 sm:px-6 lg:px-8 gap-8 lg:gap-12">
        <div class="w-full lg:w-1/2 space-y-4 sm:space-y-6 text-center lg:text-left">
            <p class="text-sm sm:text-base lg:text-lg">Selalu terhubung dengan</p>
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold leading-tight">
                Tracer Study <br><span class="text-[#0c2a5b]">Polibatam</span>
            </h1>
            <p class="text-sm sm:text-base lg:text-lg">Halo, alumni!</p>
            <p class="text-gray-800 text-sm sm:text-base">
                Mari sukseskan pelaksanaan <span class="font-bold text-[#0c2a5b]">tracer study</span> Politeknik Negeri Batam.
            </p>
            <a href="{{route('login')}}" class="inline-block bg-[#0c2a5b] text-white font-medium px-6 sm:px-8 lg:px-12 py-2 sm:py-3 rounded-xl hover:bg-[#123c80] transition-colors duration-200">
                Login
            </a>
        </div>
        <div class="w-full lg:w-1/2 flex justify-center">
            <img src="assets\images/cuate.svg" alt="Ilustrasi Lulusan" class="w-full max-w-xs sm:max-w-sm lg:max-w-md" />
        </div>
    </div>
</section>

<!-- Tentang -->
<section class="bg-[#fef4f2] py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
            <div class="w-full lg:w-1/2 flex justify-center order-2 lg:order-1">
                <img src="assets\images/rafiki.svg" alt="Tentang Tracer" class="max-w-xs sm:max-w-sm lg:max-w-md w-full">
            </div>
            <div class="w-full lg:w-1/2 space-y-4 sm:space-y-6 text-center lg:text-left order-1 lg:order-2">
                <h5 class="text-xs sm:text-sm font-medium uppercase tracking-wide">Tentang</h5>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-bold leading-tight">
                    Apa itu <span class="text-[#0c2a5b]">Tracer Study</span>?
                </h2>
                <p class="text-sm sm:text-base leading-relaxed text-gray-700 text-justify">
                    Tracer Study merupakan salah satu metode yang digunakan oleh beberapa perguruan tinggi di Indonesia untuk memberikan umpan balik dari alumni. 
                    Umpan balik yang diperoleh digunakan untuk melakukan evaluasi dalam rangka pengembangan kualitas sistem pendidikan.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Manfaat -->
<section class="bg-[#fdfcfe] py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
            <div class="w-full lg:w-1/2 space-y-4 sm:space-y-6 lg:space-y-10 text-center lg:text-left">
                <h5 class="text-xs sm:text-sm font-medium uppercase tracking-wide">Manfaat Tracer Study</h5>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-bold leading-tight">
                    Apa Manfaat <span class="text-[#0c2a5b]">Tracer Study</span>?
                </h2>
                <p class="text-sm sm:text-base leading-relaxed text-gray-700 text-justify">
                    Hasil dari Tracer Study ini akan memberikan manfaat secara langsung bagi Politeknik Negeri Batam karena selain menjadi monitoring dan penunjang akreditasi, tracer study dapat berfungsi sebagai feedback untuk program strategis perguruan tinggi seperti penyesuaian dan evaluasi kurikulum dan pengelolaan PT. PT juga akan dapat mengidentifikasi kebutuhan/harapan masyarakat dan dunia kerja terhadap lulusan, hingga dapat mengetahui waktu tunggu, jenis perusahaan, status pekerjaan, jabatan serta pendapatan para alumni.
                </p>
            </div>
            <div class="w-full lg:w-1/2 flex justify-center">
                <img src="assets\images/bro.svg" alt="Manfaat Tracer" class="max-w-xs sm:max-w-sm lg:max-w-md w-full">
            </div>
        </div>
    </div>
</section>

<!-- Metode Tracer Study -->
<section class="bg-blue-100 py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
            <div class="w-full lg:w-1/2 flex justify-center order-2 lg:order-1">
                <img src="assets\images/cuate1.svg" alt="Ilustrasi Metode" class="w-full max-w-xs sm:max-w-sm lg:max-w-lg" />
            </div>
            <div class="w-full lg:w-1/2 space-y-4 sm:space-y-6 text-center lg:text-left order-1 lg:order-2">
                <h3 class="text-xs sm:text-sm text-black font-bold uppercase tracking-wide">Metode Tracer Study</h3>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold leading-tight">
                    Bagaimana Metode <span class="text-blue-700">Tracer Study</span>?
                </h2>
                <p class="text-sm sm:text-base text-justify text-gray-700 leading-relaxed">
                    Metode pengumpulan data yang digunakan dalam aplikasi ini adalah metode survey dengan menggunakan kuesioner melalui aplikasi web Tracer Study Polibatam. Pertanyaan dalam kuesioner yang diberikan terdiri dari pertanyaan terbuka dan tertutup. Kuesioner ini sudah disusun dan disebarkan baik melalui email, penyebaran langsung, dan secara online.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Tujuan Tracer Study -->
<section class="bg-white py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
            <div class="w-full lg:w-1/2 space-y-4 sm:space-y-6 text-center lg:text-left">
                <h3 class="text-xs sm:text-sm text-black font-bold uppercase tracking-wide">Tujuan Tracer Study</h3>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold leading-tight">
                    Apa Tujuan Diadakan <span class="text-blue-700">Tracer Study</span>?
                </h2>
                <div class="space-y-3 sm:space-y-4">
                    <div class="bg-blue-900 text-white p-3 sm:p-4 rounded-lg text-sm sm:text-base">
                        <strong>Hasil:</strong> Menilai lulusan pendidikan yang dihasilkan oleh Politeknik Negeri Batam.
                    </div>
                    <div class="bg-blue-900 text-white p-3 sm:p-4 rounded-lg text-sm sm:text-base">
                        <strong>Kontribusi:</strong> Mengetahui kontribusi Polibatam terhadap kompetensi yang ada di dunia kerja.
                    </div>
                    <div class="bg-blue-900 text-white p-3 sm:p-4 rounded-lg text-sm sm:text-base">
                        <strong>Monitoring:</strong> Memantau relevansi antara lulusan dengan kebutuhan industri.
                    </div>
                    <div class="bg-blue-900 text-white p-3 sm:p-4 rounded-lg text-sm sm:text-base">
                        <strong>Evaluasi:</strong> Memberikan evaluasi terhadap kualitas lulusan bagi Polibatam sebagai institusi pendidikan.
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-1/2 flex justify-center">
                <img src="assets\images/pana.svg" alt="Ilustrasi Tujuan" class="w-full max-w-xs sm:max-w-sm lg:max-w-md" />
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-[#152A6B] text-white relative">
    <div class="absolute left-0 w-full overflow-hidden leading-0">
        <svg class="relative block w-full h-12 sm:h-16 lg:h-20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 1200 120">
            <path d="M0,0 C300,100 900,0 1200,100 L1200,0 L0,0 Z" fill="#152A6B"></path>
        </svg>
    </div>
    <div class="pt-16 sm:pt-20 lg:pt-24 px-4 sm:px-6 lg:px-8 pb-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6 sm:mb-8">
                <h2 class="text-white font-semibold text-lg sm:text-xl lg:text-2xl text-center lg:text-left">
                    Mari sukseskan pelaksanaan Tracer Study Politeknik Negeri Batam
                </h2>
            </div>
            <hr class="border-gray-400 mb-6 sm:mb-8" />
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 text-sm">
                <div class="text-center sm:text-left">
                    <img src="assets\images/Group 3.png" alt="Logo Tracer Study" class="mb-3 h-8 sm:h-10 mx-auto sm:mx-0" />   
                    <div class="flex justify-center sm:justify-start space-x-2 mt-2">
                        <a href="#" class="hover:text-[#F2692A] transition-colors duration-200">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                <div class="text-center sm:text-left">
                    <h3 class="font-bold mb-2 sm:mb-3">Kontak Kami</h3>
                    <p class="mb-2">Jl. Ahmad Yani Batam Kota, Kota Batam, Kepulauan Riau, Indonesia</p>
                    <p class="mb-1">Admin Pusat Karir Polibatam</p>
                    <p class="mb-1">(+62) 812-6755-3364</p>
                    <p>cdc@polibatam.ac.id</p>
                </div>
                <div class="text-center sm:text-left">
                    <h3 class="font-bold mb-2 sm:mb-3">Tautan Penting</h3>
                    <a href="https://www.polibatam.ac.id/" class="block hover:text-[#F2692A] transition-colors duration-200">Polibatam</a>
                </div>
                <div class="text-center sm:text-left">
                    <h3 class="font-bold mb-2 sm:mb-3">Dokumentasi</h3>
                    <p>Anda dapat mengakses dokumentasi terbaru mengenai Karir Polibatam 
                        <a href="https://linktr.ee/karirpolibatam" class="hover:text-[#F2692A] transition-colors duration-200 underline">di sini.</a>
                    </p>
                </div>
            </div>
            <div class="py-12 sm:py-16 lg:py-20 text-center text-xs sm:text-sm text-gray-300 border-t border-gray-600 mt-8">
                ©2025 Politeknik Negeri Batam. All Rights Reserved.
            </div>
        </div>
    </div>
</footer>

<script>
    // Toggle menu mobile
    document.getElementById('hamburgerBtn').addEventListener('click', () => {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
        const menu = document.getElementById('mobileMenu');
        const hamburger = document.getElementById('hamburgerBtn');
        
        if (!menu.contains(e.target) && !hamburger.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    // Close mobile menu when window is resized to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.getElementById('mobileMenu').classList.add('hidden');
        }
    });
</script>

@endsection

@extends('layouts.app')
@section('content')
<!-- Navbar -->
<header class="fixed z-999 left-0 right-0 items-center bg-[#0c2a5b] text-white p-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center">
            <img src="assets\images/Group 3.png" alt="Logo" class="h-10" />
        </div>

        <!-- Desktop Menu -->
        <nav class="hidden md:flex items-center gap-6">
            <a href="#" class="hover:text-[#F2692A]">Beranda</a>
            <a href="{{route('about')}}" class="hover:text-[#F2692A]">Tentang</a>
            <a href="#" class="hover:text-[#F2692A]">Kontak</a>
            <a href="#" class="hover:text-[#F2692A]">Statistik</a>
            <div class="relative group">
                <button class="hover:text-[#F2692A]">Laporan</button>
                <ul class="absolute hidden group-hover:block bg-white text-black mt-2 rounded-md shadow-md w-70">
                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Laporan Tracer Study Polibatam 2022</a></li>
                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Laporan Tracer Study Polibatam 2023</a></li>
                </ul>
            </div>
            <a href="{{route('login')}}" class="flex items-center gap-2 hover:text-[#F2692A]"><i class="fa-solid fa-user"></i> Login</a>
        </nav>

        <!-- Hamburger (Mobile) -->
        <div class="md:hidden">
            <button id="hamburgerBtn"><i class="fa-solid fa-bars text-2xl"></i></button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden hidden flex-col px-4 pb-4 space-y-2 bg-[#0c2a5b]">
        <a href="#" class="block">Beranda</a>
        <a href="#" class="block">Tentang</a>
        <a href="#" class="block">Kontak</a>
        <a href="#" class="block">Statistik</a>
        <a href="#" class="block">Laporan</a>
        <a href="#" class="block">Login</a>
    </div>
</header>

<!-- Include Google Translate Widget Component -->
<x-translate-widget 
    position="top-right" 
    :languages="['en', 'id']" 
    theme="light" 
/>

<!-- Hero Section -->
<section class="max-w-7xl mx-auto flex flex-col-reverse md:flex-row items-center justify-between p-6 md:p-12 min-h-screen">
    <div class="md:w-1/2 space-y-6">
        <p class="text-lg">Selalu terhubung dengan</p>
        <h1 class="text-4xl md:text-7xl font-bold leading-tight">
            Tracer Study <br><span class="text-[#0c2a5b">Polibatam</span>
        </h1>
        <p class="text-md">Halo, alumni!</p>
        <p class="text-gray-800">Mari sukseskan pelaksanaan <span class="font-bold text-[#0c2a5b]">tracer study</span> Politeknik Negeri Batam.</p>
        <a href="#" class="inline-block bg-[#0c2a5b] text-white font-medium px-13 py-2 rounded-xl hover:bg-[#123c80] transition">Login</a>
    </div>
    <div class="md:w-1/2">
        <img src="assets\images/cuate.svg" alt="Ilustrasi Lulusan" class="w-full max-w-md mx-auto" />
    </div>
</section>

<!-- Tentang -->
<section class="bg-[#fef4f2] py-16 px-6 min-h-screen flex items-center">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-10">
        <div class="md:w-1/2 flex justify-center">
            <img src="assets\images/rafiki.svg" alt="Tentang Tracer" class="max-w-sm w-full">
        </div>
        <div class="md:w-1/2 space-y-4">
            <h5 class="text-sm font-medium">Tentang</h5>
            <h2 class="text-2xl md:text-4xl font-bold">Apa itu <span class="text-[#0c2a5b]">Tracer Study</span>?</h2>
            <p class="text-sm leading-relaxed text-gray-700 text-justify">
                Tracer Study merupakan salah satu metode yang digunakan oleh beberapa perguruan tinggi di Indonesia untuk memberikan umpan balik dari alumni. 
                Umpan balik yang diperoleh digunakan untuk melakukan evaluasi dalam rangka pengembangan kualitas sistem pendidikan.
            </p>
        </div>
    </div>
</section>

<!-- Manfaat -->
<section class="bg-[#fdfcfe] py-16 px-6 min-h-screen flex items-center">
    <div class="max-w-7xl mx-auto flex flex-col-reverse md:flex-row items-center gap-10">
        <div class="md:w-1/2 space-y-10">
            <h5 class="text-sm font-medium">Manfaat Tracer Study</h5>
            <h2 class="text-5xl md:text-4xl font-bold">Apa Manfaat <span class="text-[#0c2a5b]">Tracer Study</span>?</h2>
            <p class="text-sm leading-relaxed text-gray-700 text-justify">
                Hasil dari Tracer Study ini akan memberikan manfaat secara langsung bagi Politeknik Negeri Batam karena selain menjadi monitoring dan penunjang akreditasi, tracer study dapat berfungsi sebagai feedback untuk program strategis perguruan tinggi seperti penyesuaian dan evaluasi kurikulum dan pengelolaan PT. PT juga akan dapat mengidentifikasi kebutuhan/harapan masyarakat dan dunia kerja terhadap lulusan, hingga dapat mengetahui waktu tunggu, jenis perusahaan, status pekerjaan, jabatan serta pendapatan para alumni.
            </p>
        </div>
        <div class="md:w-1/2 flex justify-center">
            <img src="assets\images/bro.svg" alt="Manfaat Tracer" class="max-w-sm w-full">
        </div>
    </div>
</section>

<!-- Metode Tracer Study -->
<section class="bg-blue-100 px-6 py-12 min-h-screen flex items-center">
    <div class="max-w-7xl mx-auto md:flex md:items-center md:justify-between">
        <img src="assets\images/cuate1.svg" alt="Ilustrasi Metode" class="w-full md:w-[500px] mb-6 md:mb-0" />
        <div class="md:w-1/2 md:pl-10">
            <h3 class="text-sm text-black mb-1 font-bold">Metode Tracer Study</h3>
            <h2 class="text-4xl font-bold mb-4">Bagaimana Metode <span class="text-blue-700">Tracer Study</span>?</h2>
            <p class="text-justify text-gray-700 leading-relaxed">
                Metode pengumpulan data yang digunakan dalam aplikasi ini adalah metode survey dengan menggunakan kuesioner melalui aplikasi web Tracer Study Polibatam. Pertanyaan dalam kuesioner yang diberikan terdiri dari pertanyaan terbuka dan tertutup. Kuesioner ini sudah disusun dan disebarkan baik melalui email, penyebaran langsung, dan secara online.
            </p>
        </div>
    </div>
</section>

<!-- Tujuan Tracer Study -->
<section class="bg-white px-6 py-12 min-h-screen flex items-center">
    <div class="max-w-7xl mx-auto md:flex md:items-center md:justify-between">
        <div class="md:w-1/2 md:pr-10">
            <h3 class="text-sm text-black mb-1 font-bold">Tujuan Tracer Study</h3>
            <h2 class="text-4xl font-bold mb-4">Apa Tujuan Diadakan <span class="text-blue-700">Tracer Study</span>?</h2>
            <div class="space-y-4">
                <div class="bg-blue-900 text-white p-4 rounded-lg">Hasil: Menilai lulusan pendidikan yang dihasilkan oleh Politeknik Negeri Batam.</div>
                <div class="bg-blue-900 text-white p-4 rounded-lg">Kontribusi: Mengetahui kontribusi Polibatam terhadap kompetensi yang ada di dunia kerja.</div>
                <div class="bg-blue-900 text-white p-4 rounded-lg">Monitoring: Memantau relevansi antara lulusan dengan kebutuhan industri.</div>
                <div class="bg-blue-900 text-white p-4 rounded-lg">Evaluasi: Memberikan evaluasi terhadap kualitas lulusan bagi Polibatam sebagai institusi pendidikan.</div>
            </div>
        </div>
        <img src="assets\images/pana.svg" alt="Ilustrasi Tujuan" class="w-full md:w-1/2 mt-6 md:mt-0" />
    </div>
</section>

<!-- Footer -->
<footer class="bg-[#152A6B] text-white relative">
    <div class="absolute left-0 w-full overflow-hidden leading-0">
        <svg class="relative block w-full h-[80px]" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 1200 120">
            <path d="M0,0 C300,100 900,0 1200,100 L1200,0 L0,0 Z" fill="#152A6B"></path>
        </svg>
    </div>
    <div class="pt-20 px-6 md:px-16 pb-8">
        <div class="mb-6">
            <h2 class="text-white font-semibold text-2xl">
                Mari sukseskan pelaksanaan Tracer Study Politeknik Negeri Batam
            </h2>
        </div>
        <hr class="border-gray-400 mb-8" />
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-sm">
            <div>
                <img src="assets\images/Group 3.png" alt="Logo Tracer Study" class="mb-3 h-10" />   
                <div class="flex space-x-2 mt-2">
                    <a href="#" class="hover:text-[#F2692A]">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="font-bold mb-2">Kontak Kami</h3>
                <p>Jl. Ahmad Yani Batam Kota, Kota Batam, Kepulauan Riau, Indonesia</p>
                <p class="mt-2">Admin Pusat Karir Polibatam</p>
                <p>(+62) 812-6755-3364</p>
                <p>cdc@polibatam.ac.id</p>
            </div>
            <div>
                <h3 class="font-bold mb-2">Tautan Penting</h3>
                <a href="https://www.polibatam.ac.id/" class="block hover:text-[#F2692A]">Polibatam</a>
            </div>
            <div>
                <h3 class="font-bold mb-2">Dokumentasi</h3>
                <p>Anda dapat mengakses dokumentasi terbaru mengenai Karir Polibatam <a href="https://linktr.ee/karirpolibatam" class="hover:text-[#F2692A]">di sini.</a></p>
            </div>
        </div>
        <div class="py-20 text-center mt-12 text-sm text-gray-300">
            ©2025 Politeknik Negeri Batam. All Rights Reserved.
        </div>
    </div>
</footer>

<script>
    // Toggle menu mobile
    document.getElementById('hamburgerBtn').addEventListener('click', () => {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    });
</script>
<!-- Google Translate Widget CSS -->
    <link rel="stylesheet" href="{{ asset('css/translate-widget.css') }}">
@endsection

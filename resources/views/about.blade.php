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
            <a href="{{route('landing')}}" class="hover:text-[#F2692A] transition-colors duration-200">Beranda</a>
            <a href="#" class="hover:text-[#F2692A] transition-colors duration-200">Tentang</a>
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
            <a href="{{route('landing')}}" class="block py-2 text-sm hover:text-[#F2692A]">Beranda</a>
            <a href="#" class="block py-2 text-sm hover:text-[#F2692A]">Tentang</a>
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

<!-- Seksi Penjelasan Tracer Study -->
<section class="bg-[#fdf0ef] py-12 sm:py-16 lg:py-20 px-4 sm:px-6 lg:px-8 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-4xl bg-white p-6 sm:p-8 lg:p-10 rounded-lg shadow-lg relative mt-16 sm:mt-20">
        <!-- Garis latar belakang -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;utf8,<svg width=&quot;20&quot; height=&quot;20&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><rect width=&quot;100%&quot; height=&quot;100%&quot; fill=&quot;%23fddede&quot;/><path d=&quot;M10 0h1v20h-1z&quot; fill=&quot;%23fbcaca&quot;/></svg>')] opacity-20 z-0 rounded-lg"></div>

        <div class="relative z-10">
            <!-- Logo -->
            <div class="flex justify-center mb-4 sm:mb-6">
                <img src="assets/images/Group 3.png" alt="Logo Tracer Study" class="h-12 sm:h-16" />
            </div>

            <!-- Judul -->
            <h2 class="text-center text-2xl sm:text-3xl lg:text-4xl font-bold mb-6 sm:mb-8 leading-tight">
                Tracer Study <span class="text-[#F2692A]">Polibatam</span>
            </h2>

            <!-- Paragraf -->
            <div class="space-y-4 sm:space-y-6 text-justify text-sm sm:text-base text-gray-800 leading-relaxed">
                <p>
                    Pendidikan tinggi di Indonesia merupakan tahap pendidikan terakhir, selain mendidik juga mempersiapkan seseorang untuk menjadi pelaku profesional dengan keahlian tertentu yang dibutuhkan oleh dunia kerja. Pendidikan tinggi saat ini dituntut untuk dapat merespon perubahan dan kebutuhan masyarakat dan pasar tenaga kerja. Politeknik Negeri Batam merupakan institusi pendidikan tinggi yang terus berkembang dan mulai menyiapkan lulusannya menuju ke pasar kerja yang kompetitif. Keberhasilan lulusan dari perguruan tinggi memasuki dunia kerja merupakan salah satu indikator outcome pembelajaran dan kebermanfaatan perguruan tinggi bagi masyarakat.
                </p>
                <p>
                    Salah satu tahapan kegiatan yang dilakukan untuk mengetahui kompetensi dan kebermanfaatan lulusan dengan kebutuhan pengguna tenaga lulusan adalah tracer study (Tracer Study). Tracer study ditujukan untuk mengetahui sejauh mana lulusan dapat diterima dan bersaing di tengah masyarakat (khususnya pasar kerja). Tracer Study dilakukan dengan cara penyebaran kuesioner kepada alumni dengan tujuan mendapatkan informasi yang dibutuhkan. Selain itu juga digunakan untuk mendapatkan umpan balik (feedback) terhadap kesesuaian antara kompetensi yang diperoleh alumni dengan kebutuhan kerja.
                </p>
                <p>
                    Hasil tracer study juga digunakan untuk menetapkan kebijakan karir dalam mengejar prosesnya sesuai yang tercantum pada visi Politeknik Negeri Batam dan profil lulusan yang diharapkan. Selain itu, hasil tracer study digunakan sebagai informasi pendukung dalam akreditasi dan pelaporan kelembagaan.
                </p>
                <p>
                    Jumlah responden (Alumni) yang berpartisipasi dalam survey tracer study ini masih belum maksimal, dikarenakan minimnya kesadaran alumni untuk berpartisipasi dengan kampusnya. Namun, tracer study tetap harus disebar secara maksimal untuk memperoleh informasi sebagai tanggapan awal, observasi awal, yang kemudian bisa jadi masukan rekomendasi.
                </p>
                <p>
                    Akhir kata kami ucapkan terima kasih atas bantuan dan partisipasi semua pihak sehingga survey tracer study Politeknik Negeri Batam ini dapat terlaksana.
                </p>
                <p class="font-bold text-[#0c2a5b]">
                    Unit Pengembangan Karir dan Penguatan Karakter
                </p>
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

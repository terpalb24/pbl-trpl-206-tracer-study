@extends('layouts.app')
@section('content')
  <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

<div id="top" class="scroll-smooth">
<!-- Navbar -->
<nav class="fixed top-0 left-0 w-full xl:h-[83px] lg:h-[83px] md:h-[83px] h-[70px]  z-50 text-white bg-[#152A6B] -mt-1" data-aos="fade-down"
     data-aos-duration="1000">
    <div class="max-w-7xl mx-auto h-full flex items-center justify-between px-4">
        <a href="#top">
            <img src="assets/images/Group 3.png" alt="Logo Tracer Study" class="xl:h-12 lg:h-12 md:h-12 h-10 w-auto object-contain" />
        </a>
        <!-- Desktop Menu -->
        <ul id="nav-menu" class="hidden md:flex items-center gap-8 h-full">
            <li class="hover:text-[#F2692A]">
                <a href="{{ route('landing') }}">Beranda</a>
            </li>
            <li class="hover:text-[#F2692A]">
                <a href="#top">Tentang</a>
            </li>
            <li class="hover:text-[#F2692A]">
                <a href="#kontak">Kontak</a>
            </li>
            <li class="hover:text-[#F2692A]">
                <a href="https://linktr.ee/karirpolibatam">Laporan</a>
            </li>
        </ul>
        <div class="flex items-center gap-4">
            @if(Auth::check())
                @php
                    $role = Auth::user()->role;
                    $dashboard = $role == 1 ? route('dashboard.admin') : ($role == 2 ? route('dashboard.alumni') : ($role == 3 ? route('dashboard.company') : route('login')));
                @endphp
                <a href="{{ $dashboard }}" class="hover:text-[#F2692A] flex items-center">
                    <i class="fa-solid fa-gauge mr-2"></i><span>Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="hover:text-[#F2692A] flex items-center">
                    <i class="fa-solid fa-user mr-2"></i><span>Login</span>
                </a>
            @endif
            <!-- Hamburger: hidden on md and up -->
            <button id="menu-button" class="hamburger hamburger--collapse block md:hidden focus:outline-none" aria-label="Toggle menu">
                <div class="hamburger-box">
                    <div class="hamburger-inner"></div>
                </div>
            </button>
        </div>
    </div>
    <!-- Mobile Menu -->
    <ul id="mobile-menu" class="md:hidden fixed top-[60px] left-0 w-full bg-[#152A6B] text-center py-4 space-y-2 shadow transition-all duration-300 z-40 hidden">
        <li class="hover:text-[#F2692A]">
            <a href="{{ route('landing') }}">Beranda</a>
        </li>
        <li class="hover:text-[#F2692A]">
            <a href="#top">Tentang</a>
        </li>
        <li class="hover:text-[#F2692A]">
            <a href="#kontak">Kontak</a>
        </li>
        <li class="hover:text-[#F2692A]">
            <a href="https://linktr.ee/karirpolibatam">Laporan</a>
        </li>
    </ul>
    <style>
        /* Hamburger lines white */
        .hamburger .hamburger-inner,
        .hamburger .hamburger-inner::before,
        .hamburger .hamburger-inner::after {
            background-color: #fff !important;
        }
        /* Hide hamburger on desktop */
        @media (min-width: 768px) {
            #menu-button {
                display: none !important;
            }
        }
    </style>
</nav>
<!-- JS Hamburger Mobile-->
<script>
    const menuButton = document.getElementById('menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    menuButton.addEventListener('click', () => {
        menuButton.classList.toggle('is-active');
        mobileMenu.classList.toggle('hidden');
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
        if (
            !mobileMenu.contains(e.target) &&
            !menuButton.contains(e.target) &&
            !mobileMenu.classList.contains('hidden')
        ) {
            mobileMenu.classList.add('hidden');
            menuButton.classList.remove('is-active');
        }
    });

    // Hide mobile menu on resize to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            mobileMenu.classList.add('hidden');
            menuButton.classList.remove('is-active');
        }
    });
</script>

<!-- Include Google Translate Widget Component -->
<x-translate-widget 
    position="bottom-left" 
    :languages="['en', 'id']" 
    theme="light" 
/>
<!-- Google Translate Widget CSS -->
<link rel="stylesheet" href="{{ asset('css/translate-widget.css') }}">

<div class="bg-white text-black flex flex-col items-center text-center px-4 md:px-12 lg:px-32 lg:py-30 md:py-30 py-25"
     data-aos="zoom-out"
     data-aos-duration="2000">
  <!-- Logo -->
<header class="space-y-4 flex items-center gap-4 md:flex-row flex-row md:text-left text-left md:items-center md:gap-6">
    <img src="assets\images/tracer.ico" alt="Logo Tracer Study" class="md:w-30 w-20 mx-auto md:mx-0" />
    <div class="space-y-2">
      <h1 class="xl:text-6xl lg:text-6xl md:text-6xl sm:text-3xl text-3xl font-extrabold">Tracer Study</h1>
      <h2 class="xl:text-6xl lg:text-6xl md:text-6xl sm:text-3xl text-3xl font-extrabold">Polibatam</h2>
    </div>
  </header>

  <!-- Konten -->
<div class="bg-white text-black px-4 md:px-5 lg:px-5" 
     data-aos="fade-up"
     data-aos-duration="1000">
  <main class="space-y-6 text-justify leading-relaxed text-sm md:text-base max-w-4xl mx-auto md:py-20 py-10">
    <p>
      Pendidikan tinggi di Indonesia merupakan tahap pendidikan terakhir, selain mendidik juga mempersiapkan seseorang untuk menjadi pelaku profesional dengan keahlian tertentu yang dibutuhkan oleh dunia kerja. Pendidikan tinggi saat ini dituntut untuk dapat memenuhi kebutuhan dan harapan masyarakat dan pasar tenaga kerja. Politeknik Negeri Batam menyadari kebutuhan tenaga kerja pasca pandemi perlahan mulai mengalir dan persaingan antar tenaga kerja juga semakin kompetitif. Keberhasilan lulusan perguruan tinggi dalam memenuhi dunia kerja merupakan salah satu indikator outcome pembelajaran dan kebermanfaatan perguruan tinggi bagi masyarakat.
    </p>

    <p>
      Salah satu tahapan kegiatan yang dilakukan untuk mengetahui kompetensi dan keterserapan lulusan dengan kebutuhan penggunanya adalah studi pelacakan jejak (Tracer Study). Tracer study bertujuan untuk memperoleh data alumni setelah lulus dari perguruan tinggi. Beberapa perguruan tinggi di Indonesia untuk memperoleh umpan balik dari alumni. Umpan balik yang diperoleh digunakan untuk melakukan evaluasi dalam rangka pengembangan kualitas sistem Pendidikan. Selain itu, dapat digunakan untuk memperoleh data usaha dan industri agar terciptanya kesesuaian antara kompetensi yang diperoleh alumni dengan yang dibutuhkan oleh dunia kerja.
    </p>

    <p>
      Hasil tracer study juga digunakan untuk menetapkan kebijakan lanjut dalam menjamin prosesnya sesuai yang tercantum pada visi Politeknik Negeri Batam untuk menjadi perguruan tinggi vokasi yang unggul, turut mendukung program pemerintah untuk menciptakan lulusan yang siap dan membentuk manusia siap sebagai pembelajar seumur hidup, yang bermartabat, berwawasan luas, berdaya saing tinggi, peduli lingkungan dan kepekaan kebangsaan.
    </p>

    <p>
      Jumlah responden (Alumni) yang berpartisipasi dalam survey tracer study ini masih belum maksimal, dikarenakan minimnya kesadaran alumni untuk mengisi pemetaan pengembangan kampusnya. Namun, tim survey studi boleh bekerja secara maksimal untuk mengingatkan alumni. Sebagai langkah awal, sinergi antar unit terkait harus dilakukan dengan baik.
    </p>

    <p>
      Akhir kata kami ucapkan terima kasih atas bantuan dan partisipasi semua pihak sehingga survey tracer study Politeknik Negeri Batam ini dapat terlaksana dengan baik.
    </p>
  </main>

  <!-- Footer -->
  <footer class="text-justify text-sm font-semibold text-black">
    <p><span class="font-bold">Unit Pengembangan Karir dan Penguatan Karakter</span></p>
  </footer>
</div>
</div>


<!-- Footer -->
 <!-- Wave Atas Footer -->
<section data-aos="fade-up" data-aos-duration="1000">
<div class="w-full overflow-hidden -mb-1 -mt-10">
  <svg class="w-full rotate-180" viewBox="0 0 1440 320">
    <path fill="#152A6B" fill-opacity="1"
      d="M0,96L48,122.7C96,149,192,203,288,208C384,213,480,171,576,144C672,117,768,107,864,101.3C960,96,1056,96,1152,106.7C1248,117,1344,139,1392,149.3L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
    </path>
  </svg>
</div>
<footer id="kontak" class="bg-[#152A6B] text-white relative -mb-1">
    <div class="px-4 sm:px-6 lg:px-8">
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
                        <a href="https://www.instagram.com/cdcpolibatam" class="hover:text-[#F2692A] transition-colors duration-200">
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
                ©2025 <a href="https://terpalb24.github.io/projects" class="text-white hover:text-gray-400 transition-colors duration-200">PBL-TRPL206</a>. Politeknik Negeri Batam. All Rights Reserved.
            </div>
        </div>
    </div>
</footer>
</section>
</div>

<!-- AOS -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>

<!-- Smooth Scroll -->
  <script>
  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener("click", function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute("href"));
        if (target) {
          target.scrollIntoView({
            behavior: "smooth"
          });
        }
      });
    });
  });
</script>
@endsection

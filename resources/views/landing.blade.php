@extends('layouts.app')
@section('content')
<!-- AOS -->
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

<div id="top" class="scroll-smooth">
<!-- Navbar -->
<nav id="top" class="fixed top-0 left-0 w-full xl:h-[83px] lg:h-[83px] md:h-[83px] h-[70px]  z-999 text-white bg-[#152A6B] -mt-1" data-aos="fade-down"
     data-aos-duration="1000">
    <div class="max-w-7xl mx-auto h-full flex items-center justify-between px-4">
        <a href="#top">
            <img src="assets/images/Group 3.png" alt="Logo Tracer Study" class="xl:h-12 lg:h-12 md:h-12 h-10 w-auto object-contain" />
        </a>
        <!-- Desktop Menu -->
        <ul id="nav-menu" class="hidden md:flex items-center gap-8 h-full">
            <li class="hover:text-[#F2692A]">
                <a href="#top">Beranda</a>
            </li>
            <li class="hover:text-[#F2692A]">
                <a href="{{ route('about') }}">Tentang</a>
            </li>
            <li class="hover:text-[#F2692A]">
                <a href="#kontak">Kontak</a>
            </li>
            <li class="hover:text-[#F2692A]">
                <a href="https://linktr.ee/karirpolibatam">Laporan</a>
            </li>
        </ul>
        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}" class="hover:text-[#F2692A] flex items-center">
                <i class="fa-solid fa-user mr-2"></i><span>Login</span>
            </a>
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
            <a href="#top">Beranda</a>
        </li>
        <li class="hover:text-[#F2692A]">
            <a href="{{ route('about') }}">Tentang</a>
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
<!-- Navbar -->
 
<!-- Hero Welcome-->
<section class="min-h-screen flex flex-col md:flex-row items-center justify-between px-4 sm:px-10 md:px-20 lg:px-40 xl:py-35 lg:py-35 md:py-35 sm:py-35 py-5 bg-white">
    <!-- Kiri: Teks -->
    <div class="w-full md:w-1/2 text-center md:text-left space-y-4 order-2 md:order-1 py-20">
        <p class="xl:text-[20px] lg:text-[20px] md:text-[20px]" 
    data-aos="fade-down"
     data-aos-duration="3000">Selalu terhubung dengan</p>
        <h1 class="text-4xl md:text-7xl font-extrabold leading-tight" 
    data-aos="fade-right"
     data-aos-offset="200"
     data-aos-duration="1000">
            Tracer Study <br>
            <span class="text-[#152A6B]">Polibatam</span>
        </h1>
        <p class="text-base md:text-lg" 
    data-aos="fade-right"
     data-aos-offset="200"
     data-aos-duration="1000">Halo, alumni!</p>
        <p class="text-sm md:text-lg max-w-md" 
    data-aos="fade-right"
     data-aos-offset="200"
     data-aos-duration="1000">
            Mari sukseskan pelaksanaan <span class="font-semibold text-[#152A6B]">tracer study</span><br />
            Politeknik Negeri Batam.
        </p>
        <a href="{{ route('login') }}" class="inline-block bg-[#152A6B] text-white px-15 py-2 rounded-[18px] shadow-md font-medium mt-4 text-[18px]" 
    data-aos="fade-up"
     data-aos-duration="3000">
            Login
        </a>
    </div>

    <!-- Kanan: Gambar ilustrasi -->
    <div class="w-full md:w-1/2 flex justify-center order-1 md:order-2 mb-6 md:mb-0" 
    data-aos="fade-left"
     data-aos-anchor="#example-anchor"
     data-aos-offset="500"
     data-aos-duration="1000">
        <img src="assets/images/cuate.svg" alt="Ilustrasi Wisuda" class="max-w-full h-[350px] md:h-[550px]" />
    </div>
</section>
<style>
    @media (max-width: 767px) {
        /* Pastikan gambar ilustrasi di bawah teks pada mobile */
        section > div:nth-child(2) {
            order: 2 !important;
        }
        section > div:nth-child(1) {
            order: 1 !important;
        }
    }
</style>
<!-- Hero Welcome-->

<!-- Hero Tentang -->
<section class="bg-gradient-to-t from-[#FFD5C1] to-white py-10 md:py-13">
    <div class="max-w-[1350px] mx-auto px-4 sm:px-6 md:px-8 flex flex-col md:flex-row items-center gap-8 md:gap-40">
        
        <!-- Teks kanan (di mobile: urutan 1) -->
        <div class="w-full md:w-1/2 text-center md:text-left order-1 md:order-2">
            <h4 class="text-[18px] md:text-[20px] font-semibold text-gray-700 mb-2" data-aos="fade-down" data-aos-duration="2000">Tentang</h4>
            <h2 class="text-3xl sm:text-5xl md:text-5xl font-extrabold text-black leading-snug mb-4" data-aos="fade-left" data-aos-duration="1000">
                Apa itu <span class="text-[#152A6B]">Tracer Study</span>?
            </h2>
            <div class="max-w-xl mx-auto md:mx-0 w-120" data-aos="fade-up" data-aos-duration="2000">
                <p class="text-black text-sm sm:text-base leading-relaxed text-justify">
                    Tracer study merupakan salah satu metode yang digunakan oleh beberapa perguruan tinggi di Indonesia untuk
                    memperoleh umpan balik dari alumni. Umpan balik yang diperoleh digunakan untuk melakukan evaluasi dalam
                    rangka pengembangan kualitas sistem Pendidikan.
                </p>
            </div>
            <style>
            @media (max-width: 768px) {
                .w-120 {
                    width: 100% !important;
                    max-width: 100% !important;
                    padding-left: 0.5rem;
                    padding-right: 0.5rem;
                }
            }
            </style>
        </div>

        <!-- Ilustrasi kiri (di mobile: urutan 2) -->
        <div class="w-full md:w-1/2 mb-6 md:mb-0 flex justify-center order-2 md:order-1" data-aos="fade-right" data-aos-duration="2000">
            <img src="assets/images/rafiki.svg" alt="Ilustrasi Tracer Study" class="w-4/5 sm:w-3/4 md:w-full h-auto object-contain max-h-[300px] md:max-h-[600px]">
        </div>
    </div>
</section>
<!-- Hero Tentang -->

<!-- Hero Manfaat -->
<section class="bg-white xl:py-30 lg:py-30 md:py-30 py-15">
    <div class="max-w-7xl mx-auto px-4 md:px-8 flex flex-col md:flex-row items-center gap-12">
        
        <!-- Kiri: Teks -->
        <div class="w-full md:w-1/2 text-center md:text-left">
            <h4 class="text-[18px] md:text-[20px] font-semibold text-gray-700 mb-2" data-aos="fade-down" data-aos-duration="2000">Manfaat Tracer Study</h4>
            <h2 class="text-3xl md:text-5xl font-extrabold text-black leading-snug mb-4" data-aos="fade-right" data-aos-duration="1000">
                Apa manfaat <span class="text-[#152A6B]">Tracer Study</span>?
            </h2>
            <div class="max-w-xl mx-auto md:mx-0">
                <p class="text-black text-justify text-base leading-relaxed max-w-[620px] w-full md:w-[500px] mx-auto md:mx-0" data-aos="fade-up" data-aos-duration="2000">
                    Hasil dari Tracer Study ini akan memberikan manfaat secara langsung bagi Politeknik Negeri Batam karena selain menjadi monitoring dan penunjang akreditasi, tracer study dapat berfungsi sebagai feedback untuk program studi dan perguruan tinggi dalam mengevaluasi dan memperbaiki kurikulum dan pengelolaan PT, agar lulusan dapat mengakomodasi kebutuhan/tuntutan masyarakat dan pengelola PT. Perguruan tinggi juga dapat mengetahui waktu tunggu, jenis perusahaan, status pekerjaan, jabatan serta pendapatan para-alumni.
                </p>
            </div>
        </div>

        <!-- Kanan: Ilustrasi -->
        <div class="w-full md:w-1/2 flex justify-center md:justify-end mt-8 md:mt-0">
            <img src="assets/images/bro.svg" alt="Ilustrasi Manfaat Tracer Study" class="w-full max-w-xs sm:max-w-sm md:max-w-md h-auto object-contain" data-aos="fade-left" data-aos-duration="2000">
        </div>

    </div>
    <style>
        @media (max-width: 768px) {
            /* Pastikan gambar ilustrasi di bawah teks pada mobile */
            section > div > div:nth-child(2) {
                order: 2 !important;
                margin-top: 2rem !important;
            }
            section > div > div:nth-child(1) {
                order: 1 !important;
            }
            /* Responsive paragraph width */
            .max-w-\[620px\] {
                max-width: 100% !important;
            }
            .w-\[500px\] {
                width: 100% !important;
            }
        }
    </style>
</section>
<!-- Hero Manfaat -->

<!-- Hero Metode -->
 <section class="bg-gradient-to-t from-[#B1EEFF] to-white py-10 md:py-23">
    <div class="max-w-[1350px] mx-auto px-4 sm:px-6 md:px-8 flex flex-col md:flex-row items-center gap-8 md:gap-40">
        
        <!-- Teks kanan (di mobile: urutan 1) -->
        <div class="w-full md:w-1/2 text-center md:text-left order-1 md:order-2">
            <h4 class="text-[18px] md:text-[20px] font-semibold text-gray-700 mb-2" data-aos="fade-down" data-aos-duration="2000">Metode Tracer Study</h4>
            <h2 class="text-3xl sm:text-5xl md:text-5xl font-extrabold text-black leading-snug mb-4" data-aos="fade-left" data-aos-duration="1000">
                Bagaimana metode <span class="text-[#152A6B]">Tracer Study</span>?
            </h2>
            <div class="max-w-xl mx-auto md:mx-0 w-120" data-aos="fade-up" data-aos-duration="2000">
                <p class="text-black text-sm sm:text-base leading-relaxed text-justify">
                    Metode pengumpulan data yang digunakan dalam aplikasi ini adalah metode survey dengan menggunakan  kuesioner melalui aplikasi web Tracer Study Polibatam. Pertanyaan dalam kuesioner yang disebarkan  terdiri dari pertanyaan terbuka dan tertutup. Kuesioner ini sudah disusun dan disebarkan baik melalui email,  penyebaran langsung dan secara online.
                </p>
            </div>
            <style>
            @media (max-width: 768px) {
                .w-120 {
                    width: 100% !important;
                    max-width: 100% !important;
                    padding-left: 0.5rem;
                    padding-right: 0.5rem;
                }
            }
            </style>
        </div>

        <!-- Ilustrasi kiri (di mobile: urutan 2) -->
        <div class="w-full md:w-1/2 mb-6 md:mb-0 flex justify-center order-2 md:order-1" data-aos="fade-right" data-aos-duration="2000">
            <img src="assets/images/cuate1.svg" alt="Ilustrasi Tracer Study" class="w-4/5 sm:w-3/4 md:w-[500px] h-auto object-contain max-h-[300px] md:max-h-[600px]">
        </div>
    </div>
</section>
<!-- Hero Metode -->


<!-- Hero Tujuan-->
 <section class="bg-white py-8 md:py-20">
  <div class="max-w-7xl mx-auto px-4 md:px-8 flex flex-col md:flex-row items-center gap-12">
    
    <!-- Kiri: Judul + Ilustrasi -->
    <div class="w-full md:w-1/2 text-center md:text-left md:py-10">
      <h4 class="text-[18px] md:text-[20px] font-semibold text-gray-700 mb-2" data-aos="fade-down" data-aos-duration="2000">Tujuan Tracer Study</h4>
      <h2 class="text-3xl sm:text-5xl md:text-5xl font-extrabold text-black leading-snug mb-8" data-aos="fade-right" data-aos-duration="1000">
        Apa Tujuan Diadakan <span class="text-[#152A6B]">Tracer Study</span>?
      </h2>
      <img src="assets\images/pana.svg" alt="Ilustrasi Tujuan" class="w-full h-auto object-contain" data-aos="fade-up" data-aos-duration="2000">
    </div>

    <!-- Kanan: Zigzag Card -->
    <div class="w-full md:w-1/2 flex flex-col gap-6 relative">
      <!-- Card 1 -->
      <div class="bg-[#152A6B] text-white p-6 rounded-2xl shadow self-start w-full md:w-[85%]" data-aos="flip-down" data-aos-duration="1000">
        <h3 class="text-lg font-bold mb-1">Hasil</h3>
        <p class="text-sm leading-relaxed">
          Mengetahui outcome pendidikan yang dihasilkan oleh Politeknik Negeri Batam
        </p>
      </div>

      <!-- Card 2 -->
      <div class="bg-[#152A6B] text-white p-6 rounded-2xl shadow self-end w-full md:w-[85%]" data-aos="flip-down" data-aos-duration="1500">
        <h3 class="text-lg font-bold mb-1">Kontribusi</h3>
        <p class="text-sm leading-relaxed">
          Mengetahui kontribusi Politeknik Negeri Batam terhadap kompetensi yang ada di dunia kerja
        </p>
      </div>

      <!-- Card 3 -->
      <div class="bg-[#152A6B] text-white p-6 rounded-2xl shadow self-start w-full md:w-[85%]" data-aos="flip-down" data-aos-duration="2000">
        <h3 class="text-lg font-bold mb-1">Monitoring</h3>
        <p class="text-sm leading-relaxed">
          Monitoring kemampuan adaptasi lulusan Politeknik Negeri Batam ketika memasuki dunia kerja
        </p>
      </div>

      <!-- Card 4 -->
      <div class="bg-[#152A6B] text-white p-6 rounded-2xl shadow self-end w-full md:w-[85%]" data-aos="flip-down" data-aos-duration="2500">
        <h3 class="text-lg font-bold mb-1">Evaluasi</h3>
        <p class="text-sm leading-relaxed">
          Sebagai bahan evaluasi bagi Politeknik Negeri Batam untuk meningkatkan kualitas kedepannya
        </p>
      </div>
    </div>
  </div>
</section>
<!-- Hero Tujuan -->

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
                ©2025 PBL-TRPL206. Politeknik Negeri Batam. All Rights Reserved.
            </div>
        </div>
    </div>
</footer>
</section>
</div>

<!-- Include Google Translate Widget Component -->
<x-translate-widget 
    position="bottom-left" 
    :languages="['en', 'id']" 
    theme="light" 
/>
<!-- Google Translate Widget CSS -->
<link rel="stylesheet" href="{{ asset('css/translate-widget.css') }}">

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

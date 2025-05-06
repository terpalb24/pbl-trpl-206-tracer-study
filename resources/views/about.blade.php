@extends('layouts.app')

<!-- Navbar -->
  <header class="fixed z-999 left-0 right-0 items-center bg-[#0c2a5b] text-white p-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <div class="flex items-center">
        <img src="assets\images/Group 3.png" alt="Logo" class="h-10" />
      </div>

      <!-- Desktop Menu -->
      <nav class="hidden md:flex items-center gap-6">
        <a href="{{route('landing')}}" class="hover:text-[#F2692A]">Beranda</a>
        <a href="#" class="hover:text-[#F2692A]">Tentang</a>
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

<!-- Seksi Penjelasan Tracer Study -->
<section class="bg-[#fdf0ef] py-16 px-6 flex items-center justify-center min-h-screen">
  <div class="max-w-4xl bg-white p-10 rounded-lg shadow-md relative">
    <!-- Garis latar belakang -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;utf8,<svg width=&quot;20&quot; height=&quot;20&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><rect width=&quot;100%&quot; height=&quot;100%&quot; fill=&quot;#fddede&quot;/><path d=&quot;M10 0h1v20h-1z&quot; fill=&quot;#fbcaca&quot;/></svg>')] opacity-20 z-0"></div>

    <div class="relative z-10">
      <!-- Logo -->
      <div class="flex justify-center mb-4">
        <img src="assets/images/Group 3.png" alt="Logo Tracer Study" class="h-16" />
      </div>

      <!-- Judul -->
      <h2 class="text-center text-3xl md:text-4xl font-bold mb-6">
        Tracer Study <span class="text-[#F2692A]">Polibatam</span>
      </h2>

      <!-- Paragraf -->
      <div class="space-y-4 text-justify text-sm text-gray-800">
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
        <p class="font-bold">
          Unit Pengembangan Karir dan Penguatan Karakter
        </p>
      </div>
    </div>
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

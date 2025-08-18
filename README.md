<!-- Improved compatibility of back to top link: See: https://github.com/othneildrew/Best-README-Template/pull/73 -->

<a id="readme-top"></a>

<!--
*** Thanks for checking out the Best-README-Template. If you have a suggestion
*** that would make this better, please fork the repo and create a pull request
*** or simply open an issue with the tag "enhancement".
*** Don't forget to give the project a star!
*** Thanks again! Now go create something AMAZING! :D
-->

<!-- PROJECT SHIELDS -->
<!--
*** I'm using markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->

<!-- [![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![Unlicense License][license-shield]][license-url] -->

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <p align="center">
    Always Connected with Tracer Study Polibatam!
    <br />
    <a href="https://youtu.be/n5jAypMdJKw?si=um1Lod5bA0KJ0ad5">View Demo</a>
    ·
    <a href="mailto:tracerstudypolibatam.206@gmail.com">Report Bug</a>
    ·
    <a href="mailto:tracerstudypolibatam.206@gmail.com">Request Feature</a>
  </p>
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->

## About The Project

[![Product Name Screen Shot][product-screenshot]](https://tracer.pblku.com)

The Polibatam tracer study website is currently used solely for login purposes. Questionnaires are still filled out using another platform (JotForms) embedded within the website. The proposed PBL aims to further develop the previously developed website and display questionnaire results in graphical/statistical form. Currently, the website tracer username and password are based on student ID numbers (SIM) and cannot be changed. This compromises user data security and creates a sense of apprehension among alumni about filling out their data, potentially contributing to low tracer study completion rates.

Our Application Features :

- QUESTIONNAIRE (CREATE & FILL IN)
- ALUMNI DATA MANAGEMENT
- COMPANY DATA MANAGEMENT
- TRACER STUDY STATITICS

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Built With

This is the language, tools, framework and libraries used in creating the Tracer Study application.

[![Build](https://skillicons.dev/icons?i=laravel,tailwind,js,html,css,php,vscode,github)](tracer.pblku.com)

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- GETTING STARTED -->

## Getting Started

There are several steps that must be taken to run the Tracer Study application.

### 1. Clone Repository
```bash
git clone https://github.com/terpalb24/pbl-trpl-206-tracer-study.git
cd pbl-trpl-206-tracer-study
```

### 2. Install Dependency
```bash
composer install
```

### 3. Salin File Environment
```bash
cp .env.example .env
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Konfigurasi `.env`
Ubah pengaturan database di `.env` sesuai konfigurasi lokal:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tracer
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 6. Migrasi dan Seeder Database
```bash
php artisan migrate --seed
```

### 7. Jalankan Server Lokal
```bash
php artisan serve
```

Akses aplikasi melalui: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🛠 Persyaratan Sistem

- PHP >= 8.2
- Composer
- MySQL atau MariaDB
- Node.js & NPM (untuk membangun ulang asset frontend)

---
<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- CONTRIBUTING -->

### Contributors:

Project Manager:

- 118207 - Siti Noor Chayati, S.T., M.Sc

Team Member:

- 4342401036 - Andri Putra Desyandra Siregar
- 4342401037 - Hasna Fadhilah Ramadhan
- 4342401035 - Whelmyran Bima Adhienirma
- 4342401054 - Syifa Dwitya Wulandari
- 4342401058 - Dinny Mardin
- 4342401060 - Eric Marchelino Hutabarat

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- LICENSE -->

## License

Distributed under the Unlicense License.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- CONTACT -->

## Contact

Email - tracerstudypolibatam.206@gmail.com

Project Link: [https://github.com/terpalb24/pbl-trpl-206-tracer-study.git](https://github.com/terpalb24/pbl-trpl-206-tracer-study.git)

Site URL: [https://tracer.pblku.com](https://tracer.pblku.com)

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[contributors-shield]: https://img.shields.io/github/contributors/terpalb24/pbl-trpl-206-tracer-study.svg?style=for-the-badge
[contributors-url]: https://github.com/terpalb24/pbl-trpl-206-tracer-study/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/vterpalb24/pbl-trpl-206-tracer-study.svg?style=for-the-badge
[forks-url]: https://github.com/terpalb24/pbl-trpl-206-tracer-study/network/members
[stars-shield]: https://img.shields.io/github/stars/terpalb24/pbl-trpl-206-tracer-study.svg?style=for-the-badge
[stars-url]: https://github.com/terpalb24/pbl-trpl-206-tracer-study/stargazers
[issues-shield]: https://img.shields.io/github/issues/terpalb24/pbl-trpl-206-tracer-study.svg?style=for-the-badge
[issues-url]: https://github.com/terpalb24/pbl-trpl-206-tracer-study/issues
[license-shield]: https://img.shields.io/github/license/terpalb24/pbl-trpl-206-tracer-study.svg?style=for-the-badge
[license-url]: https://tracer.pblku.com/LICENSE
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/othneildrew
[product-screenshot]: public/assets/images/product.png
[Bootstrap.com]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com
[vscode]: https://skillicons.dev/icons?i=vscode
[VSCODE.url]: https://code.visualstudio.com/
[JavaScript-shield]: https://img.shields.io/badge/JavaScript-323330?style=for-the-badge&logo=javascript&logoColor=F7DF1E
[JavaScript-url]: https://www.javascript.com/
[PHP-shield]: https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white
[PHP-url]: https://www.php.net/
[GitHub-shield]: https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white
[GitHub-url]: https://github.com/
[HTML5-shield]: https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white
[HTML5-url]: https://www.google.com/search?q=html&sca_esv=35aa2c76c27153e3&sxsrf=ADLYWIJHY-u2SSY7sARXtFKmLnKxgk88nw%3A1734520066865&ei=Aq1iZ6K7NO_CjuMPmoK4mAk&ved=0ahUKEwiixMPjlrGKAxVvoWMGHRoBDpMQ4dUDCBA&uact=5&oq=html&gs_lp=Egxnd3Mtd2l6LXNlcnAiBGh0bWwyChAjGIAEGCcYigUyCBAAGIAEGLEDMggQABiABBixAzILEAAYgAQYsQMYgwEyBRAAGIAEMgoQABiABBhDGIoFMgoQABiABBhDGIoFMgoQABiABBhDGIoFMgUQABiABDIFEAAYgARI8wRQuQJYuQJwAXgBkAEAmAFeoAFeqgEBMbgBA8gBAPgBAZgCAqACZ8ICChAAGLADGNYEGEfCAg0QABiABBiwAxhDGIoFmAMAiAYBkAYKkgcBMqAHtgU&sclient=gws-wiz-serp
[ionicon-shield]: https://img.shields.io/badge/Ionicons-3880FF?style=for-the-badge&logo=ionic&logoColor=white
[ionicon-url]: https://ionic.io/ionicons
[sweetalert-shield]: https://img.shields.io/badge/SweetAlert2-3880FF?style=for-the-badge
[sweetalert-url]: https://sweetalert2.github.io/
[fpdf-shield]: https://img.shields.io/badge/FPDF-3880FF?style=for-the-badge
[fpdf-url]: https://www.fpdf.org/
[tailwind]: https://skillicons.dev/icons?i=tailwind
[Tailwind-url]: https://tailwindcss.com/
[laravel]: https://skillicons.dev/icons?i=laravel
[Laravel-url]: https://laravel.com/
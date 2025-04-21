<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('welcome');
});

// Menampilkan halaman login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
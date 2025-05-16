<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\CompanyController;
use App\Http\Middleware\CheckRole;

Route::get('/', fn () => view('landing'))->name('landing');
Route::get('/about', fn () => view('about'))->name('about');

// Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

// Dashboard masing-masing role
Route::middleware(['auth:web', CheckRole::class . ':1'])->get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('dashboard.admin');
Route::middleware(['auth:web', CheckRole::class . ':2'])->get('/alumni/dashboard', [AlumniController::class, 'dashboard'])->name('dashboard.alumni');
Route::middleware(['auth:web', CheckRole::class . ':3'])->get('/company/dashboard', [CompanyController::class, 'dashboard'])->name('dashboard.company');

// Verifikasi email (harus login)
Route::middleware(['auth'])->group(function () {
    Route::get('/alumni/email', [AlumniController::class, 'showEmailForm'])->name('alumni.email.form');
    Route::post('/alumni/email', [AlumniController::class, 'verifyEmail'])->name('alumni.email.verify');
});

// Ganti password (akses langsung dari email, jadi tidak pakai auth)
Route::get('/alumni/password/{token}', [AlumniController::class, 'showChangePasswordForm'])->name('alumni.password.form');
Route::post('/alumni/password', [AlumniController::class, 'updatePassword'])->name('alumni.password.update');

// Profil alumni (harus login dan role alumni)
Route::middleware(['auth:web', CheckRole::class . ':2'])->group(function () {
    Route::get('/alumni/profil', [AlumniController::class, 'edit'])->name('alumni.edit');
    Route::put('/alumni/profil', [AlumniController::class, 'update'])->name('alumni.update');
});

//change password all user
Route::middleware('auth')->group(function (){
    Route::get('/change-password', [AuthController::class, 'ChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'updatePasswordAll'])->name('password.update');
});

//profil perusahaan (harus login)
Route::middleware(['auth:web', CheckRole::class . ':3'])->group(function () {
    Route::get('/company/profil', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company/profil', [CompanyController::class, 'update'])->name('company.update');
});


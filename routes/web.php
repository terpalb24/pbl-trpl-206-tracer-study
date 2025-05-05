<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\CompanyController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Middleware\CheckRole;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', function () {
    return view('test');
});
Route::get('/halo', function(){
    return view('halo');
});
// Menampilkan halaman login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Proses login
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
//logout
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');
// untuk ke dashboard admin, alumni, dan company
Route::middleware(['auth:web', CheckRole::class . ':1'])->get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('dashboard.admin');
Route::middleware(['auth:web', CheckRole::class . ':2'])->get('/alumni/dashboard', [AlumniController::class, 'dashboard'])->name('dashboard.alumni');
Route::middleware(['auth:web', CheckRole::class . ':3'])->get('/company/dashboard', [CompanyController::class, 'dashboard'])->name('dashboard.company');

// untuk memverifikasi email
Route::group(['middleware' => ['auth']], function(){
Route::get('/alumni/email', [AlumniController::class, 'showEmailForm'])->name('alumni.email.form');
Route::post('/alumni/email', [AlumniController::class, 'verifyEmail'])->name('alumni.email.verify');
Route::get('/alumni/password', [AlumniController::class, 'showChangePasswordForm'])->name('alumni.password.form');
Route::post('/alumni/password', [AlumniController::class, 'updatePassword'])->name('alumni.password.update');
});
// untuk alumni mengedit profil alumni
Route::middleware(['auth:web', CheckRole::class . ':2'])->group(function () {
    Route::get('/alumni/profil', [AlumniController::class, 'edit'])->name('alumni.edit');
    Route::put('/alumni/profil', [AlumniController::class, 'update'])->name('alumni.update');

});

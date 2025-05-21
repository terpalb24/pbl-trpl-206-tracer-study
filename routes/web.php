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

//profil perusahaan (harus login dan role perusahaan)
Route::middleware(['auth:web', CheckRole::class . ':3'])->group(function () {
    Route::get('/company/profil', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company/profil', [CompanyController::class, 'update'])->name('company.update');
});

// Admin routes untuk manage alumni
Route::middleware(['auth:web', CheckRole::class . ':1'])->group(function () {
    Route::get('/admin/alumni', [AdminController::class, 'alumniIndex'])->name('admin.alumni.index');
    Route::get('admin/alumni/create', [AdminController::class, 'alumniCreate'])->name('admin.alumni.create');
    Route::post('admin/alumni/store', [AdminController::class, 'alumniStore'])->name('admin.alumni.store');    
    Route::get('admin/alumni/edit/{nim}', [AdminController::class, 'alumniEdit'])->name('admin.alumni.edit');
    Route::put('admin/alumni/{nim}', [AdminController::class, 'alumniUpdate'])->name('admin.alumni.update');
    Route::delete('admin/alumni/{id_user}', [AdminController::class, 'alumniDestroy'])->name('admin.alumni.destroy');
    Route::post('/admin/alumni/import', [AdminController::class, 'import'])->name('admin.alumni.import');
    Route::get('/admin/alumni/export', [AdminController::class, 'export'])->name('alumni.export');


});
//routes admin untuk manage perusahaan
Route::middleware(['auth:web', CheckRole::class . ':1'])->group(function () {
    Route::get('/admin/company', [AdminController::class, 'companyIndex'])->name('admin.company.index');
    Route::get('admin/company/create', [AdminController::class, 'companyCreate'])->name('admin.company.create');
    Route::post('admin/company/store', [AdminController::class, 'companyStore'])->name('admin.company.store');    
    Route::get('admin/company/edit/{id_company}', [AdminController::class, 'companyEdit'])->name('admin.company.edit');
    Route::put('admin/company/{id_company}', [AdminController::class, 'companyUpdate'])->name('admin.company.update');
    Route::delete('admin/company/{id_user}', [AdminController::class, 'companyDestroy'])->name('admin.company.destroy');
    Route::post('/admin/company/import', [AdminController::class, 'companyImport'])->name('admin.company.import');
    Route::get('/admin/company/export', [AdminController::class, 'companyExport'])->name('company.export');


});






<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\CompanyController;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', function () {
    return view('test');
});
Route::get('/halo', function(){
    return view('halo');
});

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class , 'logout'])->name('logout');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->name('dashboard.admin'); 

// Dashboard untuk Alumni (role 2)
Route::get('/alumni/dashboard', [AlumniController::class, 'dashboard'])
    ->name('dashboard.alumni');

// Dashboard untuk Company (role 3)
Route::get('/company/dashboard', [CompanyController::class, 'dashboard'])
    ->name('dashboard.company');

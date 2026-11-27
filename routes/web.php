<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Install\InstallController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/demo1', [HomeController::class, 'demo1'])->name('demo1');
Route::get('/demo2', [HomeController::class, 'demo2'])->name('demo2');
Route::get('/demo3', [HomeController::class, 'demo3'])->name('demo3');
Route::post('/contact', [HomeController::class, 'contact'])
    ->middleware('throttle:contact')
    ->name('contact.submit');

// First-time installation wizard (404 once the site is installed) and the admin panel.
Route::get('/install', [InstallController::class, 'index'])->name('install.index');
Route::post('/install', [InstallController::class, 'store'])->name('install.store');

require __DIR__.'/admin.php';

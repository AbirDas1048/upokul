<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/demo1', [HomeController::class, 'demo1'])->name('demo1');
Route::get('/demo2', [HomeController::class, 'demo2'])->name('demo2');
Route::get('/demo3', [HomeController::class, 'demo3'])->name('demo3');
Route::post('/contact', [HomeController::class, 'contact'])->name('contact.submit');

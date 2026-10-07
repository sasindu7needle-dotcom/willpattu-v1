<?php

use App\Http\Controllers\ParkController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ParkController::class, 'home'])->name('home');
Route::post('/contact', [ParkController::class, 'enquire'])->middleware('throttle:5,1')->name('enquire');
Route::get('/journal/{slug}', [ParkController::class, 'article'])->name('article');
Route::get('/{slug}', [ParkController::class, 'page'])->name('page');

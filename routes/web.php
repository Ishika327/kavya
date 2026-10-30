<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WritingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EngagementController;
use App\Http\Controllers\PublicReadingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicReadingController::class, 'home'])->name('home');
Route::get('/explore', [PublicReadingController::class, 'explore'])->name('explore');
Route::get('/categories', [PublicReadingController::class, 'categories'])->name('categories');
Route::get('/categories/{category:slug}', [PublicReadingController::class, 'category'])->name('categories.show');
Route::get('/writings/{writing:slug}', [PublicReadingController::class, 'show'])->name('writings.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegistration'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::post('/writings/{writing:slug}/like', [EngagementController::class, 'like'])->name('writings.like');
    Route::post('/writings/{writing:slug}/favorite', [EngagementController::class, 'favorite'])->name('writings.favorite');
    Route::post('/writings/{writing:slug}/comments', [EngagementController::class, 'comment'])->name('writings.comments.store');
    Route::get('/favorites', [EngagementController::class, 'favorites'])->name('favorites');
    Route::get('/history', [EngagementController::class, 'history'])->name('history');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('writings', WritingController::class)->except(['show']);
});

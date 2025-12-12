<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalendarFeedController;
use App\Http\Controllers\RehearsalPlanController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('calendar.index'))->name('home');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/rehearsals/{rehearsal}/plan', [RehearsalPlanController::class, 'show'])->name('rehearsals.plan');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get('/calendar/feed/{token}', CalendarFeedController::class)->name('calendar.feed');

Route::view('/imprint', 'legal.imprint')->name('legal.imprint');
Route::view('/privacy', 'legal.privacy')->name('legal.privacy');
Route::view('/data-deletion', 'legal.data-deletion')->name('legal.data-deletion');

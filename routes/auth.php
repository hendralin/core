<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\ForgotPassword;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\ConfirmPassword;
use App\Http\Controllers\Auth\VerifyEmailController;

Route::middleware('guest')->group(function () {
    Route::livewire('login', Login::class)->name('login');
    // Route::get('register', Register::class)->name('register');
    Route::livewire('forgot-password', ForgotPassword::class)->name('password.request');
    Route::livewire('reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::livewire('verify-email', VerifyEmail::class)
        ->name('verification.notice');

    Route::livewire('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::livewire('confirm-password', ConfirmPassword::class)
        ->name('password.confirm');
});

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');

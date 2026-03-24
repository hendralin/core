<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\NewsController;
use App\Http\Controllers\Api\V1\SignalController;
use App\Http\Controllers\Api\V1\SubscriptionController;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|
| Routes for API version 1.
|
*/

// Public routes with auth rate limiter (5/min - brute force protection)
Route::middleware('throttle:auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register'])->name('api.v1.register');
    Route::post('login', [AuthController::class, 'login'])->name('api.v1.login');
});

// Blog: public list and show
Route::get('posts', [BlogController::class, 'index'])->name('api.v1.posts.index');
Route::get('posts/{slug}', [BlogController::class, 'show'])->name('api.v1.posts.show');

// News headlines (public, throttled)
Route::middleware('throttle:60,1')->group(function (): void {
    Route::get('news/headlines', [NewsController::class, 'headlines'])->name('api.v1.news.headlines');
});

// Categories and tags (for blog filters)
Route::get('categories', [CategoryController::class, 'index'])->name('api.v1.categories.index');
Route::get('categories/{slug}', [CategoryController::class, 'show'])->name('api.v1.categories.show');
Route::get('tags', [TagController::class, 'index'])->name('api.v1.tags.index');
Route::get('tags/{slug}', [TagController::class, 'show'])->name('api.v1.tags.show');

// Protected routes with authenticated rate limiter (120/min)
Route::middleware(['auth:sanctum', 'throttle:authenticated'])->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout'])->name('api.v1.logout');
    Route::get('me', [AuthController::class, 'me'])->name('api.v1.me');

    // Subscription & Signals (authenticated, subscription required for signals)
    Route::get('subscription', [SubscriptionController::class, 'show'])->name('api.v1.subscription.show');
    Route::get('signals', [SignalController::class, 'index'])->name('api.v1.signals.index');

    // Blog: add comment (authenticated only)
    Route::post('posts/{slug}/comments', [BlogController::class, 'storeComment'])->name('api.v1.posts.comments.store');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API routes with token authentication
Route::middleware(['auth:api'])->group(function () {
    // Message API endpoints
    Route::prefix('messages')->name('api.messages.')->group(function () {
        Route::post('/sendText', [MessageController::class, 'sendText'])->name('send-text');
        Route::post('/sendImage', [MessageController::class, 'sendImage'])->name('send-image');
        Route::post('/sendFile', [MessageController::class, 'sendFile'])->name('send-file');
        Route::post('/link-custom-preview', [MessageController::class, 'sendCustomLink'])->name('send-custom-link');
        Route::post('/sendTemplate', [MessageController::class, 'sendTemplate'])->name('send-template');
    });
});


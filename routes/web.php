<?php

use App\Livewire\Roles\RoleEdit;
use App\Livewire\Roles\RoleShow;
use App\Livewire\Users\UserEdit;
use App\Livewire\Users\UserShow;
use App\Livewire\Roles\RoleAudit;
use App\Livewire\Roles\RoleIndex;
use App\Livewire\Users\UserAudit;
use App\Livewire\Users\UserIndex;
use App\Livewire\About\AboutIndex;
use App\Livewire\Roles\RoleCreate;
use App\Livewire\Settings\Profile;
use App\Livewire\Users\UserCreate;
use App\Livewire\Settings\Password;
use App\Livewire\Company\CompanyEdit;
use App\Livewire\Company\CompanyShow;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;
use App\Livewire\BackupRestore\BackupRestoreIndex;

// License expired page (accessible even when license is expired)
Route::get('/license-expired', function () {
    return view('license.expired');
})->name('license.expired');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('/', function () {
        return view('dashboard');
    })->name('home');

    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('company/edit', CompanyEdit::class)->name('company.edit')->middleware(['permission:company.edit']);
    Route::get('company', CompanyShow::class)->name('company.show')->middleware(['permission:company.view']);

    Route::get('users', UserIndex::class)->name('users.index');
    Route::get('users/audit', UserAudit::class)->name('users.audit');
    Route::get('users/create', UserCreate::class)->name('users.create');
    Route::get('users/{user}/edit', UserEdit::class)->name('users.edit');
    Route::get('users/{user}', UserShow::class)->name('users.show');
    Route::get('users/download/{filename}', function ($filename) {
        $path = storage_path('app/temp/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    })->name('users.download')->middleware('auth');

    Route::get('roles', RoleIndex::class)->name('roles.index')->middleware(['permission:role.view|role.create|role.edit|role.delete']);
    Route::get('roles/audit', RoleAudit::class)->name('roles.audit')->middleware(['permission:role.view']);
    Route::get('roles/create', RoleCreate::class)->name('roles.create')->middleware(['permission:role.create']);
    Route::get('roles/{role}/edit', RoleEdit::class)->name('roles.edit')->middleware(['permission:role.edit']);
    Route::get('roles/{role}', RoleShow::class)->name('roles.show')->middleware(['permission:role.view']);

    Route::prefix('backup-restore')->name('backup-restore.')->group(function () {
        Route::get('/', BackupRestoreIndex::class)->name('index')->middleware(['permission:backup-restore.view']);
    });

    Route::get('about', AboutIndex::class)->name('about.index');
});

require __DIR__ . '/auth.php';

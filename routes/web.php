<?php

use App\Livewire\Roles\RoleEdit;
use App\Livewire\Roles\RoleShow;
use App\Livewire\Users\UserEdit;
use App\Livewire\Users\UserShow;
use App\Livewire\Waha\WahaIndex;
use App\Livewire\Roles\RoleAudit;
use App\Livewire\Roles\RoleIndex;
use App\Livewire\Users\UserAudit;
use App\Livewire\Users\UserIndex;
use App\Livewire\About\AboutIndex;
use App\Livewire\Roles\RoleCreate;
use App\Livewire\Settings\Profile;
use App\Livewire\Users\UserCreate;
use App\Livewire\Groups\GroupsShow;
use App\Livewire\Settings\Password;
use App\Livewire\Groups\GroupsIndex;
use App\Livewire\Company\CompanyEdit;
use App\Livewire\Company\CompanyShow;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;
use App\Livewire\Contacts\ContactsShow;
use App\Livewire\Sessions\SessionsEdit;
use App\Livewire\Sessions\SessionsShow;
use App\Livewire\Contacts\ContactsIndex;
use App\Livewire\Sessions\SessionsAudit;
use App\Livewire\Sessions\SessionsIndex;
use App\Livewire\Sessions\SessionsCreate;
use App\Livewire\Templates\TemplatesEdit;
use App\Livewire\Templates\TemplatesShow;
use App\Livewire\Templates\TemplatesAudit;
use App\Livewire\Templates\TemplatesIndex;
use App\Livewire\Templates\TemplatesCreate;
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

    Route::get('users', UserIndex::class)->name('users.index')->middleware(['permission:user.view']);
    Route::get('users/audit', UserAudit::class)->name('users.audit')->middleware(['permission:user.audit']);
    Route::get('users/create', UserCreate::class)->name('users.create')->middleware(['permission:user.create']);
    Route::get('users/{user}/edit', UserEdit::class)->name('users.edit')->middleware(['permission:user.edit']);
    Route::get('users/{user}', UserShow::class)->name('users.show')->middleware(['permission:user.view']);
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
    Route::get('roles/audit', RoleAudit::class)->name('roles.audit')->middleware(['permission:role.audit']);
    Route::get('roles/create', RoleCreate::class)->name('roles.create')->middleware(['permission:role.create']);
    Route::get('roles/{role}/edit', RoleEdit::class)->name('roles.edit')->middleware(['permission:role.edit']);
    Route::get('roles/{role}', RoleShow::class)->name('roles.show')->middleware(['permission:role.view']);

    Route::get('waha', WahaIndex::class)->name('waha.index')->middleware(['permission:waha.view|waha.edit']);

    Route::get('sessions', SessionsIndex::class)->name('sessions.index')->middleware(['permission:session.view|session.create|session.edit|session.delete']);
    Route::get('sessions/audit', SessionsAudit::class)->name('sessions.audit')->middleware(['permission:session.view']);
    Route::get('sessions/create', SessionsCreate::class)->name('sessions.create')->middleware(['permission:session.create']);
    Route::get('sessions/{session}/edit', SessionsEdit::class)->name('sessions.edit')->middleware(['permission:session.edit']);
    Route::get('sessions/{session}', SessionsShow::class)->name('sessions.show')->middleware(['permission:session.view']);

    Route::get('contacts', ContactsIndex::class)->name('contacts.index')->middleware(['permission:contact.view|contact.sync']);
    Route::get('contacts/{contact}', ContactsShow::class)->name('contacts.show')->middleware(['permission:contact.view']);

    Route::get('groups', GroupsIndex::class)->name('groups.index')->middleware(['permission:group.view|group.sync']);
    Route::get('groups/{group}', GroupsShow::class)->name('groups.show')->middleware(['permission:group.view']);

    Route::get('templates', TemplatesIndex::class)->name('templates.index')->middleware(['permission:template.view|template.create|template.edit|template.delete']);
    Route::get('templates/audit', TemplatesAudit::class)->name('templates.audit')->middleware(['permission:template.view']);
    Route::get('templates/create', TemplatesCreate::class)->name('templates.create')->middleware(['permission:template.create']);
    Route::get('templates/{template}/edit', TemplatesEdit::class)->name('templates.edit')->middleware(['permission:template.edit']);
    Route::get('templates/{template}', TemplatesShow::class)->name('templates.show')->middleware(['permission:template.view']);

    Route::prefix('backup-restore')->name('backup-restore.')->group(function () {
        Route::get('/', BackupRestoreIndex::class)->name('index')->middleware(['permission:backup-restore.view']);
    });

    Route::get('about', AboutIndex::class)->name('about.index');
});

require __DIR__ . '/auth.php';

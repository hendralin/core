<?php

use App\Livewire\About\AboutIndex;
use App\Livewire\BackupRestore\BackupRestoreIndex;
use App\Livewire\Broadcast\Messages\MesssagesAudit;
use App\Livewire\Broadcast\Messages\MesssagesIndex;
use App\Livewire\Company\CompanyEdit;
use App\Livewire\Company\CompanyShow;
use App\Livewire\Contacts\ContactsIndex;
use App\Livewire\Contacts\ContactsShow;
use App\Livewire\Dashboard\DashboardIndex;
use App\Livewire\Groups\GroupsIndex;
use App\Livewire\Groups\GroupsShow;
use App\Livewire\Roles\RoleAudit;
use App\Livewire\Roles\RoleCreate;
use App\Livewire\Roles\RoleEdit;
use App\Livewire\Roles\RoleIndex;
use App\Livewire\Roles\RoleShow;
use App\Livewire\Schedules\SchedulesAudit;
use App\Livewire\Schedules\SchedulesCreate;
use App\Livewire\Schedules\SchedulesEdit;
use App\Livewire\Schedules\SchedulesIndex;
use App\Livewire\Schedules\SchedulesShow;
use App\Livewire\Sessions\SessionsAudit;
use App\Livewire\Sessions\SessionsCreate;
use App\Livewire\Sessions\SessionsEdit;
use App\Livewire\Sessions\SessionsIndex;
use App\Livewire\Sessions\SessionsShow;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Templates\TemplatesAudit;
use App\Livewire\Templates\TemplatesCreate;
use App\Livewire\Templates\TemplatesEdit;
use App\Livewire\Templates\TemplatesIndex;
use App\Livewire\Templates\TemplatesShow;
use App\Livewire\Users\UserAudit;
use App\Livewire\Users\UserCreate;
use App\Livewire\Users\UserEdit;
use App\Livewire\Users\UserIndex;
use App\Livewire\Users\UserShow;
use App\Livewire\Waha\WahaIndex;
use Illuminate\Support\Facades\Route;

// License expired page (accessible even when license is expired)
Route::get('/license-expired', function () {
    return view('license.expired');
})->name('license.expired');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', Profile::class)->name('settings.profile');
    Route::livewire('settings/password', Password::class)->name('settings.password');
    Route::livewire('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::livewire('/', DashboardIndex::class)->name('home');
    Route::livewire('dashboard', DashboardIndex::class)->name('dashboard');

    Route::livewire('company/edit', CompanyEdit::class)->name('company.edit')->middleware(['permission:company.edit']);
    Route::livewire('company', CompanyShow::class)->name('company.show')->middleware(['permission:company.view']);

    Route::livewire('users', UserIndex::class)->name('users.index')->middleware(['permission:user.view']);
    Route::livewire('users/audit', UserAudit::class)->name('users.audit')->middleware(['permission:user.audit']);
    Route::livewire('users/create', UserCreate::class)->name('users.create')->middleware(['permission:user.create']);
    Route::livewire('users/{user}/edit', UserEdit::class)->name('users.edit')->middleware(['permission:user.edit']);
    Route::livewire('users/{user}', UserShow::class)->name('users.show')->middleware(['permission:user.view']);
    Route::livewire('users/download/{filename}', function ($filename) {
        $path = storage_path('app/temp/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    })->name('users.download')->middleware('auth');

    Route::livewire('roles', RoleIndex::class)->name('roles.index')->middleware(['permission:role.view|role.create|role.edit|role.delete']);
    Route::livewire('roles/audit', RoleAudit::class)->name('roles.audit')->middleware(['permission:role.audit']);
    Route::livewire('roles/create', RoleCreate::class)->name('roles.create')->middleware(['permission:role.create']);
    Route::livewire('roles/{role}/edit', RoleEdit::class)->name('roles.edit')->middleware(['permission:role.edit']);
    Route::livewire('roles/{role}', RoleShow::class)->name('roles.show')->middleware(['permission:role.view']);

    Route::livewire('waha', WahaIndex::class)->name('waha.index')->middleware(['permission:waha.view|waha.edit']);

    Route::livewire('sessions', SessionsIndex::class)->name('sessions.index')->middleware(['permission:session.view|session.create|session.edit|session.delete']);
    Route::livewire('sessions/audit', SessionsAudit::class)->name('sessions.audit')->middleware(['permission:session.audit']);
    Route::livewire('sessions/create', SessionsCreate::class)->name('sessions.create')->middleware(['permission:session.create']);
    Route::livewire('sessions/{session}/edit', SessionsEdit::class)->name('sessions.edit')->middleware(['permission:session.edit']);
    Route::livewire('sessions/{session}', SessionsShow::class)->name('sessions.show')->middleware(['permission:session.view']);

    Route::livewire('contacts', ContactsIndex::class)->name('contacts.index')->middleware(['permission:contact.view|contact.sync']);
    Route::livewire('contacts/{contact}', ContactsShow::class)->name('contacts.show')->middleware(['permission:contact.view']);

    Route::livewire('groups', GroupsIndex::class)->name('groups.index')->middleware(['permission:group.view|group.sync']);
    Route::livewire('groups/{group}', GroupsShow::class)->name('groups.show')->middleware(['permission:group.view']);

    Route::livewire('templates', TemplatesIndex::class)->name('templates.index')->middleware(['permission:template.view|template.create|template.edit|template.delete']);
    Route::livewire('templates/audit', TemplatesAudit::class)->name('templates.audit')->middleware(['permission:template.audit']);
    Route::livewire('templates/create', TemplatesCreate::class)->name('templates.create')->middleware(['permission:template.create']);
    Route::livewire('templates/{template}/edit', TemplatesEdit::class)->name('templates.edit')->middleware(['permission:template.edit']);
    Route::livewire('templates/{template}', TemplatesShow::class)->name('templates.show')->middleware(['permission:template.view']);

    Route::livewire('messages', MesssagesIndex::class)->name('messages.index')->middleware(['permission:message.view|message.send|message.audit']);
    Route::livewire('messages/audit', MesssagesAudit::class)->name('messages.audit')->middleware(['permission:message.audit']);

    Route::livewire('schedules', SchedulesIndex::class)->name('schedules.index')->middleware(['permission:schedule.view|schedule.create|schedule.edit|schedule.delete']);
    Route::livewire('schedules/audit', SchedulesAudit::class)->name('schedules.audit')->middleware(['permission:schedule.audit']);
    Route::livewire('schedules/create', SchedulesCreate::class)->name('schedules.create')->middleware(['permission:schedule.create']);
    Route::livewire('schedules/{schedule}/edit', SchedulesEdit::class)->name('schedules.edit')->middleware(['permission:schedule.edit']);
    Route::livewire('schedules/{schedule}', SchedulesShow::class)->name('schedules.show')->middleware(['permission:schedule.view']);

    Route::prefix('backup-restore')->name('backup-restore.')->group(function () {
        Route::livewire('/', BackupRestoreIndex::class)->name('index')->middleware(['permission:backup-restore.view']);
    });

    Route::livewire('about', AboutIndex::class)->name('about.index');
});

require __DIR__ . '/auth.php';

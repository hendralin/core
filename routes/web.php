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
use App\Livewire\Signals\Admin\SignalsEdit;
use App\Livewire\Signals\Admin\SignalsShow;
use App\Livewire\Signals\Admin\SignalsAudit;
use App\Livewire\Signals\Admin\SignalsIndex;
use App\Livewire\Signals\Admin\SignalsCreate;
use App\Livewire\BackupRestore\BackupRestoreIndex;
use App\Livewire\TradingSummary\StockSummaryIndex;

// License expired page (accessible even when license is expired)
Route::get('/license-expired', function () {
    return view('license.expired');
})->name('license.expired');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', Profile::class)->name('settings.profile');
    Route::livewire('settings/password', Password::class)->name('settings.password');
    Route::livewire('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('/', function () {
        return view('dashboard');
    })->name('home');

    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

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

    Route::livewire('stock-summary', StockSummaryIndex::class)->name('stock-summary.index')->middleware(['permission:stock-summary.view']);

    Route::livewire('admin/signals', SignalsIndex::class)->name('admin.signals.index')->middleware(['permission:admin.signal.view']);
    Route::livewire('admin/signals/audit', SignalsAudit::class)->name('admin.signals.audit')->middleware(['permission:admin.signal.audit']);
    Route::livewire('admin/signals/create', SignalsCreate::class)->name('admin.signals.create')->middleware(['permission:admin.signal.create']);
    Route::livewire('admin/signals/{signal}/edit', SignalsEdit::class)->name('admin.signals.edit')->middleware(['permission:admin.signal.edit']);
    Route::livewire('admin/signals/{signal}', SignalsShow::class)->name('admin.signals.show')->middleware(['permission:admin.signal.view']);

    Route::livewire('categories', 'pages::blog.categories.index')->name('blog.categories.index')->middleware(['permission:blog.category.view|blog.category.create|blog.category.edit|blog.category.delete|blog.category.audit']);
    // Route::livewire('categories/audit', CategoriesAudit::class)->name('blog.categories.audit')->middleware(['permission:blog.category.audit']);
    // Route::livewire('categories/create', CategoriesCreate::class)->name('blog.categories.create')->middleware(['permission:blog.category.create']);
    // Route::livewire('categories/{category}/edit', CategoriesEdit::class)->name('blog.categories.edit')->middleware(['permission:blog.category.edit']);
    // Route::livewire('categories/{category}', CategoriesShow::class)->name('blog.categories.show')->middleware(['permission:blog.category.view']);

    // Route::livewire('tags', TagsIndex::class)->name('tags.index')->middleware(['permission:blog.tag.view|blog.tag.create|blog.tag.edit|blog.tag.delete|blog.tag.audit']);
    // Route::livewire('tags/audit', TagsAudit::class)->name('tags.audit')->middleware(['permission:blog.tag.audit']);
    // Route::livewire('tags/create', TagsCreate::class)->name('tags.create')->middleware(['permission:blog.tag.create']);
    // Route::livewire('tags/{tag}/edit', TagsEdit::class)->name('tags.edit')->middleware(['permission:blog.tag.edit']);
    // Route::livewire('tags/{tag}', TagsShow::class)->name('tags.show')->middleware(['permission:blog.tag.view']);

    // Route::livewire('posts', PostsIndex::class)->name('posts.index')->middleware(['permission:blog.post.view|blog.post.create|blog.post.edit.own|blog.post.edit.all|blog.post.delete.own|blog.post.delete.all|blog.post.publish|blog.post.audit']);
    // Route::livewire('posts/audit', PostsAudit::class)->name('posts.audit')->middleware(['permission:blog.post.audit']);
    // Route::livewire('posts/create', PostsCreate::class)->name('posts.create')->middleware(['permission:blog.post.create']);
    // Route::livewire('posts/{post}/edit', PostsEdit::class)->name('posts.edit')->middleware(['permission:blog.post.edit']);
    // Route::livewire('posts/{post}', PostsShow::class)->name('posts.show')->middleware(['permission:blog.post.view']);

    Route::prefix('backup-restore')->name('backup-restore.')->group(function () {
        Route::livewire('/', BackupRestoreIndex::class)->name('index')->middleware(['permission:backup-restore.view']);
    });

    Route::livewire('about', AboutIndex::class)->name('about.index');
});

require __DIR__ . '/auth.php';

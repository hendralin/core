<?php

use App\Livewire\About\AboutIndex;
use App\Livewire\BackupRestore\BackupRestoreIndex;
use App\Livewire\Blog\Categories\CategoryAudit;
use App\Livewire\Blog\Categories\CategoryCreate;
use App\Livewire\Blog\Categories\CategoryEdit;
use App\Livewire\Blog\Categories\CategoryIndex;
use App\Livewire\Blog\Categories\CategoryShow;
use App\Livewire\Blog\Comments\CommentsIndex;
use App\Livewire\Blog\Dashboard\DashboardBlog;
use App\Livewire\Blog\Posts\PostsAudit;
use App\Livewire\Blog\Posts\PostsCreate;
use App\Livewire\Blog\Posts\PostsEdit;
use App\Livewire\Blog\Posts\PostsIndex;
use App\Livewire\Blog\Posts\PostsShow;
use App\Livewire\Blog\Tags\TagsAudit;
use App\Livewire\Blog\Tags\TagsCreate;
use App\Livewire\Blog\Tags\TagsEdit;
use App\Livewire\Blog\Tags\TagsIndex;
use App\Livewire\Blog\Tags\TagsShow;
use App\Livewire\Company\CompanyEdit;
use App\Livewire\Company\CompanyShow;
use App\Livewire\Home\HomeIndex;
use App\Livewire\PublicBlog\BlogIndex;
use App\Livewire\PublicBlog\BlogShow;
use App\Livewire\Roles\RoleAudit;
use App\Livewire\Roles\RoleCreate;
use App\Livewire\Roles\RoleEdit;
use App\Livewire\Roles\RoleIndex;
use App\Livewire\Roles\RoleShow;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Signals\Admin\SignalsAudit;
use App\Livewire\Signals\Admin\SignalsCreate;
use App\Livewire\Signals\Admin\SignalsEdit;
use App\Livewire\Signals\Admin\SignalsIndex;
use App\Livewire\Signals\Admin\SignalsShow;
use App\Livewire\Signals\User\SignalsIndex as SignalsUserIndex;
use App\Livewire\TradingSummary\StockSummaryIndex;
use App\Livewire\Users\UserAudit;
use App\Livewire\Users\UserCreate;
use App\Livewire\Users\UserEdit;
use App\Livewire\Users\UserIndex;
use App\Livewire\Users\UserShow;
use Illuminate\Support\Facades\Route;

// License expired page (accessible even when license is expired)
Route::get('/license-expired', function () {
    return view('license.expired');
})->name('license.expired');

// Public routes (landing & blog)
Route::livewire('/', HomeIndex::class)->name('home');

Route::livewire('blogs', BlogIndex::class)->name('blogs.index');
Route::livewire('blogs/{post:slug}', BlogShow::class)->name('blogs.show');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', Profile::class)->name('settings.profile');
    Route::livewire('settings/password', Password::class)->name('settings.password');
    Route::livewire('settings/appearance', Appearance::class)->name('settings.appearance');

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

    Route::livewire('signals', SignalsUserIndex::class)->name('signals.index')->middleware(['permission:signal.view']);

    Route::livewire('dashboard/blog', DashboardBlog::class)->name('blog.dashboard')->middleware(['permission:blog.dashboard.view']);
    Route::livewire('categories', CategoryIndex::class)->name('blog.categories.index')->middleware(['permission:blog.category.view|blog.category.create|blog.category.edit|blog.category.delete']);
    Route::livewire('categories/create', CategoryCreate::class)->name('blog.categories.create')->middleware(['permission:blog.category.create']);
    Route::livewire('categories/{category}', CategoryShow::class)->name('blog.categories.show')->middleware(['permission:blog.category.view']);
    Route::livewire('categories/{category}/edit', CategoryEdit::class)->name('blog.categories.edit')->middleware(['permission:blog.category.edit']);
    Route::livewire('categories/{category}/audit', CategoryAudit::class)->name('blog.categories.audit')->middleware(['permission:blog.category.audit']);

    Route::livewire('tags', TagsIndex::class)->name('blog.tags.index')->middleware(['permission:blog.tag.view|blog.tag.create|blog.tag.edit|blog.tag.delete']);
    Route::livewire('tags/create', TagsCreate::class)->name('blog.tags.create')->middleware(['permission:blog.tag.create']);
    Route::livewire('tags/{tag}', TagsShow::class)->name('blog.tags.show')->middleware(['permission:blog.tag.view']);
    Route::livewire('tags/{tag}/edit', TagsEdit::class)->name('blog.tags.edit')->middleware(['permission:blog.tag.edit']);
    Route::livewire('tags/{tag}/audit', TagsAudit::class)->name('blog.tags.audit')->middleware(['permission:blog.tag.audit']);

    Route::livewire('posts', PostsIndex::class)->name('blog.posts.index')->middleware(['permission:blog.post.view|blog.post.create|blog.post.edit.own|blog.post.edit.all|blog.post.delete.own|blog.post.delete.all']);
    Route::livewire('posts/{post}/audit', PostsAudit::class)->name('blog.posts.audit')->middleware(['permission:blog.post.audit']);
    Route::livewire('posts/create', PostsCreate::class)->name('blog.posts.create')->middleware(['permission:blog.post.create']);
    Route::livewire('posts/{post}/edit', PostsEdit::class)->name('blog.posts.edit')->middleware(['permission:blog.post.edit.own|blog.post.edit.all']);
    Route::livewire('posts/{post}', PostsShow::class)->name('blog.posts.show')->middleware(['permission:blog.post.view']);

    Route::livewire('comments', CommentsIndex::class)->name('blog.comments.index')->middleware(['permission:blog.comment.view|blog.comment.status']);

    Route::prefix('backup-restore')->name('backup-restore.')->group(function () {
        Route::livewire('/', BackupRestoreIndex::class)->name('index')->middleware(['permission:backup-restore.view']);
    });

    Route::livewire('about', AboutIndex::class)->name('about.index');
});

require __DIR__ . '/auth.php';

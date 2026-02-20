<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'company.view',
            'company.edit',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'user.audit',
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'role.audit',
            'backup-restore.view',
            'backup-restore.create',
            'backup-restore.download',
            'backup-restore.restore',
            'backup-restore.delete',

            // Trading Summary
            'stock-summary.view',

            // Featuring
            'admin.signal.view',
            'admin.signal.create',
            'admin.signal.edit',
            'admin.signal.publish',
            'admin.signal.unpublish',
            'admin.signal.audit',

            // Blogs
            // Categories
            'blog.category.view',
            'blog.category.create',
            'blog.category.edit',
            'blog.category.delete',
            'blog.category.audit',

            // Tags
            'blog.tag.view',
            'blog.tag.create',
            'blog.tag.edit',
            'blog.tag.delete',
            'blog.tag.audit',

            // Posts
            'blog.dashboard.view',
            'blog.post.view',
            'blog.post.create',
            'blog.post.edit.own',
            'blog.post.edit.all',
            'blog.post.delete.own',
            'blog.post.delete.all',
            'blog.post.publish',
            'blog.post.audit',
            'blog.comment.view',
            'blog.comment.status',
            'blog.comment.delete',
        ];

        foreach ($permissions as $key => $value) {
            Permission::create(['name' => $value]);
        }

        $permissions = Permission::all();

        $superadmin = Role::where('name', 'superadmin')->first();

        $superadmin->syncPermissions($permissions);
    }
}

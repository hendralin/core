<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

test('guests are redirected from ai risk assessment', function () {
    $this->get(route('ai-risk.index'))->assertRedirect('/login');
});

test('users without permission cannot access ai risk assessment', function () {
    $user = User::factory()->create(['status' => 1]);

    $this->actingAs($user)
        ->get(route('ai-risk.index'))
        ->assertForbidden();
});

test('users with ai-risk permission can access ai risk assessment', function () {
    Permission::firstOrCreate(['name' => 'ai-risk.view']);

    $user = User::factory()->create(['status' => 1]);
    $user->givePermissionTo('ai-risk.view');

    $this->actingAs($user)
        ->get(route('ai-risk.index'))
        ->assertOk();
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

test('guests are redirected from ai screener', function () {
    $this->get(route('ai-screener.index'))->assertRedirect('/login');
});

test('users without permission cannot access ai screener', function () {
    $user = User::factory()->create(['status' => 1]);

    $this->actingAs($user)
        ->get(route('ai-screener.index'))
        ->assertForbidden();
});

test('users with ai-screener permission can access ai screener', function () {
    Permission::firstOrCreate(['name' => 'ai-screener.view']);

    $user = User::factory()->create(['status' => 1]);
    $user->givePermissionTo('ai-screener.view');

    $this->actingAs($user)
        ->get(route('ai-screener.index'))
        ->assertOk();
});

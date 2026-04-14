<?php

use App\Livewire\Report\Analytics\AnalyticsIndex;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

test('analytics page is forbidden without permission', function () {
    $user = User::factory()->create(['status' => 1]);

    $this->actingAs($user)
        ->get(route('analytics.index'))
        ->assertForbidden();
});

test('analytics livewire component renders with analytics.view permission', function () {
    Permission::firstOrCreate(['name' => 'analytics.view']);

    $user = User::factory()->create(['status' => 1]);
    $user->givePermissionTo('analytics.view');

    Livewire::actingAs($user)
        ->test(AnalyticsIndex::class)
        ->assertSuccessful();
});

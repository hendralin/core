<?php

use App\Models\User;
use App\Services\Valuation\DcfValuationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

test('guests are redirected from ai valuation', function () {
    $this->get(route('ai-valuation.index'))->assertRedirect('/login');
});

test('users without permission cannot access ai valuation', function () {
    $user = User::factory()->create(['status' => 1]);

    $this->actingAs($user)
        ->get(route('ai-valuation.index'))
        ->assertForbidden();
});

test('dcf service returns error when no fundamentals for code', function () {
    $dcf = app(DcfValuationService::class);
    $r = $dcf->buildFullDcfModel('XXXX');

    expect($r)->toHaveKey('error');
});

test('users with ai-valuation permission can access ai valuation', function () {
    Permission::firstOrCreate(['name' => 'ai-valuation.view']);

    $user = User::factory()->create(['status' => 1]);
    $user->givePermissionTo('ai-valuation.view');

    $this->actingAs($user)
        ->get(route('ai-valuation.index'))
        ->assertOk();
});

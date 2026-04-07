<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
});

test('superadmin can start impersonation and session stores impersonator', function () {
    $superadmin = User::factory()->create(['status' => 1]);
    $superadmin->assignRole('superadmin');

    $target = User::factory()->create(['status' => 1]);

    $response = $this->actingAs($superadmin)->post(route('users.impersonate', $target));

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticatedAs($target);
    $this->assertEquals($superadmin->id, session('impersonator_id'));
    $this->assertTrue(session('is_impersonating'));
});

test('non superadmin cannot start impersonation', function () {
    $user = User::factory()->create(['status' => 1]);
    $target = User::factory()->create(['status' => 1]);

    $response = $this->actingAs($user)->post(route('users.impersonate', $target));

    $response->assertForbidden();
    $this->assertAuthenticatedAs($user);
});

test('superadmin cannot impersonate themselves', function () {
    $superadmin = User::factory()->create(['status' => 1]);
    $superadmin->assignRole('superadmin');

    $response = $this->actingAs($superadmin)->post(route('users.impersonate', $superadmin));

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertAuthenticatedAs($superadmin);
    $this->assertNull(session('impersonator_id'));
});

test('superadmin cannot impersonate inactive user', function () {
    $superadmin = User::factory()->create(['status' => 1]);
    $superadmin->assignRole('superadmin');

    $target = User::factory()->create(['status' => 0]);

    $response = $this->actingAs($superadmin)->post(route('users.impersonate', $target));

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertAuthenticatedAs($superadmin);
});

test('leave impersonation restores original account', function () {
    $superadmin = User::factory()->create(['status' => 1]);
    $superadmin->assignRole('superadmin');

    $target = User::factory()->create(['status' => 1]);

    $this->actingAs($superadmin)->post(route('users.impersonate', $target));

    $this->assertAuthenticatedAs($target);

    $response = $this->post(route('impersonate.leave'));

    $response->assertRedirect(route('users.index', absolute: false));
    $this->assertAuthenticatedAs($superadmin);
    $this->assertNull(session('impersonator_id'));
    $this->assertNull(session('is_impersonating'));
});

test('leave when not impersonating redirects to dashboard', function () {
    $user = User::factory()->create(['status' => 1]);

    $response = $this->actingAs($user)->post(route('impersonate.leave'));

    $response->assertRedirect(route('dashboard', absolute: false));
});

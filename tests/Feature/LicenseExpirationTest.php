<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('license expired page is accessible', function () {
    $response = $this->get('/license-expired');

    $response->assertStatus(200);
});

test('authenticated user with expired license is redirected', function () {
    // Create an active test user
    $user = User::factory()->create([
        'status' => 1, // Ensure user is active (1 = active, 0 = inactive)
    ]);

    // Set license to expired
    $company = Company::first();
    if ($company) {
        $company->update([
            'license_expires_at' => now()->subDay(),
            'license_status' => 'expired',
        ]);
    }

    // Act as authenticated user
    $response = $this->actingAs($user)->get('/');

    // Should redirect to license expired page
    $response->assertRedirect('/license-expired');
});

test('authenticated user with active license can access dashboard', function () {
    // Create an active test user
    $user = User::factory()->create([
        'status' => 1, // Ensure user is active (1 = active, 0 = inactive)
    ]);

    // Set license to active
    $company = Company::first();
    if ($company) {
        $company->update([
            'license_expires_at' => now()->addDays(30),
            'license_status' => 'active',
        ]);
    }

    // Act as authenticated user
    $response = $this->actingAs($user)->get('/');

    // Should be able to access (not redirected to license expired)
    $response->assertStatus(200);
});

test('excluded routes are accessible even with expired license', function () {
    // Set license to expired
    $company = Company::first();
    if ($company) {
        $company->update([
            'license_expires_at' => now()->subDay(),
            'license_status' => 'expired',
        ]);
    }

    // Test login page (excluded route)
    $response = $this->get('/login');
    $response->assertStatus(200);

    // Test license expired page (excluded route)
    $response = $this->get('/license-expired');
    $response->assertStatus(200);
});

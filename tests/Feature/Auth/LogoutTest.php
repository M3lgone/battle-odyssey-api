<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('logout user and revoke token', function () {

   $user = User::factory()->create();

    \Laravel\Passport\Passport::actingAs($user, [], 'api');

    $response = $this->postJson('/api/v1/auth/logout');

    $response->assertStatus(200)
             ->assertJson(['message' => 'Logged out successfully']);
});

it('cannot logout without token', function () {
    $response = $this->postJson('/api/v1/auth/logout');
    $response->assertStatus(401);
});

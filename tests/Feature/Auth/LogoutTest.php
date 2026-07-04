<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('logout user and revoke token', function () {

   $user = User::factory()->create();

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/auth/logout');

    $response->assertStatus(200)
             ->assertJson(['message' => 'Logged out successfully']);

    $this->assertEquals(0, $user->tokens()->where('revoked', false)->count());
});

it('cannot logout without token', function () {
    $response = $this->postJson('/api/v1/auth/logout');
    $response->assertStatus(401);
});

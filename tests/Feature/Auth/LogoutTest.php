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

    $token = $user->createToken('api-token')->accessToken;

    $this->assertEquals(1, $user->tokens()->where('revoked', false)->count());

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->postJson('/api/v1/logout');

    $response->assertStatus(200)
             ->assertJson(['message' => 'Logged out successfully']);

    $this->assertEquals(0, $user->tokens()->where('revoked', false)->count());
});

it('cannot logout without token', function () {

    $response = $this->postJson('/api/v1/logout');
    
    $response->assertStatus(401);
});

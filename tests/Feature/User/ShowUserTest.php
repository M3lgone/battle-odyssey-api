<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('can view own profile', function () {

    $user = User::factory()->create();

    Passport::actingAs($user);

    $response = $this->getJson('/api/v1/me');

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'id' => $user->id,
                 'name' => $user->name,
                 'email' => $user->email,
                 'role' => $user->role,
             ])
             ->assertJsonMissing(['password']);
});

it('cannot view profile without token', function () {

    $user = User::factory()->create();

    $response = $this->getJson('/api/v1/me');

    $response->assertStatus(401);
});

it('user does not exist return error', function () {

    $user = User::factory()->create();

    Passport::actingAs($user);

    $response = $this->getJson('/api/v1/users/9999');

    $response->assertStatus(404);
});

it('unauthenticated user cannot view profile', function () {

    $response = $this->getJson('/api/v1/me');

    $response->assertStatus(401);
});
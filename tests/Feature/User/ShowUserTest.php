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

    $response = $this->getJson('/api/v1/users/' . $user->id);

    $response->assertStatus(200)
             ->assertJsonFragment([
                'email' => $user->email,
                'name' => $user->name,
             ]);
});

it('cannot view profile without token', function () {
    $user = User::factory()->create();

    $response = $this->getJson('/api/v1/users/' . $user->id);

    $response->assertStatus(401);
});

it('user does not exist', function () {
    $user = User::factory()->create();

    Passport::actingAs($user);

    $response = $this->getJson('/api/v1/users/9999');

    $response->assertStatus(404);
});
<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('can update own profile user', function () {

    $user = User::factory()->create();

    Passport::actingAs($user);

    $response = $this->putJson('/api/v1/me', [
        'name' => 'Updated name',
    ]);

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => 'Updated name']);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated name',
    ]);
});

it('fails if email is already taken', function () {

    $userA = User::factory()->create(['email' => 'isma@gmail.com']);

    $userB = User::factory()->create(['email' => 'alex@gmail.com']);

    Passport::actingAs($userA);

    $response = $this->putJson('/api/v1/me', [
        'email' => 'alex@gmail.com',
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email']);
});

it('fails if passwords do not match', function () {
    
    $user = User::factory()->create();

    Passport::actingAs($user);

    $response = $this->putJson('/api/v1/me', [
        'password' => 'password123',
        'password_confirmation' => 'bad12345',
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['password']);
});

it('unauthenticated user cannot update profile', function () {

    $response = $this->putJson('/api/v1/me', [
        'name' => 'Updated name',
    ]);

    $response->assertStatus(401);
});
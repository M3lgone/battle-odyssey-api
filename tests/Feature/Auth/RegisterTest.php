<?php

use App\Models\User;

it('registers a user', function () {

    $data = [
        'name' => 'Ismael',
        'email' => 'isma@gmail.com',
        'password' => 'password4',
        'password_confirmation' => 'password4',
    ];

    $response = $this->postJson('/api/v1/auth/register', $data);

    $response->assertCreated();

    $this->assertDatabaseHas('users', [
        'email' => 'isma@gmail.com',
        'role' => 'player',
    ]);
});

it('fails validation if email is already taken', function () {

    $user = User::factory()->create([
        'email' => 'isma@gmail.com'
    ]);

    $data = [
        'name' => 'Max',
        'email' => 'isma@gmail.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->postJson('/api/v1/auth/register', $data);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email']);
});

it('fails validation if password is too short', function () {
    $data = [
        'name' => 'Alex',
        'email' => 'alex@gmail.com',
        'password' => '12345', 
        'password_confirmation' => '12345', 
    ];

    $response = $this->postJson('/api/v1/auth/register', $data);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['password']);
});


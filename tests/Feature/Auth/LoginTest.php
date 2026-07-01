<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('user login and return bearer token', function () {

    $user = User::factory()->create([
        'password' => Hash::make('password4'),
    ]);

    $data = [
        'email' => $user->email,
        'password' => 'password4'
    ];

    $response = $this->postJson('/api/v1/auth/login', $data);

    $response->assertStatus(200);
    $response->assertJsonStructure(['token']);
});

it('fails to login with incorrect password', function () {

    $user = User::factory()->create([
        'password' => Hash::make('password4'),
    ]);

    $data = [
        'email' => $user->email,
        'password' => 'false_password' 
    ];

    $response = $this->postJson('/api/v1/auth/login', $data);

    $response->assertStatus(401)
             ->assertJson(['message' => 'Invalid credentials']);
});

it('fails validation if email or password are missing', function () {

    $response = $this->postJson('/api/v1/auth/login', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email', 'password']);
});

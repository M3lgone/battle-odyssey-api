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

    $response->assertOk();
    $response->assertJsonStructure(['token']);
});

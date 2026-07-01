<?php

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



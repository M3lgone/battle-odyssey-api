<?php

use App\Models\User;
use Laravel\Passport\Passport;

it('can delete own profile', function () {

    $user = User::factory()->create();

    Passport::actingAs($user);

    $response = $this->deleteJson('/api/v1/me');

    $response->assertStatus(200);

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

it('unauthenticated user cannot delete profile', function () {
    $user = User::factory()->create();

    $response = $this->deleteJson('/api/v1/me');

    $response->assertStatus(401);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);
});

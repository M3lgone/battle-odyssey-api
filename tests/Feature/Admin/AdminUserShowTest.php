<?php

use App\Models\User;
use Laravel\Passport\Passport;

it('admin can view user details', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    $user = User::factory()->create();

    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/users/' . $user->id);

    $response->assertStatus(200)
             ->assertJsonFragment([
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
             ]);
});

it('admin cannot see user password', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    $user = User::factory()->create();

    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/users/' . $user->id);

    $response->assertStatus(200)
             ->assertJsonMissing(['password']);
});

it('returns 404 if user does not exist', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/users/99999');

    $response->assertStatus(404);
});

it('player cannot view other user details', function () {

    $player = User::factory()->create(['role' => 'player']);

    $otherUser = User::factory()->create();

    Passport::actingAs($player);

    $response = $this->getJson('/api/v1/users/' . $otherUser->id);

    $response->assertStatus(403);
});

it('unauthenticated user cannot view user details', function () {
    
    $user = User::factory()->create();

    $response = $this->getJson('/api/v1/users/' . $user->id);

    $response->assertStatus(401);
});
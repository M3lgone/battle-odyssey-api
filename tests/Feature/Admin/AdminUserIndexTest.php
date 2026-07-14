<?php

use App\Models\User;
use Laravel\Passport\Passport;

it('admin can list all users', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    User::factory()->count(3)->create();

    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/users');

    $response->assertStatus(200)
             ->assertJsonCount(4)
             ->assertJsonStructure([['id', 'name', 'email', 'role']]);
});

it('player cannot list users', function () {
    
    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $response = $this->getJson('/api/v1/users');

    $response->assertStatus(403);
});

it('returns empty list when no users exist', function () {
    
    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/users');

    $response->assertStatus(200)
             ->assertJsonCount(1);
});

it('unauthenticated user cannot list users', function () {

    $response = $this->getJson('/api/v1/users');

    $response->assertStatus(401);
});
<?php

use App\Models\User;
use Laravel\Passport\Passport;

it('admin can delete any user', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    $user = User::factory()->create();

    Passport::actingAs($admin);

    $response = $this->deleteJson('/api/v1/users/' . $user->id);

    $response->assertStatus(200)
             ->assertJson(['message' => 'User deleted successfully']);

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('admin cannot delete themselves', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $response = $this->deleteJson('/api/v1/users/' . $admin->id);

    $response->assertStatus(403);

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

it('player cannot delete a user', function () {
    
    $player = User::factory()->create(['role' => 'player']);

    $userToDelete = User::factory()->create();

    Passport::actingAs($player);

    $response = $this->deleteJson('/api/v1/users/' . $userToDelete->id);

    $response->assertStatus(403);
    
    $this->assertDatabaseHas('users', ['id' => $userToDelete->id]);
});
<?php

use App\Models\User;
use App\Models\Character;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('admin can delete an existing character', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $character = Character::factory()->create();

    $response = $this->deleteJson('/api/v1/characters/' . $character->id);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Character deleted successfully']);

    $this->assertDatabaseMissing('characters', [
        'id' => $character->id,
    ]);
});

it('player cannot delete a character', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $character = Character::factory()->create();

    $response = $this->deleteJson('/api/v1/characters/' . $character->id);

    $response->assertStatus(403);
    
    $this->assertDatabaseHas('characters', [
        'id' => $character->id,
    ]);
});

it('unauthenticated user cannot delete a character', function () {

    $character = Character::factory()->create();

    $response = $this->deleteJson('/api/v1/characters/' . $character->id);

    $response->assertStatus(401);
    
    $this->assertDatabaseHas('characters', [
        'id' => $character->id,
    ]);
});

it('returns 404 if admin tries to delete a non existent character', function () {

    $admin = User::factory()->create(['role' => 'admin']);
    
    Passport::actingAs($admin);

    $response = $this->deleteJson('/api/v1/characters/99999');

    $response->assertStatus(404);
});

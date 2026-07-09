<?php

use App\Models\User;
use App\Models\Character;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('admin can update an existing character', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Passport::actingAs($admin);

    $character = Character::factory()->create([
        'class' => 'Archer',
        'attack' => 25,
    ]);

    $response = $this->putJson('/api/v1/characters/' . $character->id, [
        'attack' => 99, 
    ]);

    $response->assertStatus(200)
             ->assertJsonFragment(['attack' => 99]);

    $this->assertDatabaseHas('characters', [
        'id' => $character->id,
        'attack' => 99,
    ]);
});

it('player cannot update a character', function () {
    $player = User::factory()->create(['role' => 'player']);
    Passport::actingAs($player);

    $character = Character::factory()->create();

    $response = $this->putJson('/api/v1/characters/' . $character->id, [
        'attack' => 99,
    ]);

    $response->assertStatus(403);
});

it('unauthenticated user cannot update a character', function () {
    $character = Character::factory()->create();

    $response = $this->putJson('/api/v1/characters/' . $character->id, [
        'attack' => 99,
    ]);

    $response->assertStatus(401);
});

<?php

use App\Models\User;
use App\Models\Character;
use App\Models\Game;
use App\Models\Battle;
use App\Models\Skill;
use App\Models\Enemy;
use Laravel\Passport\Passport;

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

it('admin can delete a character with related battles, games and skills', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $character = Character::factory()->create();
    $skills = Skill::factory()->count(2)->create();
    $character->skills()->attach($skills);

    $game = Game::factory()->create([
        'character_id' => $character->id,
    ]);

    $battle = Battle::factory()->create([
        'character_id' => $character->id,
        'game_id' => $game->id,
    ]);

    $enemy = Enemy::factory()->create();
    $battle->enemies()->attach($enemy, [
        'current_hp' => 50,
        'current_mp' => 20,
    ]);

    $response = $this->deleteJson('/api/v1/characters/' . $character->id);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Character deleted successfully']);

    $this->assertDatabaseMissing('characters', [
        'id' => $character->id,
    ]);

    $this->assertDatabaseMissing('battles', [
        'id' => $battle->id,
    ]);

    $this->assertDatabaseMissing('battle_has_enemy', [
        'battle_id' => $battle->id,
    ]);

    $this->assertDatabaseMissing('games', [
        'id' => $game->id,
    ]);

    $this->assertDatabaseMissing('character_has_skill', [
        'character_id' => $character->id,
    ]);

    $this->assertDatabaseHas('skills', [
        'id' => $skills->first()->id,
    ]);

    $this->assertDatabaseHas('enemies', [
        'id' => $enemy->id,
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

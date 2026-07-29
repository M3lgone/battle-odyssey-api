<?php

use App\Models\User;
use App\Models\Game;
use App\Models\Battle;
use App\Models\Character;
use App\Models\Enemy;
use App\Models\Skill;
use Laravel\Passport\Passport;

it('can get the battle history of a game', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    $character = Character::create([
        'class' => 'Warrior',
        'attack' => 15,
        'defense' => 20,
        'max_health_points' => 120,
        'max_magic_points' => 100,
        'character_image_url' => 'warrior.png',
    ]);

    $game = Game::create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);

    $enemy = Enemy::create([
        'enemy_name' => 'Goblin',
        'max_health_points' => 80,
        'max_magic_points' => 30,
        'attack' => 10,
        'defense' => 5,
        'enemy_image_url' => 'goblin.png',
        'background_image_url' => 'bg-goblin.png',
    ]);

    $characterSkill = Skill::factory()->create(['skill_name' => 'Slash']);
    $character->skills()->attach($characterSkill->id);

    $enemySkill = Skill::factory()->create(['skill_name' => 'Hack']);
    $enemy->skills()->attach($enemySkill->id);

    $battle1 = Battle::create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'win',
        'character_current_hp' => 50,
        'character_current_mp' => 10,
        'total_damage_dealt' => 80,
        'total_damage_received' => 70,
    ]);
    $battle1->enemies()->attach($enemy->id, [
        'current_hp' => 0,
        'current_mp' => 30,
    ]);

    $battle2 = Battle::create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'ongoing',
        'character_current_hp' => 120,
        'character_current_mp' => 100,
        'total_damage_dealt' => 0,
        'total_damage_received' => 0,
    ]);
    $battle2->enemies()->attach($enemy->id, [
        'current_hp' => 80,
        'current_mp' => 30,
    ]);

    $response = $this->getJson("/api/v1/games/{$game->id}/battles");

    $response->assertStatus(200)
             ->assertJsonFragment(['result' => 'win'])
             ->assertJsonFragment(['result' => 'ongoing'])
             ->assertJsonFragment(['class' => 'Warrior'])
             ->assertJsonFragment(['enemy_name' => 'Goblin'])
             ->assertJsonFragment(['skill_name' => 'Slash'])
             ->assertJsonFragment(['skill_name' => 'Hack']);
              
    expect($response->json('battles'))->toHaveCount(2);
});

it('cannot get the battle history of another players game', function () {

    $playerOne = User::factory()->create(['role' => 'player']);
    $playerTwo = User::factory()->create(['role' => 'player']);
    
    Passport::actingAs($playerOne);

    $gameTwo = Game::create([
        'user_id' => $playerTwo->id,
        'character_id' => Character::factory()->create()->id,
        'status' => 'active',
    ]);

    $response = $this->getJson("/api/v1/games/{$gameTwo->id}/battles");

    $response->assertStatus(403)
             ->assertJsonFragment(['error' => 'Unauthorized. This game does not belong to you.']);
});

it('returns 404 if the game does not exist when getting battles', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    $response = $this->getJson("/api/v1/games/99999/battles");

    $response->assertStatus(404);
});

<?php

use App\Models\User;
use App\Models\Game;
use App\Models\Battle;
use App\Models\Character;
use App\Models\Enemy;
use App\Models\Skill;
use Laravel\Passport\Passport;

it('a player can view their own battle state', function () {

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

    $battle = Battle::create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'ongoing',
        'character_current_hp' => $character->max_health_points,
        'character_current_mp' => $character->max_magic_points,
        'enemy_current_hp' => $enemy->max_health_points,
        'enemy_current_mp' => $enemy->max_magic_points,
        'total_damage_dealt' => 0,
        'total_damage_received' => 0,
    ]);
    
    $battle->enemies()->attach($enemy->id);

    $response = $this->getJson("/api/v1/battles/{$battle->id}");

    $response->assertStatus(200)
             ->assertJsonFragment(['result' => 'ongoing'])
             ->assertJsonFragment(['class' => 'Warrior'])
             ->assertJsonFragment(['enemy_name' => 'Goblin'])
             ->assertJsonFragment(['skill_name' => 'Slash'])
             ->assertJsonFragment(['skill_name' => 'Hack']);
});

it('a player cannot view another players battle', function () {

    $playerOne = User::factory()->create(['role' => 'player']);

    $playerTwo = User::factory()->create(['role' => 'player']);
    
    Passport::actingAs($playerOne);

    $character = Character::create([
        'class' => 'Mage',
        'attack' => 10,
        'defense' => 15,
        'max_health_points' => 100,
        'max_magic_points' => 120,
        'character_image_url' => 'mage.png',
    ]);

    $game = Game::create([
        'user_id' => $playerTwo->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);
    
    $battle = Battle::create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'ongoing',
        'character_current_hp' => 100,
        'character_current_mp' => 50,
        'enemy_current_hp' => 80,
        'enemy_current_mp' => 30,
        'total_damage_dealt' => 0,
        'total_damage_received' => 0,
    ]);

    $response = $this->getJson("/api/v1/battles/{$battle->id}");

    $response->assertStatus(403);
});

it('prevents an unauthenticated user from viewing any battle', function () {

    $response = $this->getJson("/api/v1/battles/1");

    $response->assertStatus(401);
});

it('returns 404 if the battle does not exist', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    $response = $this->getJson("/api/v1/battles/99999");

    $response->assertStatus(404);
});

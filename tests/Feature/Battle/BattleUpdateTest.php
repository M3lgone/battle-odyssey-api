<?php

use App\Models\User;
use App\Models\Game;
use App\Models\Battle;
use App\Models\Character;
use App\Models\Enemy;
use Laravel\Passport\Passport;

function createOngoingBattle(User $user, string $gameStatus = 'active'): array
{
    $character = Character::factory()->create();

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => $gameStatus,
    ]);

    $enemy = Enemy::factory()->create([
        'max_health_points' => 80,
        'max_magic_points' => 30,
    ]);

    $battle = Battle::factory()->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'ongoing',
    ]);

    $battle->enemies()->attach($enemy->id, [
        'current_hp' => 80,
        'current_mp' => 30,
    ]);

    return [$game, $battle, $enemy];
}

function battleResultPayload(Enemy $enemy): array
{
    return [
        'result' => 'win',
        'character_current_hp' => 45,
        'character_current_mp' => 30,
        'total_damage_dealt' => 80,
        'total_damage_received' => 75,
        'enemies' => [
            ['id' => $enemy->id, 'current_hp' => 0, 'current_mp' => 20],
        ],
    ];
}

it('persists the battle outcome reported by the client', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    [$game, $battle, $enemy] = createOngoingBattle($user);

    Enemy::factory()->create(['enemy_name' => 'Troll']);

    $response = $this->putJson("/api/v1/battles/{$battle->id}", battleResultPayload($enemy));

    $response->assertStatus(200)
             ->assertJsonFragment(['result' => 'win'])
             ->assertJsonFragment(['game_status' => 'active']);

    $this->assertDatabaseHas('battles', [
        'id' => $battle->id,
        'result' => 'win',
        'character_current_hp' => 45,
        'character_current_mp' => 30,
        'total_damage_dealt' => 80,
        'total_damage_received' => 75,
    ]);

    $this->assertDatabaseHas('battle_has_enemy', [
        'battle_id' => $battle->id,
        'enemy_id' => $enemy->id,
        'current_hp' => 0,
        'current_mp' => 20,
    ]);
});

it('keeps the game active when enemies remain after a win', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    [$game, $battle, $enemy] = createOngoingBattle($user);

    Enemy::factory()->create(['enemy_name' => 'Troll']);

    $response = $this->putJson("/api/v1/battles/{$battle->id}", battleResultPayload($enemy));

    $response->assertStatus(200)
             ->assertJsonFragment(['game_status' => 'active']);

    $this->assertDatabaseHas('games', [
        'id' => $game->id,
        'status' => 'active',
    ]);
});

it('finishes the game when the final enemy is defeated', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    [$game, $battle, $enemy] = createOngoingBattle($user);

    $response = $this->putJson("/api/v1/battles/{$battle->id}", battleResultPayload($enemy));

    $response->assertStatus(200)
             ->assertJsonFragment(['game_status' => 'finished']);

    $this->assertDatabaseHas('games', [
        'id' => $game->id,
        'status' => 'finished',
    ]);
});

it('finishes the game when the battle is lost', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    [$game, $battle, $enemy] = createOngoingBattle($user);

    Enemy::factory()->create(['enemy_name' => 'Troll']);

    $payload = battleResultPayload($enemy);
    $payload['result'] = 'loss';
    $payload['character_current_hp'] = 0;
    $payload['enemies'][0]['current_hp'] = 50;

    $response = $this->putJson("/api/v1/battles/{$battle->id}", $payload);

    $response->assertStatus(200)
             ->assertJsonFragment(['result' => 'loss'])
             ->assertJsonFragment(['game_status' => 'finished']);

    $this->assertDatabaseHas('games', [
        'id' => $game->id,
        'status' => 'finished',
    ]);
});

it('finishes the game when the player flees', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    [$game, $battle, $enemy] = createOngoingBattle($user);

    Enemy::factory()->create(['enemy_name' => 'Troll']);

    $payload = battleResultPayload($enemy);
    $payload['result'] = 'flee';

    $response = $this->putJson("/api/v1/battles/{$battle->id}", $payload);

    $response->assertStatus(200)
             ->assertJsonFragment(['result' => 'flee'])
             ->assertJsonFragment(['game_status' => 'finished']);

    $this->assertDatabaseHas('games', [
        'id' => $game->id,
        'status' => 'finished',
    ]);
});

it('prevents updating a battle that has already been resolved', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    [$game, $battle, $enemy] = createOngoingBattle($user);

    $battle->update(['result' => 'win']);

    $response = $this->putJson("/api/v1/battles/{$battle->id}", battleResultPayload($enemy));

    $response->assertStatus(400)
             ->assertJsonFragment([
                 'message' => 'This battle has already been resolved.'
             ]);
});

it('prevents updating another players battle', function () {

    $playerOne = User::factory()->create(['role' => 'player']);
    $playerTwo = User::factory()->create(['role' => 'player']);

    [$game, $battle, $enemy] = createOngoingBattle($playerTwo);

    Passport::actingAs($playerOne);

    $response = $this->putJson("/api/v1/battles/{$battle->id}", battleResultPayload($enemy));

    $response->assertStatus(403);
});

it('validates the battle outcome payload', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    [$game, $battle, $enemy] = createOngoingBattle($user);

    $response = $this->putJson("/api/v1/battles/{$battle->id}", []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors([
                 'result',
                 'character_current_hp',
                 'character_current_mp',
                 'total_damage_dealt',
                 'total_damage_received',
                 'enemies',
             ]);
});

it('rejects enemies that do not belong to the battle', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    [$game, $battle, $enemy] = createOngoingBattle($user);

    $stranger = Enemy::factory()->create(['enemy_name' => 'Stranger']);

    $payload = battleResultPayload($enemy);
    $payload['enemies'][0]['id'] = $stranger->id;

    $response = $this->putJson("/api/v1/battles/{$battle->id}", $payload);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['enemies.0.id']);
});

it('prevents an unauthenticated user from updating a battle', function () {

    $user = User::factory()->create(['role' => 'player']);

    [$game, $battle, $enemy] = createOngoingBattle($user);

    $response = $this->putJson("/api/v1/battles/{$battle->id}", battleResultPayload($enemy));

    $response->assertStatus(401);
});

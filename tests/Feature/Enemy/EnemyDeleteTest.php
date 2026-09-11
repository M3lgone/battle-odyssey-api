<?php

use App\Models\User;
use App\Models\Enemy;
use App\Models\Skill;
use App\Models\Battle;
use Laravel\Passport\Passport;

it('admin can delete an existing enemy', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $enemy = Enemy::factory()->create();


    $response = $this->deleteJson('/api/v1/enemies/' . $enemy->id);

    $response->assertStatus(200);
    $this->assertDatabaseMissing('enemies', [
        'id' => $enemy->id,
    ]);
});

it('admin can delete an enemy with related skills and battles', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $enemy = Enemy::factory()->create();
    $skill = Skill::factory()->create();
    $enemy->skills()->attach($skill);

    $battle = Battle::factory()->create();
    $battle->enemies()->attach($enemy, [
        'current_hp' => 50,
        'current_mp' => 20,
    ]);

    $response = $this->deleteJson('/api/v1/enemies/' . $enemy->id);

    $response->assertStatus(200);

    $this->assertDatabaseMissing('enemies', [
        'id' => $enemy->id,
    ]);

    $this->assertDatabaseMissing('enemy_has_skill', [
        'enemy_id' => $enemy->id,
    ]);

    $this->assertDatabaseMissing('battle_has_enemy', [
        'enemy_id' => $enemy->id,
    ]);

    $this->assertDatabaseHas('skills', [
        'id' => $skill->id,
    ]);

    $this->assertDatabaseHas('battles', [
        'id' => $battle->id,
    ]);
});

it('player cannot delete an enemy', function () {

    $player = User::factory()->create(['role' => 'player'])
    ;
    Passport::actingAs($player);

    $enemy = Enemy::factory()->create();

    $response = $this->deleteJson('/api/v1/enemies/' . $enemy->id);

    $response->assertStatus(403);
    $this->assertDatabaseHas('enemies', [
        'id' => $enemy->id,
    ]);
});

it('returns 404 if enemy to delete does not exist', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $response = $this->deleteJson('/api/v1/enemies/99999');

    $response->assertStatus(404);
});

it('fails to delete if user is unauthenticated', function () {
    
    $enemy = Enemy::factory()->create();

    $response = $this->deleteJson('/api/v1/enemies/' . $enemy->id);

    $response->assertStatus(401);
    
    $this->assertDatabaseHas('enemies', [
        'id' => $enemy->id,
    ]);
});

<?php

use App\Models\User;
use App\Models\Enemy;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

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

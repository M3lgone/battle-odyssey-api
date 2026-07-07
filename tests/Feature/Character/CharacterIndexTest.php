<?php

use App\Models\User;
use App\Models\Character;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('player can list all characters', function () {

    $user = User::factory()->create();

    Passport::actingAs($user);

    Artisan::call('db:seed', ['--class' => 'CharacterSeeder']);

    $response = $this->getJson('/api/v1/characters');

    $response->assertStatus(200)
             ->assertJsonStructure([['id', 'class', 'attack', 'defense', 'max_health_points', 'max_magic_points']]);
});

it('returns correct number of characters', function () {
    $user = User::factory()->create();

    Passport::actingAs($user);

    Artisan::call('db:seed', ['--class' => 'CharacterSeeder']);

    $this->assertDatabaseCount('characters', 3);

    $response = $this->getJson('/api/v1/characters');

    $response->assertStatus(200)
             ->assertJsonCount(3);
});

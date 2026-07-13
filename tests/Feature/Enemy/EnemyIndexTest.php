<?php

use App\Models\Enemy;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('admin can list all enemies', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Enemy::factory()->count(3)->create();

    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/enemies');

    $response->assertStatus(200)
             ->assertJsonCount(3);
});

it('player cannot list enemies', function () {

    $player = User::factory()->create(['role' => 'player']);

    Enemy::factory()->count(3)->create();

    Passport::actingAs($player);

    $response = $this->getJson('/api/v1/enemies');

    $response->assertStatus(403);
});

it('unauthenticated user cannot list enemies', function () {

    Enemy::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/enemies');

    $response->assertStatus(401);
});

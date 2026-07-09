<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('admin can list all users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->count(3)->create();

    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/users');

    $response->assertOk()
             ->assertJsonStructure([['id', 'name', 'email', 'role']]);
});

it('player cannot list users', function () {
    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $response = $this->getJson('/api/v1/users');

    $response->assertStatus(403);
});

it('returns empty list when no users exist', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/users');

    $response->assertStatus(200)
             ->assertJson([]);
});
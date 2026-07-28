<?php

use App\Models\User;
use App\Models\Skill;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('admin can list all skills', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    Skill::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/skills');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 '*' => ['id', 'skill_name', 'description', 'damage_skill', 'skill_cost_magic_points']
             ]);
});

it('player can list all skills', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    Skill::factory()->count(2)->create();

    $response = $this->getJson('/api/v1/skills');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 '*' => ['id', 'skill_name', 'description', 'damage_skill', 'skill_cost_magic_points']
             ]);
});

it('fails to list skills if user is unauthenticated', function () {

    Skill::factory()->count(2)->create();

    $response = $this->getJson('/api/v1/skills');

    $response->assertStatus(401);
});

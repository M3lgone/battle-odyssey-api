<?php

use App\Models\User;
use App\Models\Skill;
use Laravel\Passport\Passport;

it('player can view skill details', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $skill = Skill::factory()->create();

    $response = $this->getJson('/api/v1/skills/' . $skill->id);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'id', 'skill_name', 'description', 'damage_skill', 'skill_cost_magic_points'
             ]);
});

it('admin can view skill details', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $skill = Skill::factory()->create();

    $response = $this->getJson('/api/v1/skills/' . $skill->id);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'id', 'skill_name', 'description', 'damage_skill', 'skill_cost_magic_points'
             ]);
});

it('returns 404 if skill does not exist', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $response = $this->getJson('/api/v1/skills/99999');

    $response->assertStatus(404);
});

it('fails to view skill details if user is unauthenticated', function () {

    $skill = Skill::factory()->create();

    $response = $this->getJson('/api/v1/skills/' . $skill->id);

    $response->assertStatus(401);
});

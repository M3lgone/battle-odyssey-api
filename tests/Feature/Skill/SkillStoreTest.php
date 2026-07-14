<?php

use App\Models\User;
use App\Models\Skill;
use Laravel\Passport\Passport;

it('admin can create a new skill', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $data = [
        'skill_name' => 'Meteor Strike',
        'description' => 'A devastating fiery boulder from the sky.',
        'damage_skill' => 100,
        'skill_cost_magic_points' => 50,
    ];

    $response = $this->postJson('/api/v1/skills', $data);

    $response->assertStatus(201)
             ->assertJsonFragment(['skill_name' => 'Meteor Strike']);

    $this->assertDatabaseHas('skills', [
        'skill_name' => 'Meteor Strike',
        'damage_skill' => 100,
    ]);
});

it('player cannot create a new skill', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $data = [
        'skill_name' => 'Ice Blast',
        'description' => 'Freezes the enemy.',
        'damage_skill' => 80,
        'skill_cost_magic_points' => 15,
    ];

    $response = $this->postJson('/api/v1/skills', $data);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('skills', ['skill_name' => 'Ice Blast']);
});

it('fails to create a skill if user is unauthenticated', function () {

    $data = [
        'skill_name' => 'Poison Dart',
        'description' => 'Deals damage over time.',
        'damage_skill' => 30,
        'skill_cost_magic_points' => 10,
    ];

    $response = $this->postJson('/api/v1/skills', $data);

    $response->assertStatus(401);
});

it('validates required fields when creating a skill', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $response = $this->postJson('/api/v1/skills', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors([
                 'skill_name', 
                 'description', 
                 'damage_skill', 
                 'skill_cost_magic_points'
             ]);
});
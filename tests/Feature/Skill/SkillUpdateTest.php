<?php

use App\Models\User;
use App\Models\Skill;
use Laravel\Passport\Passport;

it('admin can update an existing skill', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $skill = Skill::factory()->create([
        'skill_name' => 'Fireball',
        'damage_skill' => 50,
    ]);

    $data = [
        'skill_name' => 'Super Fireball',
        'damage_skill' => 150,
    ];

    $response = $this->putJson('/api/v1/skills/' . $skill->id, $data);

    $response->assertStatus(200)
             ->assertJsonFragment(['skill_name' => 'Super Fireball']);

    $this->assertDatabaseHas('skills', [
        'id' => $skill->id,
        'skill_name' => 'Super Fireball',
        'damage_skill' => 150,
    ]);
});

it('player cannot update a skill', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $skill = Skill::factory()->create([
        'skill_name' => 'Heal',
    ]);

    $data = [
        'skill_name' => 'Updated Heal',
    ];

    $response = $this->putJson('/api/v1/skills/' . $skill->id, $data);

    $response->assertStatus(403);
    $this->assertDatabaseHas('skills', [
        'id' => $skill->id,
        'skill_name' => 'Heal',
    ]);
});

it('returns 404 if skill to update does not exist', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $data = [
        'skill_name' => 'Ghost Skill',
    ];

    $response = $this->putJson('/api/v1/skills/99999', $data);

    $response->assertStatus(404);
});

it('fails to update if user is unauthenticated', function () {

    $skill = Skill::factory()->create();

    $response = $this->putJson('/api/v1/skills/' . $skill->id, [
        'skill_name' => 'Hacked Skill'
    ]);

    $response->assertStatus(401);
});

it('validates data when updating a skill', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $skill = Skill::factory()->create();

    $data = [
        'damage_skill' => -50,
    ];

    $response = $this->putJson('/api/v1/skills/' . $skill->id, $data);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['damage_skill']);
});

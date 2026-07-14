<?php

use App\Models\User;
use App\Models\Skill;
use Laravel\Passport\Passport;

it('admin can delete an existing skill', function () {

    $admin = User::factory()->create(['role' => 'admin']);
    
    Passport::actingAs($admin);

    $skill = Skill::factory()->create();

    $response = $this->deleteJson('/api/v1/skills/' . $skill->id);

    $response->assertStatus(200);
    $this->assertDatabaseMissing('skills', [
        'id' => $skill->id,
    ]);
});

it('player cannot delete a skill', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $skill = Skill::factory()->create();

    $response = $this->deleteJson('/api/v1/skills/' . $skill->id);

    $response->assertStatus(403);
    $this->assertDatabaseHas('skills', [
        'id' => $skill->id,
    ]);
});

it('returns 404 if skill to delete does not exist', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $response = $this->deleteJson('/api/v1/skills/99999');

    $response->assertStatus(404);
});

it('fails to delete if user is unauthenticated', function () {

    $skill = Skill::factory()->create();

    $response = $this->deleteJson('/api/v1/skills/' . $skill->id);

    $response->assertStatus(401);
    
    $this->assertDatabaseHas('skills', [
        'id' => $skill->id,
    ]);
});

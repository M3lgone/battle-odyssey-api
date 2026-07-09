<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('admin can view user details', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/users/' . $user->id);

    $response->assertStatus(200)
             ->assertJsonFragment([
                'email' => $user->email,
                'name' => $user->name,
             ]);
});

it('admin cannot see user password', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/users/' . $user->id);

    $response->assertStatus(200)
             ->assertJsonMissing(['password']);
});
<?php

use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Hash;

it('admin can update any user', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    $user = User::factory()->create();

    Passport::actingAs($admin);

    $response = $this->putJson('/api/v1/users/' . $user->id, [
        'name' => 'Updated name',
    ]);

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => 'Updated name']);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated name',
    ]);
});

it('admin can update user password', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    $user = User::factory()->create();

    Passport::actingAs($admin);

    $response = $this->putJson('/api/v1/users/' . $user->id, [
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertStatus(200);

    $this->assertTrue(
        Hash::check('newpassword123', $user->fresh()->password)
    );
});

it('admin can update user role', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($admin);

    $response = $this->putJson('/api/v1/users/' . $user->id, [
        'role' => 'admin',
    ]);

    $response->assertStatus(200)
             ->assertJsonFragment(['role' => 'admin']);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'role' => 'admin',
    ]);
});

it('returns 404 if admin tries to update a non-existent user', function () {

    $admin = User::factory()->create(['role' => 'admin']);
    
    Passport::actingAs($admin);

    $response = $this->putJson('/api/v1/users/99999', [
        'name' => 'Ghost User',
    ]);

    $response->assertStatus(404);
});



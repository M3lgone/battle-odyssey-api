<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('admin can update any user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    Passport::actingAs($admin);

    $response = $this->patchJson('/api/v1/admin/users/' . $user->id, [
        'name' => 'Updated name',
    ]);

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => 'Updated name']);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated name',
    ]);
});



<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('admin can delete any user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    Passport::actingAs($admin);

    $response = $this->deleteJson('/api/v1/admin/users/' . $user->id);

    $response->assertStatus(200)
             ->assertJson(['message' => 'User deleted successfully']);

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

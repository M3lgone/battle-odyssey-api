<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('can delete own profile', function () {
    $user = User::factory()->create();

    Passport::actingAs($user);

    $response = $this->deleteJson('/api/v1/users/' . $user->id);

    $response->assertStatus(200);

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});
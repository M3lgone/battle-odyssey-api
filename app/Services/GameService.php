<?php

namespace App\Services;

use App\Models\Game;
use InvalidArgumentException;

class GameService
{
    public function getActiveGame(int $userId): ?Game
    {
        return Game::where('user_id', $userId)
            ->where('status', 'active')
            ->with('character.skills')
            ->first();
    }

    public function createGame(int $userId, int $characterId): Game
    {
        $activeGameExists = Game::where('user_id', $userId)
            ->where('status', 'active')
            ->exists();

        if ($activeGameExists) {
            throw new InvalidArgumentException('You already have an active game, you must finish it to start another one.');
        }

        $game = Game::create([
            'user_id' => $userId,
            'character_id' => $characterId,
            'status' => 'active',
        ]);

        $game->load('character.skills');

        return $game;
    }
}
